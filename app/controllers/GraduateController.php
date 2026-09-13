<?php

class GraduateController extends BaseController {
    public function index($conn) {
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

        $this->renderAdmin('graduates/index', [
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
        
        if (!$sessionId) {
            $this->redirect('/graduation-events');
        }

        $session = GraduationSession::find($conn, $sessionId);
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

            // 1. Cek Duplikat NRP Eksplisit
            if (!empty($nrp)) {
                $stmtCheck = $conn->prepare("SELECT id FROM graduates WHERE nrp = ?");
                $stmtCheck->bind_param("s", $nrp);
                $stmtCheck->execute();
                if ($stmtCheck->get_result()->num_rows > 0) {
                    $errors[] = "NRP '$nrp' sudah terdaftar di sistem! Gunakan NRP lain.";
                }
            }

            if (empty($errors)) {
                try {
                    Graduate::create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
                    $_SESSION['success'] = "Wisudawan ($name - $nrp) berhasil ditambahkan.";
                    
                    // Redirect langsung ke daftar wisudawan sesi tersebut
                    $this->redirect("/graduates?session_id=" . $sessionId);
                } catch (\mysqli_sql_exception $e) {
                    $errors[] = "Gagal menyimpan ke database: " . $e->getMessage();
                }
            }
        }

        $pageTitle = 'Tambah Wisudawan';
        
        $this->renderAdmin('graduates/form', [
            'sessionId'     => $sessionId,
            'session'       => $session,
            'faculties'     => $faculties,
            'studyPrograms' => $studyPrograms,
            'seats'         => $seats,
            'errors'        => $errors,
            'pageTitle'     => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        $sessionId = $_GET['session_id'] ?? null;
        $page = $_GET['page'] ?? 1;

        if (isset($_GET['id'])) {
            try {
                Graduate::delete($conn, $_GET['id']);
                $_SESSION['success'] = "Data wisudawan berhasil dihapus.";
            } catch (\Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus data wisudawan.";
            }
        }

        if ($sessionId) {
            $this->redirect("/graduates?session_id=" . $sessionId . "&page=" . $page);
        }

        $this->redirect('/graduation-events');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();

        $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? null;
        $page = $_POST['page'] ?? $_GET['page'] ?? 1;
        $ids = $_POST['ids'] ?? [];

        if (!empty($ids) && is_array($ids)) {
            try {
                Graduate::bulkDelete($conn, array_map('intval', $ids));
                $_SESSION['success'] = "Data wisudawan terpilih berhasil dihapus.";
            } catch (\Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus data wisudawan terpilih.";
            }
        }

        if ($sessionId) {
            $this->redirect("/graduates?session_id=" . $sessionId . "&page=" . $page);
        }

        $this->redirect('/graduation-events');
    }
}