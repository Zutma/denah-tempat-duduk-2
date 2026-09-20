<?php

class SeatRow {
    public static function getBySession($conn, $sessionId) {
        $stmt = $conn->prepare("
            SELECT sr.*, COUNT(s.id) AS seat_count
            FROM seat_rows sr
            LEFT JOIN seats s ON s.seat_row_id = sr.id
            WHERE sr.graduation_session_id = ?
            GROUP BY sr.id
            ORDER BY sr.`row`, sr.`side`
        ");
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function exists($conn, $sessionId, $rowLabel, $side) {
        $stmt = $conn->prepare("SELECT id FROM seat_rows WHERE graduation_session_id = ? AND `row` = ? AND `side` = ?");
        $stmt->bind_param("iss", $sessionId, $rowLabel, $side);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() !== null;
    }

    public static function createWithSeats($conn, $sessionId, $rowLabel, $side, $capacity) {
        $stmt = $conn->prepare("INSERT INTO seat_rows (`graduation_session_id`, `row`, `side`, `capacity`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("issi", $sessionId, $rowLabel, $side, $capacity);
        $stmt->execute();
        $seatRowId = $conn->insert_id;

        $seatStmt = $conn->prepare("INSERT INTO seats (seat_row_id, local, global, category, created_at, updated_at) VALUES (?, ?, NULL, 'regular', NOW(), NOW())");
        for ($i = 1; $i <= $capacity; $i++) {
            $seatStmt->bind_param("ii", $seatRowId, $i);
            $seatStmt->execute();
        }
    }

    public static function addSeats($conn, $seatRowId, $additionalCount) {
        $additionalCount = (int)$additionalCount;
        if ($additionalCount <= 0) return false;

        $stmtMax = $conn->prepare("SELECT MAX(local) AS max_pos FROM seats WHERE seat_row_id = ?");
        $stmtMax->bind_param("i", $seatRowId);
        $stmtMax->execute();
        $row = $stmtMax->get_result()->fetch_assoc();
        $lastPos = (int)($row['max_pos'] ?? 0);

        $conn->begin_transaction();
        try {
            $seatStmt = $conn->prepare("INSERT INTO seats (seat_row_id, local, global, category, created_at, updated_at) VALUES (?, ?, NULL, 'regular', NOW(), NOW())");
            for ($i = 1; $i <= $additionalCount; $i++) {
                $pos = $lastPos + $i;
                $seatStmt->bind_param("ii", $seatRowId, $pos);
                $seatStmt->execute();
            }

            $stmtUpdate = $conn->prepare("UPDATE seat_rows SET capacity = capacity + ?, updated_at = NOW() WHERE id = ?");
            $stmtUpdate->bind_param("ii", $additionalCount, $seatRowId);
            $stmtUpdate->execute();

            $conn->commit();
            return true;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    public static function shrinkSeats($conn, $seatRowId, $newCapacity) {
        $newCapacity = (int)$newCapacity;
        if ($newCapacity < 0) return false;

        $conn->begin_transaction();
        try {
            $stmtDelete = $conn->prepare("DELETE FROM seats WHERE seat_row_id = ? AND local > ?");
            $stmtDelete->bind_param("ii", $seatRowId, $newCapacity);
            $stmtDelete->execute();

            $stmtUpdate = $conn->prepare("UPDATE seat_rows SET capacity = ?, updated_at = NOW() WHERE id = ?");
            $stmtUpdate->bind_param("ii", $newCapacity, $seatRowId);
            $stmtUpdate->execute();

            $conn->commit();
            return true;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    public static function delete($conn, $id) {
        $conn->begin_transaction();
        try {
            $stmtSeats = $conn->prepare("DELETE FROM seats WHERE seat_row_id = ?");
            $stmtSeats->bind_param("i", $id);
            $stmtSeats->execute();

            $stmt = $conn->prepare("DELETE FROM seat_rows WHERE id = ?");
            $stmt->bind_param("i", $id);
            $res = $stmt->execute();

            $conn->commit();
            return $res;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }

    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $conn->begin_transaction();
        try {
            $stmtSeats = $conn->prepare("DELETE FROM seats WHERE seat_row_id IN ($placeholders)");
            $stmtSeats->bind_param($types, ...$ids);
            $stmtSeats->execute();

            $stmt = $conn->prepare("DELETE FROM seat_rows WHERE id IN ($placeholders)");
            $stmt->bind_param($types, ...$ids);
            $res = $stmt->execute();

            $conn->commit();
            return $res;
        } catch (Throwable $e) {
            $conn->rollback();
            throw $e;
        }
    }
}