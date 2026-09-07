<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/StudyProgram.php';
require_once __DIR__ . '/../models/Faculty.php';

class StudyProgramController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        $studyPrograms = StudyProgram::all($conn);
        $pageTitle = 'Daftar Program Studi';

        $this->render('study-programs/index', [
            'studyPrograms' => $studyPrograms,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();

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
        $this->render('study-programs/form', [
            'faculties' => $faculties,
            'studyProgram' => $studyProgram,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        if (isset($_GET['id'])) {
            StudyProgram::delete($conn, $_GET['id']);
        }

        $this->redirect('/study-programs');
    }
}
