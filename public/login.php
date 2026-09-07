<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$controller = new AuthController();
$controller->login($conn);