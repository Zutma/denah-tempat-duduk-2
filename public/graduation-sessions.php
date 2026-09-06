<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/GraduationSession.php';
require __DIR__ . '/../models/GraduationEvent.php';

if (!isset($_GET['event_id'])) {
    header("Location: /graduation-events.php");
    exit;
}

$event = getEventById($conn, $_GET['event_id']);
$sessions = getSessionsByEvent($conn, $_GET['event_id']);
$pageTitle = 'Daftar Sesi — ' . $event['name'];

require __DIR__ . '/../views/graduation-sessions/index.php';