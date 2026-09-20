<?php

class GraduateController extends BaseController {
    // tampilkan daftar wisudawan per sesi
    public function index($conn) {
        $this->checkAuth();
        $sessionId = $_GET['session_id'] ?? null;
        if (!$sessionId) {
            $this->redirect('/graduation-events');
        }

        $session = GraduationSession::find($conn, $sessionId);

        $searchQuery = trim($_GET['q'] ?? $_GET['search'] ?? '');
        $perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], [10, 25, 50, 100]) ? (int)$_GET['per_page'] : 20;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $graduates = Graduate::getBySession($conn, $sessionId, $perPage, $offset, $searchQuery);
        $total = Graduate::countBySession($conn, $sessionId, $searchQuery);
        $totalPages = (int) ceil($total / $perPage);

        $pageTitle = 'Data Wisudawan — ' . ($session['date'] ?? '');

        $this->renderAdmin('graduates/index', [
            'sessionId'   => $sessionId,
            'session'     => $session,
            'graduates'   => $graduates,
            'total'       => $total,
            'perPage'     => $perPage,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'searchQuery' => $searchQuery,
            'pageTitle'   => $pageTitle
        ]);
    }

    // form tambah / edit wisudawan
    public function form($conn) {
        $this->checkAuth();

        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        $graduate = $id ? Graduate::find($conn, $id) : null;

        $sessionId = $_GET['session_id'] ?? $_POST['session_id'] ?? ($graduate['graduation_session_id'] ?? null);

        if (!$sessionId) {
            $this->redirect('/graduation-events');
        }

        $session = GraduationSession::find($conn, $sessionId);
        $faculties = Faculty::all($conn);
        $studyPrograms = StudyProgram::all($conn);
        $seats = Graduate::getAvailableSeats($conn, $sessionId, $graduate['seat_id'] ?? null);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $nrp = trim($_POST['nrp'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $facultyId = $_POST['faculty_id'] ?? '';
            $studyProgramId = $_POST['study_program_id'] ?? '';
            $seatId = $_POST['seat_id'] ?: null;

            if (!preg_match('/^[0-9]+$/', $nrp)) $errors[] = "NRP hanya boleh berisi angka.";
            if ($name === '') $errors[] = "Nama wajib diisi.";
            if ($facultyId === '') $errors[] = "Fakultas wajib dipilih.";
            if ($studyProgramId === '') $errors[] = "Prodi wajib dipilih.";

            if (!empty($nrp) && Graduate::nrpExists($conn, $nrp, $id)) {
                $errors[] = "NRP '$nrp' sudah terdaftar di sistem! Gunakan NRP lain.";
            }

            if (empty($errors)) {
                try {
                    if ($id && $graduate) {
                        Graduate::update($conn, $id, $facultyId, $studyProgramId, $nrp, $name, $seatId);
                        $_SESSION['success'] = "Data wisudawan ($name - $nrp) berhasil diperbarui.";
                    } else {
                        Graduate::create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
                        $_SESSION['success'] = "Wisudawan ($name - $nrp) berhasil ditambahkan.";
                    }
                    $this->redirect("/graduates?session_id=" . $sessionId);
                } catch (\mysqli_sql_exception $e) {
                    $errors[] = "Gagal menyimpan data. Silakan coba lagi.";
                }
            }
        }

        $pageTitle = ($id && $graduate) ? 'Edit Wisudawan' : 'Tambah Wisudawan';

        $this->renderAdmin('graduates/form', [
            'sessionId'     => $sessionId,
            'session'       => $session,
            'faculties'     => $faculties,
            'studyPrograms' => $studyPrograms,
            'seats'         => $seats,
            'graduate'      => $graduate,
            'errors'        => $errors,
            'pageTitle'     => $pageTitle
        ]);
    }

    // hapus wisudawan
    public function delete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? null;
        $page = $_POST['page'] ?? $_GET['page'] ?? 1;

        if ($id) {
            try {
                Graduate::delete($conn, $id);
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

    // hapus masal wisudawan
    public function bulkDelete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

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