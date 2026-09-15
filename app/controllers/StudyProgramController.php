<?php

class StudyProgramController extends BaseController {
    public function index($conn) {
        $studyPrograms = StudyProgram::all($conn);
        $pageTitle = 'Daftar Program Studi';

        $this->renderAdmin('study-programs/index', [
            'studyPrograms' => $studyPrograms,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();
        $this->checkCsrf();
        $faculties = Faculty::all($conn);
        $studyProgram = null;

        if (isset($_GET['id'])) {
            $studyProgram = StudyProgram::find($conn, $_GET['id']);
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $facultyId = $_POST['faculty_id'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $degreeLevel = trim($_POST['degree_level'] ?? '');

            if ($facultyId === '') $errors[] = "Fakultas wajib dipilih.";
            if ($name === '') $errors[] = "Nama prodi wajib diisi.";

            if (empty($errors)) {
                if (isset($_POST['id']) && $_POST['id'] !== '') {
                    StudyProgram::update($conn, $_POST['id'], $facultyId, $name, $degreeLevel);
                } else {
                    StudyProgram::create($conn, $facultyId, $name, $degreeLevel);
                }
                $this->redirect('/study-programs');
            }

            $studyProgram = [
                'id' => $_POST['id'] ?? null,
                'faculty_id' => $facultyId,
                'name' => $name,
                'degree_level' => $degreeLevel,
            ];
        }

        $pageTitle = $studyProgram ? 'Edit Program Studi' : 'Tambah Program Studi';
        $this->renderAdmin('study-programs/form', [
            'faculties' => $faculties,
            'studyProgram' => $studyProgram,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        if (isset($_GET['id'])) {
            try {
                StudyProgram::delete($conn, $_GET['id']);
                $_SESSION['success'] = "Program studi berhasil dihapus.";
            } catch (\mysqli_sql_exception $e) {
                if ($e->getCode() === 1451) {
                    $_SESSION['warning'] = "Gagal menghapus! Program Studi ini masih memiliki data Wisudawan.";
                } else {
                    $_SESSION['error'] = "Terjadi kesalahan database: " . $e->getMessage();
                }
            }
        }

        $this->redirect('/study-programs');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $ids = $_POST['ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $deleted = 0;
            $failed = 0;
            foreach ($ids as $id) {
                $id = (int)$id;
                if ($id <= 0) continue;
                try {
                    StudyProgram::delete($conn, $id);
                    $deleted++;
                } catch (\mysqli_sql_exception $e) {
                    if ($e->getCode() === 1451) {
                        $failed++;
                    }
                }
            }

            if ($deleted > 0 && $failed === 0) {
                $_SESSION['success'] = "$deleted program studi berhasil dihapus.";
            } elseif ($deleted > 0 && $failed > 0) {
                $_SESSION['warning'] = "$deleted program studi berhasil dihapus, namun $failed program studi tidak dapat dihapus karena masih memiliki data Wisudawan.";
            } else {
                $_SESSION['warning'] = "Gagal menghapus! Seluruh program studi yang dipilih masih memiliki data Wisudawan.";
            }
        }

        $this->redirect('/study-programs');
    }
}