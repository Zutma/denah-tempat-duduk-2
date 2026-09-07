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
            $handle = fopen($_FILES['file']['tmp_name'], 'r');
            $header = fgetcsv($handle);
            $header = array_map('strtolower', array_map('trim', $header));

            $rowNum = 1;
            while (($data = fgetcsv($handle)) !== false) {
                $rowNum++;
                $row = array_combine($header, $data);

                $fakultasKode = trim($row['fakultas'] ?? '');
                $prodiNama = trim($row['prodi'] ?? '');
                $jenjang = strtoupper(trim($row['jenjang'] ?? ''));
                $baris = strtoupper(trim($row['kursi'] ?? ''));
                $sisi = strtolower(trim($row['sisi'] ?? '')) === 'kiri' ? 'left' : 'right';
                $nomor = (int) ($row['nomor'] ?? 0);
                $nrp = (string) trim($row['nrp'] ?? '');
                $nama = trim($row['nama'] ?? '');

                if (!$nrp || !$nama || !$baris || !$nomor) {
                    $failed[] = "Baris $rowNum: data tidak lengkap.";
                    continue;
                }

                $facultyId = Graduate::findOrCreateFaculty($conn, $fakultasKode);
                $studyProgramId = Graduate::findOrCreateStudyProgram($conn, $facultyId, $prodiNama, $jenjang);

                $seatId = Graduate::findSeatByPosition($conn, $sessionId, $baris, $sisi, $nomor);
                if (!$seatId) {
                    $failed[] = "Baris $rowNum: kursi $baris$nomor tidak ditemukan.";
                    continue;
                }

                if (Graduate::seatIsTaken($conn, $seatId)) {
                    $failed[] = "Baris $rowNum: kursi $baris$nomor sudah terisi.";
                    continue;
                }

                Graduate::create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $nama, $seatId);
                $success++;
            }
            fclose($handle);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['import_success'] = $success;
        $_SESSION['import_failed'] = $failed;

        $this->redirect("/graduates?session_id=$sessionId&success=" . urlencode("$success data berhasil diimport."));
    }
}
