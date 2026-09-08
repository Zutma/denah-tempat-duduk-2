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

        if (isset($_GET['session_id']) && $_GET['session_id'] !== '') {
            $activeSession = PublicSeat::getPublishedSessionById($conn, $_GET['session_id']);

            if ($activeSession) {
                $leftRows = PublicSeat::getSeatRowsWithSeats($conn, $activeSession['id'], 'left');
                $rightRows = PublicSeat::getSeatRowsWithSeats($conn, $activeSession['id'], 'right');

                if ($searchQuery !== '') {
                    $searchResults = PublicSeat::searchGraduates($conn, $activeSession['id'], $searchQuery);
                }
            } else {
                $message = "Sesi tidak ditemukan atau belum dipublikasikan.";
            }
        } elseif (empty($publishedSessions)) {
            $message = "Belum ada data sesi wisuda yang tersedia.";
        }

        $pageTitle = 'Denah Kursi Wisuda';

        $this->render('public/index', [
            'publishedSessions' => $publishedSessions,
            'activeSession' => $activeSession,
            'leftRows' => $leftRows,
            'rightRows' => $rightRows,
            'searchQuery' => $searchQuery,
            'searchResults' => $searchResults,
            'message' => $message,
            'pageTitle' => $pageTitle
        ]);
    }
}
