<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/Graduate.php';

$sessionId = $_POST['session_id'];
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

        $facultyId = findOrCreateFaculty($conn, $fakultasKode);
        $studyProgramId = findOrCreateStudyProgram($conn, $facultyId, $prodiNama, $jenjang);

        $seatId = findSeatByPosition($conn, $sessionId, $baris, $sisi, $nomor);
        if (!$seatId) {
            $failed[] = "Baris $rowNum: kursi $baris$nomor tidak ditemukan.";
            continue;
        }

        if (seatIsTaken($conn, $seatId)) {
            $failed[] = "Baris $rowNum: kursi $baris$nomor sudah terisi.";
            continue;
        }

        createGraduate($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $nama, $seatId);
        $success++;
    }
    fclose($handle);
}

$_SESSION['import_success'] = $success;
$_SESSION['import_failed'] = $failed;

header("Location: /graduates.php?session_id=$sessionId&success=" . urlencode("$success data berhasil diimport."));
exit;