<?php

class GraduationSessionController extends BaseController {
    public function index($conn) {
        if (!isset($_GET['event_id'])) {
            $this->redirect('/graduation-events');
        }

        $event = GraduationEvent::find($conn, $_GET['event_id']);
        $sessions = GraduationSession::getByEvent($conn, $_GET['event_id']);
        $pageTitle = 'Daftar Sesi — ' . ($event['name'] ?? '');

        $this->renderAdmin('graduation-sessions/index', [
            'event' => $event,
            'sessions' => $sessions,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();
        $this->checkCsrf();
        $session = null;
        $eventId = $_GET['event_id'] ?? null;

        if (isset($_GET['id'])) {
            $session = GraduationSession::find($conn, $_GET['id']);
            if ($session) {
                $eventId = $session['graduation_event_id'];
            }
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = trim($_POST['date'] ?? '');
            $sessionNumber = $_POST['session'] !== '' ? (int)$_POST['session'] : null;
            $status = $_POST['status'] ?? 'draft';
            $eventId = $_POST['event_id'] ?? $eventId;

            if ($date === '') $errors[] = "Tanggal wajib diisi.";

            if (empty($errors)) {
                if (isset($_POST['id']) && $_POST['id'] !== '') {
                    GraduationSession::update($conn, $_POST['id'], $date, $sessionNumber, $status);
                    $_SESSION['success'] = "Sesi wisuda berhasil diperbarui.";
                } else {
                    GraduationSession::create($conn, $eventId, $date, $sessionNumber, $status);
                    $_SESSION['success'] = "Sesi wisuda baru berhasil ditambahkan.";
                }
                $this->redirect("/graduation-sessions?event_id=" . $eventId);
            }

            $session = ['id' => $_POST['id'] ?? null, 'date' => $date, 'session' => $sessionNumber, 'status' => $status];
        }

        $pageTitle = $session ? 'Edit Sesi' : 'Tambah Sesi';
        $this->renderAdmin('graduation-sessions/form', [
            'session' => $session,
            'eventId' => $eventId,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $id = $_POST['id'] ?? $_GET['id'] ?? null;

        if ($id) {
            $session = GraduationSession::find($conn, $id);
            $eventId = $_POST['event_id'] ?? ($session ? $session['graduation_event_id'] : null);
            try {
                GraduationSession::delete($conn, $id);
                $_SESSION['success'] = "Sesi wisuda beserta data wisudawan & denah di dalamnya berhasil dihapus.";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus sesi wisuda: " . $e->getMessage();
            }

            if ($eventId) {
                $this->redirect("/graduation-sessions?event_id=" . $eventId);
            }
        }

        $this->redirect('/graduation-events');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $eventId = $_POST['event_id'] ?? $_GET['event_id'] ?? null;
        $ids = $_POST['ids'] ?? [];

        if (!empty($ids) && is_array($ids)) {
            try {
                GraduationSession::bulkDelete($conn, array_map('intval', $ids));
                $_SESSION['success'] = count($ids) . " sesi wisuda terpilih berhasil dihapus.";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus sesi terpilih: " . $e->getMessage();
            }
        }

        if ($eventId) {
            $this->redirect("/graduation-sessions?event_id=" . $eventId);
        }

        $this->redirect('/graduation-events');
    }
}
