<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/GraduationSession.php';

$session = null;
$eventId = $_GET['event_id'] ?? null;

if (isset($_GET['id'])) {
    $session = getSessionById($conn, $_GET['id']);
    $eventId = $session['graduation_event_id'];
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = trim($_POST['date'] ?? '');
    $sessionNumber = $_POST['session'] !== '' ? (int)$_POST['session'] : null;
    $status = $_POST['status'] ?? 'draft';
    $eventId = $_POST['event_id'] ?? $eventId;

    if ($date === '') $errors[] = "Tanggal wajib diisi.";

    if (empty($errors)) {
        if (isset($_POST['id'])) {
            updateSession($conn, $_POST['id'], $date, $sessionNumber, $status);
        } else {
            createSession($conn, $eventId, $date, $sessionNumber, $status);
        }
        header("Location: /graduation-sessions?event_id=" . $eventId);
        exit;
    }
    $session = ['id' => $_POST['id'] ?? null, 'date' => $date, 'session' => $sessionNumber, 'status' => $status];
}

$pageTitle = $session ? 'Edit Sesi' : 'Tambah Sesi';
require __DIR__ . '/../../../views/graduation-sessions/form.php';