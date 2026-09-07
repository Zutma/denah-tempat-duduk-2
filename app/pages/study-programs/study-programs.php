<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/StudyProgram.php';

$studyPrograms = getAllStudyPrograms($conn);
$pageTitle = 'Daftar Program Studi';

require __DIR__ . '/../../../views/study-programs/index.php';