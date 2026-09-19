<?php

class DashboardController extends BaseController {
    // tampilkan statistik dashboard admin
    public function index($conn) {
        $totalFaculties         = Faculty::count($conn);
        $totalProdi             = StudyProgram::count($conn);
        $draftSessionsCount     = GraduationSession::countByStatus($conn, 'draft');
        $publishedSessionsCount = GraduationSession::countByStatus($conn, 'published');
        $archivedSessionsCount  = GraduationSession::countByStatus($conn, 'archived');
        $activeGraduatesCount   = Graduate::countActiveInPublishedSessions($conn);
        $recentEvents           = GraduationEvent::getRecentSummary($conn, 5);

        $this->renderAdmin('dashboard/index', [
            'pageTitle'              => 'Dashboard Overview',
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