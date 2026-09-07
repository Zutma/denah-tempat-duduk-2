<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Faculty.php';

class FacultyController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        $faculties = Faculty::all($conn);
        $pageTitle = 'Daftar Fakultas';

        $this->render('faculties/index', [
            'faculties' => $faculties,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();

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
                if (isset($_POST['id']) && $_POST['id'] !== '') {
                    Faculty::update($conn, $_POST['id'], $code, $name, $color);
                } else {
                    Faculty::create($conn, $code, $name, $color);
                }
                $this->redirect('/faculties');
            }
            $faculty = ['id' => $_POST['id'] ?? null, 'code' => $code, 'name' => $name, 'color' => $color];
        }

        $pageTitle = $faculty ? 'Edit Fakultas' : 'Tambah Fakultas';
        $this->render('faculties/form', [
            'faculty' => $faculty,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        if (isset($_GET['id'])) {
            Faculty::delete($conn, $_GET['id']);
        }

        $this->redirect('/faculties');
    }
}
