<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host     = '127.0.0.1';
$user     = 'dev_denah';
$password = 'password123';
$dbname   = 'db_denah_duduk';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');