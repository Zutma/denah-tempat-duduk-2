<?php

class GraduationEventController extends BaseController {
    public function index($conn) {
        $events = GraduationEvent::all($conn);
        $pageTitle = 'Daftar Periode Wisuda';

        $this->renderAdmin('graduation-events/index', [
            'events' => $events,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();
        $this->checkCsrf();
        $event = null;
        if (isset($_GET['id'])) {
            $event = GraduationEvent::find($conn, $_GET['id']);
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');

            if ($name === '') $errors[] = "Nama periode wajib diisi.";

            if (empty($errors)) {
                if (isset($_POST['id']) && $_POST['id'] !== '') {
                    GraduationEvent::update($conn, $_POST['id'], $name);
                    $_SESSION['success'] = "Periode wisuda berhasil diperbarui.";
                } else {
                    GraduationEvent::create($conn, $name);
                    $_SESSION['success'] = "Periode wisuda baru berhasil ditambahkan.";
                }
                $this->redirect('/graduation-events');
            }

            $event = ['id' => $_POST['id'] ?? null, 'name' => $name];
        }

        $pageTitle = $event ? 'Edit Periode Wisuda' : 'Tambah Periode Wisuda';
        $this->renderAdmin('graduation-events/form', [
            'event' => $event,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        if (isset($_GET['id'])) {
            try {
                GraduationEvent::delete($conn, $_GET['id']);
                $_SESSION['success'] = "Periode wisuda dan seluruh data di dalamnya berhasil dihapus.";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus periode wisuda: " . $e->getMessage();
            }
        }

        $this->redirect('/graduation-events');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $ids = $_POST['ids'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            try {
                GraduationEvent::bulkDelete($conn, array_map('intval', $ids));
                $_SESSION['success'] = count($ids) . " periode wisuda terpilih berhasil dihapus.";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus periode wisuda terpilih: " . $e->getMessage();
            }
        }

        $this->redirect('/graduation-events');
    }
}