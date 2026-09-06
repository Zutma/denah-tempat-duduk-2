<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/Faculty.php';

$faculty = null;
if (isset($_GET['id'])) {
    $faculty = getFacultyById($conn, $_GET['id']);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $color = trim($_POST['color'] ?? '');

    if ($code === '') $errors[] = "Kode wajib diisi.";
    if ($name === '') $errors[] = "Nama wajib diisi.";

    if (empty($errors)) {
        if (isset($_POST['id'])) {
            updateFaculty($conn, $_POST['id'], $code, $name, $color);
        } else {
            createFaculty($conn, $code, $name, $color);
        }
        header("Location: /faculties.php");
        exit;
    }
    // Kalau ada error, data yg diketik user tetap ditampilkan lagi di form
    $faculty = ['id' => $_POST['id'] ?? null, 'code' => $code, 'name' => $name, 'color' => $color];
}

$pageTitle = $faculty ? 'Edit Fakultas' : 'Tambah Fakultas';
require __DIR__ . '/../views/faculties/form.php';