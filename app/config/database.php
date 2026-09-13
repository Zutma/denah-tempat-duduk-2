<?php
$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'db_denah_duduk';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');