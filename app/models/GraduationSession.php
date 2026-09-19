<?php

class GraduationSession {
    // ambil sesi wisuda per event
    public static function getByEvent($conn, $eventId) {
        $stmt = $conn->prepare("SELECT * FROM graduation_sessions WHERE graduation_event_id = ? ORDER BY date");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // cari sesi id beserta nama event
    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT gs.*, ge.name AS event_name 
                                 FROM graduation_sessions gs
                                 JOIN graduation_events ge ON gs.graduation_event_id = ge.id
                                 WHERE gs.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // tambah sesi wisuda baru
    public static function create($conn, $eventId, $date, $sessionNumber, $status) {
        $stmt = $conn->prepare("INSERT INTO graduation_sessions (graduation_event_id, date, session, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("isis", $eventId, $date, $sessionNumber, $status);
        return $stmt->execute();
    }

    // update data sesi wisuda
    public static function update($conn, $id, $date, $sessionNumber, $status) {
        $stmt = $conn->prepare("UPDATE graduation_sessions SET date = ?, session = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sisi", $date, $sessionNumber, $status, $id);
        return $stmt->execute();
    }

    // hapus sesi beserta wisudawan & denah
    public static function delete($conn, $id) {
        $conn->begin_transaction();
        try {
            // hapus wisudawan di sesi ini
            $stmt1 = $conn->prepare("DELETE FROM graduates WHERE graduation_session_id = ?");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // hapus kursi dan baris di sesi ini
            $stmt2 = $conn->prepare("DELETE s FROM seats s JOIN seat_rows sr ON s.seat_row_id = sr.id WHERE sr.graduation_session_id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $stmt3 = $conn->prepare("DELETE FROM seat_rows WHERE graduation_session_id = ?");
            $stmt3->bind_param("i", $id);
            $stmt3->execute();

            // hapus sesi utama
            $stmt4 = $conn->prepare("DELETE FROM graduation_sessions WHERE id = ?");
            $stmt4->bind_param("i", $id);
            $res = $stmt4->execute();

            $conn->commit();
            return $res;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // hapus banyak sesi sekaligus
    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $conn->begin_transaction();
        try {
            // hapus wisudawan di sesi-sesi ini
            $stmt1 = $conn->prepare("DELETE FROM graduates WHERE graduation_session_id IN ($placeholders)");
            $stmt1->bind_param($types, ...$ids);
            $stmt1->execute();

            // hapus kursi dan baris kursi
            $stmt2 = $conn->prepare("DELETE s FROM seats s JOIN seat_rows sr ON s.seat_row_id = sr.id WHERE sr.graduation_session_id IN ($placeholders)");
            $stmt2->bind_param($types, ...$ids);
            $stmt2->execute();

            $stmt3 = $conn->prepare("DELETE FROM seat_rows WHERE graduation_session_id IN ($placeholders)");
            $stmt3->bind_param($types, ...$ids);
            $stmt3->execute();

            // hapus sesi wisuda
            $stmt4 = $conn->prepare("DELETE FROM graduation_sessions WHERE id IN ($placeholders)");
            $stmt4->bind_param($types, ...$ids);
            $res = $stmt4->execute();

            $conn->commit();
            return $res;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // hitung total sesi berdasar status
    public static function countByStatus($conn, $status) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM graduation_sessions WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    }
}