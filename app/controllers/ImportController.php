<?php

class ImportController extends BaseController {
    // proses pembacaan dan penyimpan data csv
    public function process($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $sessionId = $_POST['session_id'] ?? null;
        $success = 0;
        $skipped = 0;
        $failed = [];
        $warnings = [];

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $filePath = $_FILES['file']['tmp_name'];
            $fileContent = file_get_contents($filePath);
            $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);

            $delimiter = ',';
            if (strpos($fileContent, "sep=;") !== false) {
                $delimiter = ';';
                $fileContent = str_replace(["sep=;\n", "sep=;\r\n"], "", $fileContent);
            } else {
                $firstLine = strtok($fileContent, "\r\n");
                if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                    $delimiter = ';';
                } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                    $delimiter = "\t";
                }
            }

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
                    $cleanData = array_map(function($val) { return trim((string)$val); }, $data);

                    if (empty(array_filter($cleanData))) continue;

                    if (!$headerFound) {
                        $lowerData = array_map('strtolower', $cleanData);
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
                        continue;
                    }

                    $nrp = $cleanData[$headerMap['nrp']] ?? '';
                    $nama = $cleanData[$headerMap['nama']] ?? '';

                    if (!$nrp || !$nama || !preg_match('/^[0-9]+$/', $nrp)) continue;

                    if (Graduate::nrpExists($conn, $nrp)) {
                        $skipped++;
                        $warnings[] = "Baris $rowNum ($nama - $nrp): Di-skip karena NRP sudah terdaftar.";
                        continue;
                    }

                    $fakultasKode = $cleanData[$headerMap['fakultas']] ?? 'FAKULTAS';
                    $prodiRaw = $cleanData[$headerMap['prodi']] ?? '';
                    $baris = isset($headerMap['kursi']) ? strtoupper($cleanData[$headerMap['kursi']]) : '';
                    $sisiRaw = isset($headerMap['sisi']) ? strtolower($cleanData[$headerMap['sisi']]) : '';
                    $sisi = ($sisiRaw === 'kiri' || $sisiRaw === 'left') ? 'left' : 'right';
                    $nomor = isset($headerMap['nomor']) ? (int)$cleanData[$headerMap['nomor']] : 0;

                    $jenjang = 'S1';
                    $prodiNama = $prodiRaw;

                    if (isset($headerMap['jenjang']) && !empty($cleanData[$headerMap['jenjang']])) {
                        $jenjang = strtoupper($cleanData[$headerMap['jenjang']]);
                    } elseif (preg_match('/^(S3|S2|S1|D4|D3|D2|D1|PROFESI|MAGISTER|DOKTOR)[-\s_.]*(.*)/i', $prodiRaw, $matches)) {
                        $jenjang = strtoupper($matches[1]);
                        $prodiNama = trim($matches[2]);
                    }

                    if (!$prodiNama) $prodiNama = $prodiRaw ?: 'Umum';

                    $facultyRes = Graduate::findOrCreateFaculty($conn, $fakultasKode);
                    $facultyId = $facultyRes['id'];

                    $studyProgramRes = Graduate::findOrCreateStudyProgram($conn, $facultyId, $prodiNama, $jenjang);
                    $studyProgramId = $studyProgramRes['id'];

                    $seatId = null;
                    if ($baris && $nomor) {
                        $seatId = Graduate::findSeatByPosition($conn, $sessionId, $baris, $sisi, $nomor);
                        if (!$seatId) {
                            $sisiLabel = ($sisi === 'left') ? 'Kiri' : 'Kanan';
                            $failed[] = "Baris $rowNum ($nama - $nrp): Kursi Baris $baris ($sisiLabel) No $nomor TIDAK DITEMUKAN di denah (kapasitas kurang).";
                            continue;
                        }
                        if (Graduate::seatIsTaken($conn, $seatId)) {
                            $sisiLabel = ($sisi === 'left') ? 'Kiri' : 'Kanan';
                            $failed[] = "Baris $rowNum ($nama - $nrp): Kursi Baris $baris ($sisiLabel) No $nomor SUDAH DIPAKAI wisudawan lain.";
                            continue;
                        }
                    }

                    Graduate::create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $nama, $seatId);
                    $success++;
                }

                $conn->commit();
            } catch (Throwable $e) {
                $conn->rollback();
                $failed[] = "Gagal memproses import data: " . $e->getMessage();
            }

            fclose($tempStream);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['import_success'] = $success;
        $_SESSION['import_skipped'] = $skipped;
        $_SESSION['import_failed']  = $failed;
        $_SESSION['import_details'] = array_merge($warnings, $failed);

        $this->redirect("/graduates?session_id=$sessionId");
    }

    // unduh berkas template csv
    public function downloadTemplate($conn) {
        $this->checkAuth();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=template_import_wisudawan.csv');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fwrite($output, "sep=;\n");
        fputcsv($output, ['FAKULTAS', 'PROGRAM STUDI', 'KURSI', 'SISI', 'NOMOR', 'NRP', 'NAMA'], ';');
        fclose($output);
        exit;
    }
}