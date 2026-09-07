<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/SeatRow.php';
require_once __DIR__ . '/../models/GraduationSession.php';

class SeatRowController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        if (!isset($_GET['session_id'])) {
            $this->redirect('/graduation-events');
        }

        $sessionId = $_GET['session_id'];
        $session = GraduationSession::find($conn, $sessionId);
        $errors = [];
        $messages = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barisList = $_POST['baris'] ?? [];

            foreach ($barisList as $item) {
                $rowLabel = strtoupper(trim($item['row'] ?? ''));
                $kapasitasKiri = (int) ($item['kapasitas_kiri'] ?? 0);
                $kapasitasKanan = (int) ($item['kapasitas_kanan'] ?? 0);

                if ($rowLabel === '' || $kapasitasKiri < 1 || $kapasitasKanan < 1) {
                    $errors[] = "Baris '$rowLabel' dilewati karena data tidak lengkap.";
                    continue;
                }

                if (SeatRow::exists($conn, $sessionId, $rowLabel, 'left')) {
                    $errors[] = "Baris $rowLabel Kiri sudah ada, dilewati.";
                } else {
                    SeatRow::createWithSeats($conn, $sessionId, $rowLabel, 'left', $kapasitasKiri);
                    $messages[] = "Baris $rowLabel Kiri berhasil dibuat ($kapasitasKiri kursi).";
                }

                if (SeatRow::exists($conn, $sessionId, $rowLabel, 'right')) {
                    $errors[] = "Baris $rowLabel Kanan sudah ada, dilewati.";
                } else {
                    SeatRow::createWithSeats($conn, $sessionId, $rowLabel, 'right', $kapasitasKanan);
                    $messages[] = "Baris $rowLabel Kanan berhasil dibuat ($kapasitasKanan kursi).";
                }
            }
        }

        $seatRows = SeatRow::getBySession($conn, $sessionId);
        $pageTitle = 'Kelola Kursi — ' . ($session['date'] ?? '');

        $this->render('seat-rows/index', [
            'sessionId' => $sessionId,
            'session' => $session,
            'seatRows' => $seatRows,
            'errors' => $errors,
            'messages' => $messages,
            'pageTitle' => $pageTitle
        ]);
    }

    public function delete($conn) {
        $this->checkAuth();

        $sessionId = $_GET['session_id'] ?? null;

        if (isset($_GET['id'])) {
            SeatRow::delete($conn, $_GET['id']);
        }

        if ($sessionId) {
            $this->redirect("/seat-rows?session_id=" . $sessionId);
        }

        $this->redirect('/graduation-events');
    }
}
