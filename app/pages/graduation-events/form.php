<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/GraduationEvent.php';

$event = null;
if (isset($_GET['id'])) {
    $event = getEventById($conn, $_GET['id']);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') $errors[] = "Nama event wajib diisi.";

    if (empty($errors)) {
        if (isset($_POST['id'])) {
            updateEvent($conn, $_POST['id'], $name);
        } else {
            createEvent($conn, $name);
        }
        header("Location: /graduation-events");
        exit;
    }
    $event = ['id' => $_POST['id'] ?? null, 'name' => $name];
}

$pageTitle = $event ? 'Edit Event' : 'Tambah Event';
require __DIR__ . '/../../../views/graduation-events/form.php';