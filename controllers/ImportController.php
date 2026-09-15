<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Graduate.php';

class ImportController extends BaseController {
    public function form($conn) {
        $this->checkAuth();

        $sessionId = $_GET['session_id'] ?? null;
        $pageTitle = 'Import Data Wisudawan';

        $this->render('imports/form', [
            'sessionId' => $sessionId,
            'pageTitle' => $pageTitle
        ]);
    }

    public function process($conn) {
        $this->checkAuth();

        $sessionId = $_POST['session_id'] ?? null;
        $success = 0;
        $failed = [];

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $filePath = $_FILES['file']['tmp_name'];
            $fileContent = file_get_contents($filePath);

            // Bersihkan BOM UTF-8 jika ada
            $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);

            // Deteksi delimiter (titik-koma ;, koma ,, atau tab \t)
            $delimiter = ',';
            if (strpos($fileContent, "sep=;") !== false) {
                $delimiter = ';';
                $fileContent = str_replace("sep=;\n", "", $fileContent);
                $fileContent = str_replace("sep=;\r\n", "", $fileContent);
            } else {
                $firstLine = strtok($fileContent, "\r\n");
                if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                    $delimiter = ';';
                } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                    $delimiter = "\t";
                }
            }

            // Simpan kembali ke stream temporary
            $tempStream = fopen('php://memory', 'r+');
            fwrite($tempStream, $fileContent);
            rewind($tempStream);

            $headerMap = [];
            $rowNum = 0;
            $headerFound = false;

            $conn->begin_transaction();

            try {
                while (($data = fgetcsv($tempStream, 0, $delimiter)) !== false) {
                    $rowNum++;

                    // Bersihkan setiap cell
                    $cleanData = array_map(function($val) {
                        return trim((string)$val);
                    }, $data);

                    // Skip baris kosong
                    if (empty(array_filter($cleanData))) {
                        continue;
                    }

                    // Cari baris header jika belum ketemu
                    if (!$headerFound) {
                        $lowerData = array_map('strtolower', $cleanData);
                        // Cek apakah baris ini berisi header (ada nrp & nama atau program studi)
                        if (in_array('nrp', $lowerData) && (in_array('nama', $lowerData) || in_array('program studi', $lowerData) || in_array('prodi', $lowerData))) {
                            foreach ($lowerData as $idx => $colName) {
                                if (strpos($colName, 'fakultas') !== false) $headerMap['fakultas'] = $idx;
                                elseif (strpos($colName, 'prodi') !== false || strpos($colName, 'program studi') !== false) $headerMap['prodi'] = $idx;
                                elseif (strpos($colName, 'jenjang') !== false) $headerMap['jenjang'] = $idx;
                                elseif (strpos($colName, 'kursi') !== false) $headerMap['kursi'] = $idx;
                                elseif (strpos($colName, 'sisi') !== false) $headerMap['sisi'] = $idx;
                                elseif (strpos($colName, 'nomor') !== false || strpos($colName, 'urut') !== false || strpos($colName, 'no') !== false) $headerMap['nomor'] = $idx;
                                elseif (strpos($colName, 'nrp') !== false) $headerMap['nrp'] = $idx;
                                elseif (strpos($colName, 'nama') !== false) $headerMap['nama'] = $idx;
                            }
                            $headerFound = true;
                            continue;
                        }
                        // Jika belum ada header dan ini baris awal, abaikan dulu sampai ketemu header
                        continue;
                    }

                    // Ambil nilai berdasarkan headerMap
                    $nrp = isset($headerMap['nrp']) && isset($cleanData[$headerMap['nrp']]) ? $cleanData[$headerMap['nrp']] : '';
                    $nama = isset($headerMap['nama']) && isset($cleanData[$headerMap['nama']]) ? $cleanData[$headerMap['nama']] : '';

                    // Jika baris ini tidak punya NRP atau Nama (misal baris header judul prodi S3 - MANAJEMEN TEKNOLOGI / Page 4), skip!
                    if (!$nrp || !$nama || !preg_match('/^[0-9]+$/', $nrp)) {
                        continue;
                    }

                    $fakultasKode = isset($headerMap['fakultas']) && isset($cleanData[$headerMap['fakultas']]) ? $cleanData[$headerMap['fakultas']] : 'FAKULTAS';
                    $prodiRaw = isset($headerMap['prodi']) && isset($cleanData[$headerMap['prodi']]) ? $cleanData[$headerMap['prodi']] : '';
                    $baris = isset($headerMap['kursi']) && isset($cleanData[$headerMap['kursi']]) ? strtoupper($cleanData[$headerMap['kursi']]) : '';
                    $sisiRaw = isset($headerMap['sisi']) && isset($cleanData[$headerMap['sisi']]) ? strtolower($cleanData[$headerMap['sisi']]) : '';
                    $sisi = ($sisiRaw === 'kiri' || $sisiRaw === 'left') ? 'left' : 'right';
                    $nomor = isset($headerMap['nomor']) && isset($cleanData[$headerMap['nomor']]) ? (int)$cleanData[$headerMap['nomor']] : 0;

                    // Parse Jenjang dan Nama Prodi dari $prodiRaw (contoh: "D4-TEK. REK. KIMIA INDUSTRI" atau "S3 - MANAJEMEN TEKNOLOGI")
                    $jenjang = 'S1';
                    $prodiNama = $prodiRaw;

                    if (isset($headerMap['jenjang']) && !empty($cleanData[$headerMap['jenjang']])) {
                        $jenjang = strtoupper($cleanData[$headerMap['jenjang']]);
                    } elseif (preg_match('/^(S3|S2|S1|D4|D3|D2|D1|PROFESI|MAGISTER|DOKTOR)[-\s_.]*(.*)/i', $prodiRaw, $matches)) {
                        $jenjang = strtoupper($matches[1]);
                        $prodiNama = trim($matches[2]);
                    }

                    if (!$prodiNama) {
                        $prodiNama = $prodiRaw ?: 'Umum';
                    }

                    if (!$baris || !$nomor) {
                        $failed[] = "Baris $rowNum ($nama - $nrp): Posisi kursi ($baris/$nomor) tidak valid.";
                        continue;
                    }

                    $facultyRes = Graduate::findOrCreateFaculty($conn, $fakultasKode);
                    $facultyId = $facultyRes['id'];

                    $studyProgramRes = Graduate::findOrCreateStudyProgram($conn, $facultyId, $prodiNama, $jenjang);
                    $studyProgramId = $studyProgramRes['id'];

                    // Catat info pencocokan atau pembuatan prodi baru
                    $prodiKey = $prodiNama . ' (' . $jenjang . ')';
                    if ($studyProgramRes['created']) {
                        $warnings["prodi_created_$prodiKey"] = "Prodi baru dibuat dari Excel: \"$prodiNama\" ($jenjang).";
                    } else {
                        $infoMatch["prodi_match_$prodiKey"] = "Prodi \"$prodiNama\" otomatis dicocokkan ke \"{$studyProgramRes['matched_name']}\" di database.";
                    }

                    $seatId = Graduate::findSeatByPosition($conn, $sessionId, $baris, $sisi, $nomor);
                    if (!$seatId) {
                        $failed[] = "Baris $rowNum ($nama): kursi $baris $sisi $nomor tidak ditemukan di denah.";
                        continue;
                    }

                    if (Graduate::seatIsTaken($conn, $seatId)) {
                        $failed[] = "Baris $rowNum ($nama): kursi $baris $sisi $nomor sudah terisi.";
                        continue;
                    }

                    Graduate::create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $nama, $seatId);
                    $success++;
                }

                $conn->commit();
            } catch (Throwable $e) {
                $conn->rollback();
                $failed[] = "Terjadi kesalahan sistem saat impor: " . $e->getMessage();
            }

            fclose($tempStream);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['import_success'] = $success;
        $_SESSION['import_failed'] = $failed;
        $_SESSION['import_warnings'] = array_values($warnings ?? []);
        $_SESSION['import_info'] = array_values($infoMatch ?? []);

        $this->redirect("/graduates?session_id=$sessionId&success=" . urlencode("$success data berhasil diimport."));
    }

    public function downloadTemplate($conn) {
        $this->checkAuth();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=template_import_wisudawan.csv');

        // Output BOM UTF-8 agar karakter & accents di Excel terbaca sempurna
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        
        // instruksi khusus Excel agar langsung memisah kolom berdasarkan titik koma (;)
        fwrite($output, "sep=;\n");

        // Header CSV dengan delimiter titik koma (;)
        fputcsv($output, ['FAKULTAS', 'PROGRAM STUDI', 'KURSI', 'SISI', 'NOMOR', 'NRP', 'NAMA'], ';');


        fclose($output);
        exit;
    }
}
