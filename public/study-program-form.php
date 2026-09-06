<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/StudyProgram.php';
require __DIR__ . '/../models/Faculty.php';

$faculties = getAllFaculties($conn);

$studyProgram = null;
if (isset($_GET['id'])) {
    $studyProgram = getStudyProgramById($conn, $_GET['id']);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facultyId = $_POST['faculty_id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $degreeLevel = trim($_POST['degree_level'] ?? '');

    if ($facultyId === '') $errors[] = "Fakultas wajib dipilih.";
    if ($name === '') $errors[] = "Nama prodi wajib diisi.";

    if (empty($errors)) {
        if (isset($_POST['id'])) {
            updateStudyProgram($conn, $_POST['id'], $facultyId, $name, $degreeLevel);
        } else {
            createStudyProgram($conn, $facultyId, $name, $degreeLevel);
        }
        header("Location: /study-programs.php");
        exit;
    }
    $studyProgram = [
        'id' => $_POST['id'] ?? null,
        'faculty_id' => $facultyId,
        'name' => $name,
        'degree_level' => $degreeLevel,
    ];
}

$pageTitle = $studyProgram ? 'Edit Program Studi' : 'Tambah Program Studi';
require __DIR__ . '/../views/study-programs/form.php';