<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PublicSeat.php';

class PublicController extends BaseController {
    public function denah($conn) {
        $publishedSessions = PublicSeat::getPublishedSessions($conn);
        $activeSession = null;
        $leftRows = [];
        $rightRows = [];
        $searchQuery = trim($_GET['search'] ?? '');
        $searchResults = [];
        $message = null;

        // Cek apakah parameter session_id ada di URL
        if (array_key_exists('session_id', $_GET)) {
            $sessionId = trim($_GET['session_id']);

            // Jika user memilih opsi kosongan (?session_id=)
            if ($sessionId === '') {
                $activeSession = null;
                $message = "Silakan pilih acara wisuda pada dropdown di atas untuk melihat denah tempat duduk.";
            } else {
                // Jika user memilih ID sesi spesifik
                $activeSession = PublicSeat::getPublishedSessionById($conn, $sessionId);

                if ($activeSession) {
                    $leftRows = PublicSeat::getSeatRowsWithSeats($conn, $activeSession['id'], 'left');
                    $rightRows = PublicSeat::getSeatRowsWithSeats($conn, $activeSession['id'], 'right');

                    if ($searchQuery !== '') {
                        $searchResults = PublicSeat::searchGraduates($conn, $activeSession['id'], $searchQuery);
                    }
                } else {
                    $message = "Sesi tidak ditemukan atau belum dipublikasikan.";
                }
            }
        } else {
            // Pertama kali buka halaman tanpa parameter URL sama sekali (denah-duduk.test/)
            // Jika ingin default-nya KOSONG, biarkan $activeSession = null
            $activeSession = null;
            
            if (empty($publishedSessions)) {
                $message = "Belum ada data sesi wisuda yang tersedia.";
            }
        }

        $pageTitle = 'Denah Kursi Wisuda';

        $this->render('public/index', [
            'publishedSessions' => $publishedSessions,
            'activeSession'     => $activeSession,
            'leftRows'          => $leftRows,
            'rightRows'         => $rightRows,
            'searchQuery'       => $searchQuery,
            'searchResults'     => $searchResults,
            'message'           => $message,
            'pageTitle'         => $pageTitle
        ]);
    }
}