<?php

class SeatRow {
    public static function getBySession($conn, $sessionId) {
        $stmt = $conn->prepare("SELECT * FROM seat_rows WHERE graduation_session_id = ? ORDER BY `row`, `side`");
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$row) {
            $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM seats WHERE seat_row_id = ?");
            $countStmt->bind_param("i", $row['id']);
            $countStmt->execute();
            $row['seat_count'] = $countStmt->get_result()->fetch_assoc()['total'];
        }

        return $rows;
    }

    public static function exists($conn, $sessionId, $rowLabel, $side) {
        $stmt = $conn->prepare("SELECT id FROM seat_rows WHERE graduation_session_id = ? AND `row` = ? AND `side` = ?");
        $stmt->bind_param("iss", $sessionId, $rowLabel, $side);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() !== null;
    }

    public static function createWithSeats($conn, $sessionId, $rowLabel, $side, $capacity) {
        $stmt = $conn->prepare("INSERT INTO seat_rows (`graduation_session_id`, `row`, `side`, `index`, `capacity`, `created_at`, `updated_at`) VALUES (?, ?, ?, 0, ?, NOW(), NOW())");
        $stmt->bind_param("issi", $sessionId, $rowLabel, $side, $capacity);
        $stmt->execute();
        $seatRowId = $conn->insert_id;

        $seatStmt = $conn->prepare("INSERT INTO seats (seat_row_id, position, number, category, created_at, updated_at) VALUES (?, ?, ?, 'regular', NOW(), NOW())");
        for ($i = 1; $i <= $capacity; $i++) {
            $seatStmt->bind_param("iii", $seatRowId, $i, $i);
            $seatStmt->execute();
        }
    }

    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM seat_rows WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}