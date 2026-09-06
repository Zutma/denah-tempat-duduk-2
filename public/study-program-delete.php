<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/StudyProgram.php';

if (isset($_GET['id'])) {
    deleteStudyProgram($conn, $_GET['id']);
}

header("Location: /study-programs.php");
exit;