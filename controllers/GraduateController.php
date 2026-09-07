<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Graduate.php';
require_once __DIR__ . '/../models/GraduationSession.php';
require_once __DIR__ . '/../models/Faculty.php';
require_once __DIR__ . '/../models/StudyProgram.php';

class GraduateController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        $sessionId = $_GET['session_id'] ?? null;
        if (!$sessionId) {
            $this->redirect('/graduation-events');
        }

        $session = GraduationSession::find($conn, $sessionId);

        $perPage = 20;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $graduates = Graduate::getBySession($conn, $sessionId, $perPage, $offset);
        $total = Graduate::countBySession($conn, $sessionId);
        $totalPages = (int) ceil($total / $perPage);

        $pageTitle = 'Data Wisudawan — ' . ($session['date'] ?? '');

        $this->render('graduates/index', [
            'sessionId' => $sessionId,
            'session' => $session,
            'graduates' => $graduates,
            'page' => $page,
            'totalPages' => $totalPages,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();

        $sessionId = $_GET['session_id'] ?? $_POST['session_id'] ?? null;
        $faculties = Faculty::all($conn);
        $studyPrograms = StudyProgram::all($conn);
        $seats = Graduate::getAvailableSeats($conn, $sessionId);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nrp = trim($_POST['nrp'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $facultyId = $_POST['faculty_id'] ?? '';
            $studyProgramId = $_POST['study_program_id'] ?? '';
            $seatId = $_POST['seat_id'] ?: null;

            if (!preg_match('/^[0-9]+$/', $nrp)) $errors[] = "NRP hanya boleh berisi angka.";
            if ($name === '') $errors[] = "Nama wajib diisi.";
            if ($facultyId === '') $errors[] = "Fakultas wajib dipilih.";
            if ($studyProgramId === '') $errors[] = "Prodi wajib dipilih.";

            if (empty($errors)) {
                Graduate::create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
                $this->redirect("/graduates?session_id=" . $sessionId);
            }
        }

        $pageTitle = 'Tambah Wisudawan';
        $this->render('graduates/form', [
            'sessionId' => $sessionId,
            'faculties' => $faculties,
            'studyPrograms' => $studyPrograms,
            'seats' => $seats,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        $sessionId = $_GET['session_id'] ?? null;
        if (isset($_GET['id'])) {
            Graduate::delete($conn, $_GET['id']);
        }

        if ($sessionId) {
            $this->redirect("/graduates?session_id=" . $sessionId);
        }

        $this->redirect('/graduation-events');
    }
}
