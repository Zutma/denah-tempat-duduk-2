<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PublicSeat.php';

class PublicController extends BaseController {
    public function denah($conn) {
        $publishedSessions = PublicSeat::getPublishedSessions($conn);
        $activeSession = null;
        $leftRows = [];
        $rightRows = [];
        $seatMapInfo = [];
        $allGraduatesList = [];
        $searchQuery = trim($_GET['search'] ?? '');
        $searchResults = [];
        $message = null;

        if (array_key_exists('session_id', $_GET)) {
            $sessionId = trim($_GET['session_id']);

            if ($sessionId === '') {
                $activeSession = null;
                $message = "Silakan pilih acara wisuda pada dropdown di atas untuk melihat denah tempat duduk.";
            } else {
                $activeSession = PublicSeat::getPublishedSessionById($conn, $sessionId);

                if ($activeSession) {
                    $leftRows = PublicSeat::getSeatRowsWithSeats($conn, $activeSession['id'], 'left');
                    $rightRows = PublicSeat::getSeatRowsWithSeats($conn, $activeSession['id'], 'right');

                    // --- PROSES KODE KURSI & DAFTAR WISUDAWAN (DIPINDAHKAN DARI VIEW) ---
                    $globalCounter = 1;
                    $rowLabels = [];
                    foreach ($leftRows as $lr) { $rowLabels[$lr['row']] = true; }
                    foreach ($rightRows as $rr) { $rowLabels[$rr['row']] = true; }
                    ksort($rowLabels);

                    $leftByRow = [];
                    foreach ($leftRows as $lr) { $leftByRow[$lr['row']] = $lr; }
                    $rightByRow = [];
                    foreach ($rightRows as $rr) { $rightByRow[$rr['row']] = $rr; }

                    foreach (array_keys($rowLabels) as $label) {
                        if (!empty($leftByRow[$label]['seats'])) {
                            foreach ($leftByRow[$label]['seats'] as $s) {
                                $num = $globalCounter++;
                                $seatMapInfo[$s['id']] = [
                                    'code' => $label . sprintf('%03d', $num),
                                    'num'  => $num
                                ];
                            }
                        }
                        if (!empty($rightByRow[$label]['seats'])) {
                            foreach ($rightByRow[$label]['seats'] as $s) {
                                $num = $globalCounter++;
                                $seatMapInfo[$s['id']] = [
                                    'code' => $label . sprintf('%03d', $num),
                                    'num'  => $num
                                ];
                            }
                        }
                    }

                    $collectGraduates = function($rows) use (&$allGraduatesList, $seatMapInfo) {
                        foreach ($rows as $r) {
                            if (!empty($r['seats'])) {
                                foreach ($r['seats'] as $s) {
                                    if (!empty($s['graduate_name'])) {
                                        $facCode = !empty($s['faculty_code']) ? $s['faculty_code'] : ($s['faculty_name'] ?? '-');
                                        $allGraduatesList[] = [
                                            'seat_id'      => (int)$s['id'],
                                            'seat_code'    => $seatMapInfo[$s['id']]['code'] ?? '-',
                                            'name'         => $s['graduate_name'],
                                            'nrp'          => $s['nrp'],
                                            'prodi'        => $s['prodi_name'] ?? '-',
                                            'faculty'      => $facCode,
                                            'faculty_name' => $s['faculty_name'] ?? '-',
                                            'color'        => $s['faculty_color'] ?? '#cbd5e1'
                                        ];
                                    }
                                }
                            }
                        }
                    };
                    $collectGraduates($leftRows);
                    $collectGraduates($rightRows);

                    if ($searchQuery !== '') {
                        $searchResults = PublicSeat::searchGraduates($conn, $activeSession['id'], $searchQuery);
                    }
                } else {
                    $message = "Sesi tidak ditemukan atau belum dipublikasikan.";
                }
            }
        } else {
            $activeSession = null;
            if (empty($publishedSessions)) {
                $message = "Belum ada data sesi wisuda yang tersedia.";
            }
        }

        $this->render('public/index', [
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