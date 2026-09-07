<?php

class Graduate {
    public static function getBySession($conn, $sessionId, $limit = 20, $offset = 0) {
        $stmt = $conn->prepare("
            SELECT g.*, f.name AS faculty_name, sp.name AS prodi_name, sp.degree_level,
                   sr.`row`, sr.side, s.position, s.number
            FROM graduates g
            JOIN faculties f ON g.faculty_id = f.id
            JOIN study_programs sp ON g.study_program_id = sp.id
            LEFT JOIN seats s ON g.seat_id = s.id
            LEFT JOIN seat_rows sr ON s.seat_row_id = sr.id
            WHERE g.graduation_session_id = ?
            ORDER BY g.id
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $sessionId, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function countBySession($conn, $sessionId) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM graduates WHERE graduation_session_id = ?");
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getAvailableSeats($conn, $sessionId) {
        $stmt = $conn->prepare("
            SELECT s.id, sr.`row`, sr.side, s.position
            FROM seats s
            JOIN seat_rows sr ON s.seat_row_id = sr.id
            LEFT JOIN graduates g ON g.seat_id = s.id
            WHERE sr.graduation_session_id = ? AND g.id IS NULL
            ORDER BY sr.`row`, sr.side, s.position
        ");
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId) {
        $stmt = $conn->prepare("INSERT INTO graduates (graduation_session_id, faculty_id, study_program_id, nrp, name, seat_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iiissi", $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
        return $stmt->execute();
    }

    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM graduates WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function findOrCreateFaculty($conn, $code) {
        $stmt = $conn->prepare("SELECT id FROM faculties WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) return $row['id'];

        $stmt = $conn->prepare("INSERT INTO faculties (code, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        return $conn->insert_id;
    }

    public static function findOrCreateStudyProgram($conn, $facultyId, $name, $degreeLevel) {
        $stmt = $conn->prepare("SELECT id FROM study_programs WHERE faculty_id = ? AND name = ?");
        $stmt->bind_param("is", $facultyId, $name);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) return $row['id'];

        $stmt = $conn->prepare("INSERT INTO study_programs (faculty_id, name, degree_level, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iss", $facultyId, $name, $degreeLevel);
        $stmt->execute();
        return $conn->insert_id;
    }

    public static function findSeatByPosition($conn, $sessionId, $row, $side, $position) {
        $stmt = $conn->prepare("
            SELECT s.id FROM seats s
            JOIN seat_rows sr ON s.seat_row_id = sr.id
            WHERE sr.graduation_session_id = ? AND sr.`row` = ? AND sr.side = ? AND s.position = ?
        ");
        $stmt->bind_param("issi", $sessionId, $row, $side, $position);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['id'] : null;
    }

    public static function seatIsTaken($conn, $seatId) {
        $stmt = $conn->prepare("SELECT id FROM graduates WHERE seat_id = ?");
        $stmt->bind_param("i", $seatId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() !== null;
    }
}