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
            // Support both new Alpine format (rows[]) and legacy format (baris[])
            $rowsList = $_POST['rows'] ?? $_POST['baris'] ?? [];

            $conn->begin_transaction();
            try {
                foreach ($rowsList as $item) {
                    $rowLabel = strtoupper(trim($item['row'] ?? ''));
                    // Alpine format: left_capacity/right_capacity; legacy: kapasitas_kiri/kapasitas_kanan
                    $kapasitasKiri  = (int) ($item['left_capacity']  ?? $item['kapasitas_kiri']  ?? 0);
                    $kapasitasKanan = (int) ($item['right_capacity'] ?? $item['kapasitas_kanan'] ?? 0);

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
                $conn->commit();
            } catch (Throwable $e) {
                $conn->rollback();
                $errors[] = "Gagal menyimpan baris kursi: " . $e->getMessage();
            }
        }


        $seatRows   = SeatRow::getBySession($conn, $sessionId);
        $allLetters = range('A', 'Z');
        $usedLetters = array_values(array_unique(array_column($seatRows, 'row')));
        $pageTitle  = 'Kelola Kursi — ' . ($session['date'] ?? '');

        $this->render('seat-rows/index', [
            'sessionId'   => $sessionId,
            'session'     => $session,
            'seatRows'    => $seatRows,
            'allLetters'  => $allLetters,
            'usedLetters' => $usedLetters,
            'errors'      => $errors,
            'messages'    => $messages,
            'pageTitle'   => $pageTitle
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
