<?php

class PublicSeat {
    public static function getPublishedSessions($conn) {
        $stmt = $conn->prepare("
            SELECT gs.*, ge.name AS event_name
            FROM graduation_sessions gs
            JOIN graduation_events ge ON gs.graduation_event_id = ge.id
            WHERE gs.status = 'published'
            ORDER BY gs.date DESC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getPublishedSessionById($conn, $id) {
        $stmt = $conn->prepare("
            SELECT gs.*, ge.name AS event_name
            FROM graduation_sessions gs
            JOIN graduation_events ge ON gs.graduation_event_id = ge.id
            WHERE gs.id = ? AND gs.status = 'published'
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getSeatRowsWithSeats($conn, $sessionId, $side) {
        $stmt = $conn->prepare("SELECT * FROM seat_rows WHERE graduation_session_id = ? AND side = ? ORDER BY `row`");
        $stmt->bind_param("is", $sessionId, $side);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$row) {
            $seatStmt = $conn->prepare("
                SELECT s.*, g.name AS graduate_name, g.nrp, sp.name AS prodi_name, f.name AS faculty_name, f.color AS faculty_color
                FROM seats s
                LEFT JOIN graduates g ON g.seat_id = s.id
                LEFT JOIN study_programs sp ON g.study_program_id = sp.id
                LEFT JOIN faculties f ON g.faculty_id = f.id
                WHERE s.seat_row_id = ?
                ORDER BY s.position
            ");
            $seatStmt->bind_param("i", $row['id']);
            $seatStmt->execute();
            $row['seats'] = $seatStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $rows;
    }

    public static function searchGraduates($conn, $sessionId, $keyword) {
        $like = '%' . $keyword . '%';
        $stmt = $conn->prepare("
            SELECT g.id AS seat_id_link, g.name, g.nrp, sp.name AS prodi_name, s.id AS seat_id, sr.`row`, s.number
            FROM graduates g
            JOIN study_programs sp ON g.study_program_id = sp.id
            LEFT JOIN seats s ON g.seat_id = s.id
            LEFT JOIN seat_rows sr ON s.seat_row_id = sr.id
            WHERE g.graduation_session_id = ? AND (g.name LIKE ? OR g.nrp LIKE ?)
            LIMIT 20
        ");
        $stmt->bind_param("iss", $sessionId, $like, $like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}