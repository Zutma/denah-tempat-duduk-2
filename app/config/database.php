<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// set kredensial koneksi db
$host     = '127.0.0.1';
$user     = 'dev_denah';
$password = 'password123';
$dbname   = 'db_denah_duduk';

// buat koneksi mysqli
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// samain charset utf8mb4
$conn->set_charset('utf8mb4');