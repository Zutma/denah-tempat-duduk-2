<?php
require __DIR__ . '/../../../includes/db.php';
require __DIR__ . '/../../../includes/auth-check.php';
require __DIR__ . '/../../../models/GraduationEvent.php';

$events = getAllEvents($conn);
$pageTitle = 'Daftar Event Wisuda';

require __DIR__ . '/../../../views/graduation-events/index.php';