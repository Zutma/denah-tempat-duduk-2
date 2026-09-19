<?php

class GraduationEvent {
    // ambil semua event wisuda
    public static function all($conn) {
        $result = $conn->query("
            SELECT ge.*, COUNT(gs.id) AS session_count
            FROM graduation_events ge
            LEFT JOIN graduation_sessions gs ON gs.graduation_event_id = ge.id
            GROUP BY ge.id
            ORDER BY ge.id DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // cari event berdasar id
    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM graduation_events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // simpan event wisuda baru
    public static function create($conn, $name) {
        $stmt = $conn->prepare("INSERT INTO graduation_events (name, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    // update nama event wisuda
    public static function update($conn, $id, $name) {
        $stmt = $conn->prepare("UPDATE graduation_events SET name = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    // hapus event beserta relasi turunan
    public static function delete($conn, $id) {
        $conn->begin_transaction();
        try {
            // hapus data wisudawan terikat
            $stmt1 = $conn->prepare("
                DELETE g FROM graduates g
                JOIN graduation_sessions gs ON g.graduation_session_id = gs.id
                WHERE gs.graduation_event_id = ?
            ");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // hapus data kursi dan baris
            $stmtSeats = $conn->prepare("
                DELETE s FROM seats s
                JOIN seat_rows sr ON s.seat_row_id = sr.id
                JOIN graduation_sessions gs ON sr.graduation_session_id = gs.id
                WHERE gs.graduation_event_id = ?
            ");
            $stmtSeats->bind_param("i", $id);
            $stmtSeats->execute();

            $stmtRows = $conn->prepare("
                DELETE sr FROM seat_rows sr
                JOIN graduation_sessions gs ON sr.graduation_session_id = gs.id
                WHERE gs.graduation_event_id = ?
            ");
            $stmtRows->bind_param("i", $id);
            $stmtRows->execute();

            // hapus sesi wisuda
            $stmt2 = $conn->prepare("DELETE FROM graduation_sessions WHERE graduation_event_id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            // hapus event utama
            $stmt3 = $conn->prepare("DELETE FROM graduation_events WHERE id = ?");
            $stmt3->bind_param("i", $id);
            $res = $stmt3->execute();

            $conn->commit();
            return $res;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // hapus banyak event sekaligus
    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $conn->begin_transaction();
        try {
            // hapus wisudawan terikat
            $stmt1 = $conn->prepare("
                DELETE g FROM graduates g
                JOIN graduation_sessions gs ON g.graduation_session_id = gs.id
                WHERE gs.graduation_event_id IN ($placeholders)
            ");
            $stmt1->bind_param($types, ...$ids);
            $stmt1->execute();

            // hapus kursi dan baris
            $stmtSeats = $conn->prepare("
                DELETE s FROM seats s
                JOIN seat_rows sr ON s.seat_row_id = sr.id
                JOIN graduation_sessions gs ON sr.graduation_session_id = gs.id
                WHERE gs.graduation_event_id IN ($placeholders)
            ");
            $stmtSeats->bind_param($types, ...$ids);
            $stmtSeats->execute();

            $stmtRows = $conn->prepare("
                DELETE sr FROM seat_rows sr
                JOIN graduation_sessions gs ON sr.graduation_session_id = gs.id
                WHERE gs.graduation_event_id IN ($placeholders)
            ");
            $stmtRows->bind_param($types, ...$ids);
            $stmtRows->execute();

            // hapus sesi wisuda
            $stmt2 = $conn->prepare("DELETE FROM graduation_sessions WHERE graduation_event_id IN ($placeholders)");
            $stmt2->bind_param($types, ...$ids);
            $stmt2->execute();

            // hapus event wisuda
            $stmt3 = $conn->prepare("DELETE FROM graduation_events WHERE id IN ($placeholders)");
            $stmt3->bind_param($types, ...$ids);
            $res = $stmt3->execute();

            $conn->commit();
            return $res;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    // ringkasan event terbaru buat dashboard
    public static function getRecentSummary($conn, $limit = 5) {
        $stmt = $conn->prepare("
            SELECT 
                e.id, 
                e.name, 
                COUNT(DISTINCT s.id) AS session_count,
                COUNT(DISTINCT g.id) AS graduate_count
            FROM graduation_events e
            LEFT JOIN graduation_sessions s ON s.graduation_event_id = e.id
            LEFT JOIN graduates g ON g.graduation_session_id = s.id
            GROUP BY e.id, e.name
            ORDER BY e.id DESC
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}