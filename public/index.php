<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../models/PublicSeat.php';

$publishedSessions = getPublishedSessions($conn);
$activeSession = null;
$leftRows = [];
$rightRows = [];
$searchQuery = trim($_GET['search'] ?? '');
$searchResults = [];
$message = null;

if (isset($_GET['session_id']) && $_GET['session_id'] !== '') {
    $activeSession = getPublishedSessionById($conn, $_GET['session_id']);

    if ($activeSession) {
        $leftRows = getSeatRowsWithSeats($conn, $activeSession['id'], 'left');
        $rightRows = getSeatRowsWithSeats($conn, $activeSession['id'], 'right');

        if ($searchQuery !== '') {
            $searchResults = searchGraduates($conn, $activeSession['id'], $searchQuery);
        }
    } else {
        $message = "Sesi tidak ditemukan atau belum dipublikasikan.";
    }
} elseif (empty($publishedSessions)) {
    $message = "Belum ada data sesi wisuda yang tersedia.";
}

$pageTitle = 'Denah Kursi Wisuda';
require __DIR__ . '/../views/public/index.php';