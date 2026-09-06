<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';
require __DIR__ . '/../models/SeatRow.php';
require __DIR__ . '/../models/GraduationSession.php';

if (!isset($_GET['session_id'])) {
    header("Location: /graduation-events.php");
    exit;
}

$sessionId = $_GET['session_id'];
$session = getSessionById($conn, $sessionId);
$errors = [];
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barisList = $_POST['baris'] ?? [];

    foreach ($barisList as $item) {
        $rowLabel = strtoupper(trim($item['row'] ?? ''));
        $kapasitasKiri = (int) ($item['kapasitas_kiri'] ?? 0);
        $kapasitasKanan = (int) ($item['kapasitas_kanan'] ?? 0);

        if ($rowLabel === '' || $kapasitasKiri < 1 || $kapasitasKanan < 1) {
            $errors[] = "Baris '$rowLabel' dilewati karena data tidak lengkap.";
            continue;
        }

        if (seatRowExists($conn, $sessionId, $rowLabel, 'left')) {
            $errors[] = "Baris $rowLabel Kiri sudah ada, dilewati.";
        } else {
            createSeatRowWithSeats($conn, $sessionId, $rowLabel, 'left', $kapasitasKiri);
            $messages[] = "Baris $rowLabel Kiri berhasil dibuat ($kapasitasKiri kursi).";
        }

        if (seatRowExists($conn, $sessionId, $rowLabel, 'right')) {
            $errors[] = "Baris $rowLabel Kanan sudah ada, dilewati.";
        } else {
            createSeatRowWithSeats($conn, $sessionId, $rowLabel, 'right', $kapasitasKanan);
            $messages[] = "Baris $rowLabel Kanan berhasil dibuat ($kapasitasKanan kursi).";
        }
    }
}

$seatRows = getSeatRowsBySession($conn, $sessionId);
$pageTitle = 'Kelola Kursi — ' . $session['date'];

require __DIR__ . '/../views/seat-rows/index.php';