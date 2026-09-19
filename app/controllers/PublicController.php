<?php

class PublicController extends BaseController {
    public function denah($conn) {
        // ambil sesi wisuda rilis
        $publishedSessions = PublicSeat::getPublishedSessions($conn);
        $activeSession = null;
        $searchQuery = trim($_GET['search'] ?? '');
        $searchResults = [];
        $message = null;
        
        $leftRows = [];
        $rightRows = [];
        $seatMapInfo = [];
        $allGraduatesList = [];

        if (array_key_exists('session_id', $_GET)) {
            $sessionId = trim($_GET['session_id']);

            if ($sessionId === '') {
                $message = "Silakan pilih acara wisuda pada dropdown di atas untuk melihat denah tempat duduk.";
            } else {
                $activeSession = PublicSeat::getPublishedSessionById($conn, $sessionId);

                if ($activeSession) {
                    // sedot data denah dari model
                    $seatData = PublicSeat::getProcessedSeatData($conn, $activeSession['id']);
                    
                    $leftRows         = $seatData['leftRows'];
                    $rightRows        = $seatData['rightRows'];
                    $seatMapInfo      = $seatData['seatMapInfo'];
                    $allGraduatesList = $seatData['allGraduatesList'];

                    if ($searchQuery !== '') {
                        // filter pencarian wisudawan
                        $searchResults = PublicSeat::searchGraduates($conn, $activeSession['id'], $searchQuery);
                    }
                } else {
                    $message = "Sesi tidak ditemukan atau belum dipublikasikan.";
                }
            }
        } else {
            if (empty($publishedSessions)) {
                $message = "Belum ada data sesi wisuda yang tersedia.";
            }
        }

        // render ke halaman denah publik
        $this->renderPublic('index', [
            'publishedSessions' => $publishedSessions,
            'activeSession'     => $activeSession,
            'leftRows'          => $leftRows,
            'rightRows'         => $rightRows,
            'seatMapInfo'       => $seatMapInfo,
            'allGraduatesList'  => $allGraduatesList,
            'searchQuery'       => $searchQuery,
            'searchResults'     => $searchResults,
            'message'           => $message,
            'pageTitle'         => 'Denah Kursi Wisuda'
        ]);
    }
}