<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/GraduationSession.php';

$session = getSessionById($conn, $_GET['id']);
$eventId = $session['graduation_event_id'];

deleteSession($conn, $_GET['id']);

header("Location: /graduation-sessions?event_id=" . $eventId);
exit;