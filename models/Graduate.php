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

    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM graduates WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }

    public static function findOrCreateFaculty($conn, $code) {
        $codeClean = strtoupper(trim($code));

        // 1. Exact Match Code
        $stmt = $conn->prepare("SELECT id, name FROM faculties WHERE UPPER(code) = ? OR UPPER(name) = ?");
        $stmt->bind_param("ss", $codeClean, $codeClean);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) return ['id' => $row['id'], 'created' => false, 'matched_name' => $row['name'] ?: $codeClean];

        // 2. Buat Baru
        $stmt = $conn->prepare("INSERT INTO faculties (code, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->bind_param("ss", $codeClean, $codeClean);
        $stmt->execute();
        return ['id' => $conn->insert_id, 'created' => true, 'matched_name' => $codeClean];
    }

    public static function findOrCreateStudyProgram($conn, $facultyId, $rawName, $degreeLevel) {
        $rawClean = trim($rawName);
        $degreeClean = strtoupper(trim($degreeLevel));

        // Ambil semua prodi di fakultas ini (atau semua prodi jika fakultas baru)
        $stmt = $conn->prepare("SELECT id, name, degree_level FROM study_programs WHERE faculty_id = ?");
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $existingProdis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Fungsi normalisasi string untuk perbandingan cerdas (hilangkan titik, dash, spasi ganda)
        $normalize = function($str) {
            $str = preg_replace('/(TEK|REK|KIM|IND|MAN|MANAJ|TEKNO|TEKNOL|KOMP|PEND|ADM|INF|INFORM|SIST|AKUN|FIST|BIO|MAT|STAT)\b/i', '$1', $str);
            $str = preg_replace('/[^a-zA-Z0-9]/', ' ', $str);
            return strtolower(trim(preg_replace('/\s+/', ' ', $str)));
        };

        $targetNormalized = $normalize($rawClean);

        // 1. Check Exact atau Smart Match
        foreach ($existingProdis as $p) {
            $pNameNormalized = $normalize($p['name']);
            
            // Periksa jika persis sama (setelah dinormalisasi) ATAU salah satu mengandung kata kunci yang persis sama
            if ($targetNormalized === $pNameNormalized || 
                (strlen($targetNormalized) > 4 && strpos($pNameNormalized, $targetNormalized) !== false) ||
                (strlen($pNameNormalized) > 4 && strpos($targetNormalized, $pNameNormalized) !== false)) {
                
                return [
                    'id' => $p['id'],
                    'created' => false,
                    'matched_name' => $p['name'],
                    'degree_level' => $p['degree_level']
                ];
            }
        }

        // 2. Jika tidak ditemukan yang cocok, buat baru dengan Source of Truth dari Excel
        $stmt = $conn->prepare("INSERT INTO study_programs (faculty_id, name, degree_level, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iss", $facultyId, $rawClean, $degreeClean);
        $stmt->execute();

        return [
            'id' => $conn->insert_id,
            'created' => true,
            'matched_name' => $rawClean,
            'degree_level' => $degreeClean
        ];
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