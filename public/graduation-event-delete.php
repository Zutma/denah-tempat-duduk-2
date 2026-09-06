<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/GraduationEvent.php';

if (isset($_GET['id'])) {
    deleteEvent($conn, $_GET['id']);
}

header("Location: /graduation-events.php");
exit;