<?php

class FacultyController extends BaseController {
    public function index($conn) {
        $faculties = Faculty::all($conn);
        $pageTitle = 'Daftar Fakultas';

        $this->renderAdmin('faculties/index', [
            'faculties' => $faculties,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $faculty = null;
        if (isset($_GET['id'])) {
            $faculty = Faculty::find($conn, $_GET['id']);
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $color = trim($_POST['color'] ?? '');

            if ($code === '') $errors[] = "Kode wajib diisi.";
            if ($name === '') $errors[] = "Nama wajib diisi.";

            if (empty($errors)) {
                try {
                    if (isset($_POST['id']) && $_POST['id'] !== '') {
                        Faculty::update($conn, $_POST['id'], $code, $name, $color);
                        $_SESSION['success'] = "Data fakultas berhasil diperbarui.";
                    } else {
                        Faculty::create($conn, $code, $name, $color);
                        $_SESSION['success'] = "Fakultas baru berhasil ditambahkan.";
                    }
                    $this->redirect('/faculties');
                } catch (\mysqli_sql_exception $e) {
                    if ($e->getCode() === 1062) {
                        $errors[] = "Kode fakultas '$code' sudah digunakan! Gunakan kode lain.";
                    } else {
                        $errors[] = "Gagal menyimpan ke database: " . $e->getMessage();
                    }
                }
            }
            $faculty = ['id' => $_POST['id'] ?? null, 'code' => $code, 'name' => $name, 'color' => $color];
        }

        $pageTitle = $faculty ? 'Edit Fakultas' : 'Tambah Fakultas';
        $this->renderAdmin('faculties/form', [
            'faculty' => $faculty,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        if (isset($_GET['id'])) {
            try {
                Faculty::delete($conn, $_GET['id']);
                $_SESSION['success'] = "Fakultas berhasil dihapus.";
            } catch (\mysqli_sql_exception $e) {
                if ($e->getCode() === 1451) {
                    $_SESSION['error'] = "Gagal menghapus! Fakultas ini masih memiliki data Program Studi atau Wisudawan di dalamnya.";
                } else {
                    $_SESSION['error'] = "Terjadi kesalahan database: " . $e->getMessage();
                }
            }
        }

        $this->redirect('/faculties');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();

        $ids = $_POST['ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            try {
                Faculty::bulkDelete($conn, array_map('intval', $ids));
            } catch (Throwable $e) {
                // Tangkap exception jika terikat foreign key
            }
        }

        $this->redirect('/faculties');
    }
}