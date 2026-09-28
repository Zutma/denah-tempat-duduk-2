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
        $this->checkAuth();
        $this->checkCsrf();
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
                        $errors[] = "Gagal menyimpan data. Silakan coba lagi.";
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
                    $_SESSION['warning'] = "Gagal menghapus! Fakultas ini masih memiliki data Program Studi atau Wisudawan.";
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
            $deleted = 0;
            $failed = 0;
            foreach ($ids as $id) {
                $id = (int)$id;
                if ($id <= 0) continue;
                try {
                    Faculty::delete($conn, $id);
                    $deleted++;
                } catch (\mysqli_sql_exception $e) {
                    if ($e->getCode() === 1451) {
                        $failed++;
                    }
                }
            }

            if ($deleted > 0 && $failed === 0) {
                $_SESSION['success'] = "$deleted fakultas berhasil dihapus.";
            } elseif ($deleted > 0 && $failed > 0) {
                $_SESSION['warning'] = "$deleted fakultas berhasil dihapus, namun $failed fakultas tidak dapat dihapus karena masih memiliki data Program Studi/Wisudawan.";
            } else {
                $_SESSION['warning'] = "Gagal menghapus! Seluruh fakultas yang dipilih masih memiliki data Program Studi/Wisudawan.";
            }
        }

        $this->redirect('/faculties');
    }
}