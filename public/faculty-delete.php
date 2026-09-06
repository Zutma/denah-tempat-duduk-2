<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/Faculty.php';

if (isset($_GET['id'])) {
    deleteFaculty($conn, $_GET['id']);
}

header("Location: faculties.php");
exit;