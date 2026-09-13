<?php

class GraduationEventController extends BaseController {
    public function index($conn) {
        $events = GraduationEvent::all($conn);
        $pageTitle = 'Daftar Event Wisuda';

        $this->renderAdmin('graduation-events/index', [
            'events' => $events,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
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
        $this->renderAdmin('graduation-events/form', [
            'event' => $event,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        if (isset($_GET['id'])) {
            try {
                GraduationEvent::delete($conn, $_GET['id']);
            } catch (Throwable $e) {
                // Mencegah crash jika terjadi kesalahan tak terduga
            }
        }

        $this->redirect('/graduation-events');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();

        $ids = $_POST['ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            try {
                GraduationEvent::bulkDelete($conn, array_map('intval', $ids));
            } catch (Throwable $e) {
                // Mencegah crash jika terjadi kesalahan tak terduga
            }
        }

        $this->redirect('/graduation-events');
    }
}