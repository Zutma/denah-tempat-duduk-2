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
            $rowsData = [];

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
                                elseif (strpos($colName, 'kursi_global') !== false || strpos($colName, 'global') !== false) $headerMap['kursi_global'] = $idx;
                                elseif (strpos($colName, 'kursi') !== false) $headerMap['kursi'] = $idx;
                                elseif (strpos($colName, 'sisi') !== false) $headerMap['sisi'] = $idx;
                                elseif (strpos($colName, 'nomor') !== false && !isset($headerMap['nomor'])) $headerMap['nomor'] = $idx;
                                elseif (strpos($colName, 'urut') !== false || strpos($colName, 'no') !== false) $headerMap['urut'] = $idx;
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
                    
                    // urut = posisi lokal (reset per baris+sisi)
                    // nomor = nomor global
                    $urutIdx = $headerMap['urut'] ?? ($headerMap['nomor'] ?? null);
                    $urut = ($urutIdx !== null && isset($cleanData[$urutIdx])) ? (int)$cleanData[$urutIdx] : 0;

                    $kursiGlobal = null;
                    if (isset($headerMap['kursi_global']) && isset($cleanData[$headerMap['kursi_global']]) && $cleanData[$headerMap['kursi_global']] !== '') {
                        $kursiGlobal = (int)$cleanData[$headerMap['kursi_global']];
                    } elseif (isset($headerMap['nomor']) && isset($headerMap['urut']) && isset($cleanData[$headerMap['nomor']]) && $cleanData[$headerMap['nomor']] !== '') {
                        $kursiGlobal = (int)$cleanData[$headerMap['nomor']];
                    }

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

                    $rowsData[] = [
                        'rowNum' => $rowNum,
                        'nrp' => $nrp,
                        'nama' => $nama,
                        'facultyId' => $facultyId,
                        'studyProgramId' => $studyProgramId,
                        'baris' => $baris,
                        'sisi' => $sisi,
                        'urut' => $urut,
                        'kursiGlobal' => $kursiGlobal
                    ];
                }

                // Scan seluruh kombinasi unik (baris, sisi) dan nilai URUT maksimum dari CSV
                $maxUrutMap = []; // key: "BARIS_SISI" => max urut (posisi lokal)
                foreach ($rowsData as $rItem) {
                    if (!empty($rItem['baris']) && !empty($rItem['urut'])) {
                        $key = $rItem['baris'] . '_' . $rItem['sisi'];
                        if (!isset($maxUrutMap[$key]) || $rItem['urut'] > $maxUrutMap[$key]) {
                            $maxUrutMap[$key] = $rItem['urut'];
                        }
                    }
                }

                // Cek atau buat seat_row untuk setiap kombinasi
                foreach ($maxUrutMap as $key => $maxUrut) {
                    [$rLabel, $rSide] = explode('_', $key);
                    $sideLabel = ($rSide === 'left') ? 'Kiri' : 'Kanan';

                    if (!SeatRow::exists($conn, $sessionId, $rLabel, $rSide)) {
                        SeatRow::createWithSeats($conn, $sessionId, $rLabel, $rSide, $maxUrut);
                    } else {
                        // Ambil kapasitas existing
                        $stmtCheckCap = $conn->prepare("SELECT capacity FROM seat_rows WHERE graduation_session_id = ? AND `row` = ? AND `side` = ?");
                        $stmtCheckCap->bind_param("iss", $sessionId, $rLabel, $rSide);
                        $stmtCheckCap->execute();
                        $existingCapRow = $stmtCheckCap->get_result()->fetch_assoc();
                        $existingCapacity = $existingCapRow ? (int)$existingCapRow['capacity'] : 0;

                        if ($existingCapacity < $maxUrut) {
                            $failedCount = 0;
                            foreach ($rowsData as $rItem) {
                                if ($rItem['baris'] === $rLabel && $rItem['sisi'] === $rSide && $rItem['urut'] > $existingCapacity) {
                                    $failedCount++;
                                }
                            }
                            $warnings[] = "Baris {$rLabel} sisi {$sideLabel}: file butuh kapasitas {$maxUrut} tapi baris ini cuma punya kapasitas existing {$existingCapacity}. {$failedCount} wisudawan gagal di-assign kursi.";
                        }
                    }
                }

                // Insert data wisudawan
                foreach ($rowsData as $rItem) {
                    $rowNum = $rItem['rowNum'];
                    $nrp = $rItem['nrp'];
                    $nama = $rItem['nama'];
                    $facultyId = $rItem['facultyId'];
                    $studyProgramId = $rItem['studyProgramId'];
                    $baris = $rItem['baris'];
                    $sisi = $rItem['sisi'];
                    $urut = $rItem['urut'];
                    $kursiGlobal = $rItem['kursiGlobal'];

                    $seatId = null;
                    if ($baris && $urut) {
                        $seatId = Graduate::findSeatByPosition($conn, $sessionId, $baris, $sisi, $urut);
                        if (!$seatId) {
                            // Jika kursi tidak ditemukan (melebihi kapasitas existing), $seatId tetap null
                        } elseif (Graduate::seatIsTaken($conn, $seatId)) {
                            $sisiLabel = ($sisi === 'left') ? 'Kiri' : 'Kanan';
                            $failed[] = "Baris $rowNum ($nama - $nrp): Kursi Baris $baris ($sisiLabel) No $urut SUDAH DIPAKAI wisudawan lain.";
                            continue;
                        }
                    }

                    if ($seatId !== null && $kursiGlobal !== null) {
                        Graduate::setSeatGlobalNumber($conn, $seatId, $kursiGlobal);
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
        fputcsv($output, ['FAKULTAS', 'PROGRAM STUDI', 'KURSI', 'SISI', 'NOMOR', 'URUT', 'NRP', 'NAMA'], ';');
        fclose($output);
        exit;
    }
}