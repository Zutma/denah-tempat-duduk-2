<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/GraduationSession.php';
require_once __DIR__ . '/../models/GraduationEvent.php';

class GraduationSessionController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        if (!isset($_GET['event_id'])) {
            $this->redirect('/graduation-events');
        }

        $event = GraduationEvent::find($conn, $_GET['event_id']);
        $sessions = GraduationSession::getByEvent($conn, $_GET['event_id']);
        $pageTitle = 'Daftar Sesi — ' . ($event['name'] ?? '');

        $this->render('graduation-sessions/index', [
            'event' => $event,
            'sessions' => $sessions,
            'pageTitle' => $pageTitle
        ]);
    }

    public function form($conn) {
        $this->checkAuth();

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
                } else {
                    GraduationSession::create($conn, $eventId, $date, $sessionNumber, $status);
                }
                $this->redirect("/graduation-sessions?event_id=" . $eventId);
            }

            $session = ['id' => $_POST['id'] ?? null, 'date' => $date, 'session' => $sessionNumber, 'status' => $status];
        }

        $pageTitle = $session ? 'Edit Sesi' : 'Tambah Sesi';
        $this->render('graduation-sessions/form', [
            'session' => $session,
            'eventId' => $eventId,
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        if (isset($_GET['id'])) {
            $session = GraduationSession::find($conn, $_GET['id']);
            $eventId = $session ? $session['graduation_event_id'] : null;
            GraduationSession::delete($conn, $_GET['id']);

            if ($eventId) {
                $this->redirect("/graduation-sessions?event_id=" . $eventId);
            }
        }

        $this->redirect('/graduation-events');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();

        $eventId = $_POST['event_id'] ?? $_GET['event_id'] ?? null;
        $ids = $_POST['ids'] ?? [];

        if (!empty($ids) && is_array($ids)) {
            GraduationSession::bulkDelete($conn, array_map('intval', $ids));
        }

        if ($eventId) {
            $this->redirect("/graduation-sessions?event_id=" . $eventId);
        }

        $this->redirect('/graduation-events');
    }
}
