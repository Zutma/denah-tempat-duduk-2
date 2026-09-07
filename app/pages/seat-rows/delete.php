<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/SeatRow.php';

$sessionId = $_GET['session_id'];

if (isset($_GET['id'])) {
    deleteSeatRow($conn, $_GET['id']);
}

header("Location: /seat-rows?session_id=" . $sessionId);
exit;
