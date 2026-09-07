<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/Faculty.php';

$faculties = getAllFaculties($conn);
$pageTitle = 'Daftar Fakultas';

require __DIR__ . '/../../../views/faculties/index.php';