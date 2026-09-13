<?php

class PublicController extends BaseController {
    public function denah($conn) {
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
                    // Panggil hasil olahan data dari Model secara bersih
                    $seatData = PublicSeat::getProcessedSeatData($conn, $activeSession['id']);
                    
                    $leftRows         = $seatData['leftRows'];
                    $rightRows        = $seatData['rightRows'];
                    $seatMapInfo      = $seatData['seatMapInfo'];
                    $allGraduatesList = $seatData['allGraduatesList'];

                    if ($searchQuery !== '') {
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