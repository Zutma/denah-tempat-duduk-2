<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/Graduate.php';
require __DIR__ . '/../../../models/Faculty.php';
require __DIR__ . '/../../../models/StudyProgram.php';

$sessionId = $_GET['session_id'] ?? $_POST['session_id'] ?? null;
$faculties = getAllFaculties($conn);
$studyPrograms = getAllStudyPrograms($conn);
$seats = getAvailableSeats($conn, $sessionId);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nrp = trim($_POST['nrp'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $facultyId = $_POST['faculty_id'] ?? '';
    $studyProgramId = $_POST['study_program_id'] ?? '';
    $seatId = $_POST['seat_id'] ?: null;

    if (!preg_match('/^[0-9]+$/', $nrp)) $errors[] = "NRP hanya boleh berisi angka.";
    if ($name === '') $errors[] = "Nama wajib diisi.";
    if ($facultyId === '') $errors[] = "Fakultas wajib dipilih.";
    if ($studyProgramId === '') $errors[] = "Prodi wajib dipilih.";

    if (empty($errors)) {
        createGraduate($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
        header("Location: /graduates?session_id=" . $sessionId);
        exit;
    }
}

$pageTitle = 'Tambah Wisudawan';
require __DIR__ . '/../../../views/graduates/form.php';