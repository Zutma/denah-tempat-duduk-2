<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/Graduate.php';
require __DIR__ . '/../../../models/GraduationSession.php';

$sessionId = $_GET['session_id'] ?? null;
if (!$sessionId) {
    header("Location: /graduation-events");
    exit;
}

$session = getSessionById($conn, $sessionId);

$perPage = 20;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$graduates = getGraduatesBySession($conn, $sessionId, $perPage, $offset);
$total = countGraduatesBySession($conn, $sessionId);
$totalPages = (int) ceil($total / $perPage);

$pageTitle = 'Data Wisudawan — ' . $session['date'];

require __DIR__ . '/../../../views/graduates/index.php';