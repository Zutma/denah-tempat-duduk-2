<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/Graduate.php';

$sessionId = $_GET['session_id'];
deleteGraduate($conn, $_GET['id']);
header("Location: /graduates.php?session_id=" . $sessionId);
exit;