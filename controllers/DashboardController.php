<?php
require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        // 1. Data Master Sekunder
        $totalFaculties = (int) ($conn->query("SELECT COUNT(*) AS total FROM faculties")->fetch_assoc()['total'] ?? 0);
        $totalProdi     = (int) ($conn->query("SELECT COUNT(*) AS total FROM study_programs")->fetch_assoc()['total'] ?? 0);

        // 2. Breakdown Status Sesi Wisuda
        $draftSessionsQuery = $conn->query("SELECT COUNT(*) AS total FROM graduation_sessions WHERE status = 'draft'");
        $draftSessionsCount = (int) ($draftSessionsQuery ? $draftSessionsQuery->fetch_assoc()['total'] : 0);

        $publishedSessionsQuery = $conn->query("SELECT COUNT(*) AS total FROM graduation_sessions WHERE status = 'published'");
        $publishedSessionsCount = (int) ($publishedSessionsQuery ? $publishedSessionsQuery->fetch_assoc()['total'] : 0);

        $archivedSessionsQuery = $conn->query("SELECT COUNT(*) AS total FROM graduation_sessions WHERE status = 'archived'");
        $archivedSessionsCount = (int) ($archivedSessionsQuery ? $archivedSessionsQuery->fetch_assoc()['total'] : 0);

        // 3. Hitung Wisudawan yang Berada di Sesi Status 'published'
        $activeGraduatesQuery = $conn->query("
            SELECT COUNT(g.id) AS total 
            FROM graduates g
            JOIN graduation_sessions s ON s.id = g.graduation_session_id
            WHERE s.status = 'published'
        ");
        $activeGraduatesCount = (int) ($activeGraduatesQuery ? $activeGraduatesQuery->fetch_assoc()['total'] : 0);

        // 4. Ambil Daftar Periode Wisuda (Menampilkan Jumlah Sesi & Total Wisudawan)
        $recentEventsQuery = $conn->query("
            SELECT 
                e.id, 
                e.name, 
                COUNT(DISTINCT s.id) AS session_count,
                COUNT(DISTINCT g.id) AS graduate_count
            FROM graduation_events e
            LEFT JOIN graduation_sessions s ON s.graduation_event_id = e.id
            LEFT JOIN graduates g ON g.graduation_session_id = s.id
            GROUP BY e.id, e.name
            ORDER BY e.id DESC
            LIMIT 5
        ");
        $recentEvents = $recentEventsQuery ? $recentEventsQuery->fetch_all(MYSQLI_ASSOC) : [];

        $pageTitle = 'Dashboard Overview';
        $this->render('dashboard', [
            'pageTitle'              => $pageTitle,
            'totalFaculties'         => $totalFaculties,
            'totalProdi'             => $totalProdi,
            'draftSessionsCount'     => $draftSessionsCount,
            'publishedSessionsCount' => $publishedSessionsCount,
            'archivedSessionsCount'  => $archivedSessionsCount,
            'activeGraduatesCount'   => $activeGraduatesCount,
            'recentEvents'           => $recentEvents
        ]);
    }
}