<?php

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
            $this->checkCsrf();

            $rowsList = $_POST['rows'] ?? $_POST['baris'] ?? [];

            $conn->begin_transaction();
            try {
                foreach ($rowsList as $item) {
                    $rowLabel = strtoupper(trim($item['row'] ?? ''));
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

        $this->renderAdmin('seat-rows/index', [
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
        $this->checkCsrf();

        $idParam = $_POST['id'] ?? $_GET['id'] ?? null;
        $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? null;

        if ($idParam) {
            $ids = array_map('intval', explode(',', $idParam));
            $count = 0;
            try {
                foreach ($ids as $id) {
                    if ($id > 0) {
                        SeatRow::delete($conn, $id);
                        $count++;
                    }
                }
                if ($count > 0) {
                    $_SESSION['success'] = "Baris kursi berhasil dihapus.";
                }
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus! Baris kursi ini masih digunakan oleh data wisudawan.";
            }
        }

        if ($sessionId) {
            $this->redirect("/seat-rows?session_id=" . $sessionId);
        }

        $this->redirect('/graduation-events');
    }

    public function bulkDelete($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? null;
        $ids = $_POST['ids'] ?? [];

        if (!empty($ids) && is_array($ids)) {
            try {
                SeatRow::bulkDelete($conn, array_map('intval', $ids));
                $_SESSION['success'] = count($ids) . " baris kursi terpilih berhasil dihapus.";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menghapus baris kursi terpilih: " . $e->getMessage();
            }
        }

        if ($sessionId) {
            $this->redirect("/seat-rows?session_id=" . $sessionId);
        }

        $this->redirect('/graduation-events');
    }
}