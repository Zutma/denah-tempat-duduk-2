<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require __DIR__ . '/../config/database.php';

$conn = new mysqli(
    $config['host'],
    $config['user'],
    $config['password'],
    $config['dbname']
);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');