<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GraduationEvent.php';

class GraduationEventController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        $events = GraduationEvent::all($conn);
        $pageTitle = 'Daftar Event Wisuda';

        $this->render('graduation-events/index', [
            'events' => $events,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();

        $event = null;
        if (isset($_GET['id'])) {
            $event = GraduationEvent::find($conn, $_GET['id']);
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if ($name === '') $errors[] = "Nama event wajib diisi.";

            if (empty($errors)) {
                if (isset($_POST['id']) && $_POST['id'] !== '') {
                    GraduationEvent::update($conn, $_POST['id'], $name);
                } else {
                    GraduationEvent::create($conn, $name);
                }
                $this->redirect('/graduation-events');
            }

            $event = ['id' => $_POST['id'] ?? null, 'name' => $name];
        }

        $pageTitle = $event ? 'Edit Event' : 'Tambah Event';
        $this->render('graduation-events/form', [
            'event' => $event,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        if (isset($_GET['id'])) {
            GraduationEvent::delete($conn, $_GET['id']);
        }

        $this->redirect('/graduation-events');
    }
}
