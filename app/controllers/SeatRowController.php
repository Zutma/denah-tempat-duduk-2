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

        // simpan baris baru kalo ada submit post
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

                    // bikin baris kiri
                    if (SeatRow::exists($conn, $sessionId, $rowLabel, 'left')) {
                        $errors[] = "Baris $rowLabel Kiri sudah ada, dilewati.";
                    } else {
                        SeatRow::createWithSeats($conn, $sessionId, $rowLabel, 'left', $kapasitasKiri);
                        $messages[] = "Baris $rowLabel Kiri berhasil dibuat ($kapasitasKiri kursi).";
                    }

                    // bikin baris kanan
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

        // siapin data buat view
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

        // hapus baris spesifik
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

        // hapus banyak baris sekaligus
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

    public function updateCapacity($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $sessionId = $_POST['session_id'] ?? null;
        $rowLabel = $_POST['row'] ?? '';
        $leftId = (int)($_POST['left_id'] ?? 0);
        $leftNewCap = isset($_POST['left_capacity']) ? (int)$_POST['left_capacity'] : null;
        $leftOldCap = (int)($_POST['left_old_capacity'] ?? 0);

        $rightId = (int)($_POST['right_id'] ?? 0);
        $rightNewCap = isset($_POST['right_capacity']) ? (int)$_POST['right_capacity'] : null;
        $rightOldCap = (int)($_POST['right_old_capacity'] ?? 0);

        $errors = [];
        $updates = [];

        // 1. Cek validasi untuk baris KIRI
        if ($leftId > 0 && $leftNewCap !== null && $leftNewCap >= 0 && $leftNewCap !== $leftOldCap) {
            if ($leftNewCap < $leftOldCap) {
                $affected = Graduate::getBySeatRowBeyondPosition($conn, $leftId, $leftNewCap);
                if (!empty($affected)) {
                    $count = count($affected);
                    $positions = array_map(function($g) { return $g['local']; }, $affected);
                    $names = array_map(function($g) { return $g['name']; }, $affected);
                    $posStr = implode(', ', array_unique($positions));
                    $nameStr = implode(', ', array_map(function($n) { return "[$n]"; }, $names));
                    $errors[] = "Sisi Kiri: Ada $count wisudawan di posisi $posStr: $nameStr. Hapus/pindahkan dulu.";
                } else {
                    $updates[] = ['type' => 'shrink', 'id' => $leftId, 'val' => $leftNewCap];
                }
            } else {
                $updates[] = ['type' => 'extend', 'id' => $leftId, 'val' => $leftNewCap - $leftOldCap];
            }
        }

        // 2. Cek validasi untuk baris KANAN
        if ($rightId > 0 && $rightNewCap !== null && $rightNewCap >= 0 && $rightNewCap !== $rightOldCap) {
            if ($rightNewCap < $rightOldCap) {
                $affected = Graduate::getBySeatRowBeyondPosition($conn, $rightId, $rightNewCap);
                if (!empty($affected)) {
                    $count = count($affected);
                    $positions = array_map(function($g) { return $g['local']; }, $affected);
                    $names = array_map(function($g) { return $g['name']; }, $affected);
                    $posStr = implode(', ', array_unique($positions));
                    $nameStr = implode(', ', array_map(function($n) { return "[$n]"; }, $names));
                    $errors[] = "Sisi Kanan: Ada $count wisudawan di posisi $posStr: $nameStr. Hapus/pindahkan dulu.";
                } else {
                    $updates[] = ['type' => 'shrink', 'id' => $rightId, 'val' => $rightNewCap];
                }
            } else {
                $updates[] = ['type' => 'extend', 'id' => $rightId, 'val' => $rightNewCap - $rightOldCap];
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = "Tidak dapat mengecilkan kapasitas Baris $rowLabel — " . implode(' | ', $errors);
        } else if (!empty($updates)) {
            try {
                foreach ($updates as $u) {
                    if ($u['type'] === 'shrink') {
                        SeatRow::shrinkSeats($conn, $u['id'], $u['val']);
                    } else if ($u['type'] === 'extend') {
                        SeatRow::addSeats($conn, $u['id'], $u['val']);
                    }
                }
                $_SESSION['success'] = "Kapasitas Baris $rowLabel berhasil diperbarui.";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal memperbarui kapasitas Baris $rowLabel: " . $e->getMessage();
            }
        }

        if ($sessionId) {
            $this->redirect("/seat-rows?session_id=" . $sessionId);
        }
        $this->redirect('/graduation-events');
    }

    public function extend($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $seatRowId = (int)($_POST['seat_row_id'] ?? 0);
        $additionalCount = (int)($_POST['additional_count'] ?? 0);
        $sessionId = $_POST['session_id'] ?? null;

        if ($seatRowId > 0 && $additionalCount > 0) {
            try {
                SeatRow::addSeats($conn, $seatRowId, $additionalCount);
                $_SESSION['success'] = "Kapasitas baris kursi berhasil ditambah ($additionalCount kursi).";
            } catch (Throwable $e) {
                $_SESSION['error'] = "Gagal menambah kapasitas kursi: " . $e->getMessage();
            }
        }

        if ($sessionId) {
            $this->redirect("/seat-rows?session_id=" . $sessionId);
        }
        $this->redirect('/graduation-events');
    }

    public function shrink($conn) {
        $this->checkAuth();
        $this->checkCsrf();

        $seatRowId = (int)($_POST['seat_row_id'] ?? 0);
        $newCapacity = (int)($_POST['new_capacity'] ?? 0);
        $sessionId = $_POST['session_id'] ?? null;

        if ($seatRowId > 0 && $newCapacity >= 0) {
            $affectedGraduates = Graduate::getBySeatRowBeyondPosition($conn, $seatRowId, $newCapacity);

            if (!empty($affectedGraduates)) {
                $count = count($affectedGraduates);
                $positions = array_map(function($g) { return $g['local']; }, $affectedGraduates);
                $names = array_map(function($g) { return $g['name']; }, $affectedGraduates);
                $posStr = implode(', ', array_unique($positions));
                $nameStr = implode(', ', array_map(function($n) { return "[$n]"; }, $names));

                $_SESSION['error'] = "Tidak bisa mengecilkan kapasitas — ada $count wisudawan di posisi $posStr: $nameStr. Hapus atau pindahkan wisudawan tersebut terlebih dahulu.";
            } else {
                try {
                    SeatRow::shrinkSeats($conn, $seatRowId, $newCapacity);
                    $_SESSION['success'] = "Kapasitas baris kursi berhasil dikurangi menjadi $newCapacity kursi.";
                } catch (Throwable $e) {
                    $_SESSION['error'] = "Gagal mengecilkan kapasitas kursi: " . $e->getMessage();
                }
            }
        }

        if ($sessionId) {
            $this->redirect("/seat-rows?session_id=" . $sessionId);
        }
        $this->redirect('/graduation-events');
    }
}