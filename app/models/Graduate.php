<?php

class Graduate {
    public static function getBySession($conn, $sessionId, $limit = 20, $offset = 0, $searchQuery = '') {
        $searchQuery = trim($searchQuery);
        $whereSql = "WHERE g.graduation_session_id = ?";
        $params = [$sessionId];
        $types = "i";

        if ($searchQuery !== '') {
            $term = '%' . $searchQuery . '%';
            $whereSql .= " AND (
                g.name LIKE ? OR 
                g.nrp LIKE ? OR 
                f.name LIKE ? OR 
                f.code LIKE ? OR 
                sp.name LIKE ? OR 
                sp.degree_level LIKE ? OR 
                sr.`row` LIKE ? OR 
                CONCAT('baris ', sr.`row`) LIKE ? OR
                (CASE WHEN sr.side = 'left' THEN 'kiri' ELSE 'kanan' END) LIKE ? OR
                CONCAT('baris ', sr.`row`, ' ', CASE WHEN sr.side = 'left' THEN 'kiri' ELSE 'kanan' END) LIKE ? OR
                CAST(s.global AS CHAR) LIKE ? OR 
                CAST(s.local AS CHAR) LIKE ? OR
                CONCAT(sr.`row`, LPAD(s.global, 3, '0')) LIKE ?
            )";
            for ($i = 0; $i < 13; $i++) {
                $params[] = $term;
                $types .= "s";
            }
        }

        // Pengurutan logis denah fisik: A Kiri, A Kanan, B Kiri, B Kanan, dst.
        $sql = "
            SELECT g.*, f.code AS faculty_code, f.name AS faculty_name, 
                   sp.name AS prodi_name, sp.name AS study_program_name, sp.degree_level,
                   sr.`row`, sr.side, s.local, s.global
            FROM graduates g
            JOIN faculties f ON g.faculty_id = f.id
            JOIN study_programs sp ON g.study_program_id = sp.id
            LEFT JOIN seats s ON g.seat_id = s.id
            LEFT JOIN seat_rows sr ON s.seat_row_id = sr.id
            $whereSql
            ORDER BY 
                CASE WHEN sr.`row` IS NULL THEN 1 ELSE 0 END,
                sr.`row` ASC,
                CASE WHEN sr.side = 'left' THEN 1 WHEN sr.side = 'right' THEN 2 ELSE 3 END,
                s.local ASC,
                g.id ASC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function countBySession($conn, $sessionId, $searchQuery = '') {
        $searchQuery = trim($searchQuery);
        $whereSql = "WHERE g.graduation_session_id = ?";
        $params = [$sessionId];
        $types = "i";

        if ($searchQuery !== '') {
            $term = '%' . $searchQuery . '%';
            $whereSql .= " AND (
                g.name LIKE ? OR 
                g.nrp LIKE ? OR 
                f.name LIKE ? OR 
                f.code LIKE ? OR 
                sp.name LIKE ? OR 
                sp.degree_level LIKE ? OR 
                sr.`row` LIKE ? OR 
                CONCAT('baris ', sr.`row`) LIKE ? OR
                (CASE WHEN sr.side = 'left' THEN 'kiri' ELSE 'kanan' END) LIKE ? OR
                CONCAT('baris ', sr.`row`, ' ', CASE WHEN sr.side = 'left' THEN 'kiri' ELSE 'kanan' END) LIKE ? OR
                CAST(s.global AS CHAR) LIKE ? OR 
                CAST(s.local AS CHAR) LIKE ? OR
                CONCAT(sr.`row`, LPAD(s.global, 3, '0')) LIKE ?
            )";
            for ($i = 0; $i < 13; $i++) {
                $params[] = $term;
                $types .= "s";
            }
        }

        $sql = "
            SELECT COUNT(*) AS total 
            FROM graduates g
            JOIN faculties f ON g.faculty_id = f.id
            JOIN study_programs sp ON g.study_program_id = sp.id
            LEFT JOIN seats s ON g.seat_id = s.id
            LEFT JOIN seat_rows sr ON s.seat_row_id = sr.id
            $whereSql
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM graduates WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getAvailableSeats($conn, $sessionId, $currentSeatId = null) {
        $stmt = $conn->prepare("
            SELECT s.id, sr.`row`, sr.side, s.local, s.global
            FROM seats s
            JOIN seat_rows sr ON s.seat_row_id = sr.id
            LEFT JOIN graduates g ON g.seat_id = s.id
            WHERE sr.graduation_session_id = ? AND (g.id IS NULL OR s.id = ?)
            ORDER BY sr.`row`, sr.side, s.local
        ");
        $stmt->bind_param("ii", $sessionId, $currentSeatId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId) {
        $stmt = $conn->prepare("INSERT INTO graduates (graduation_session_id, faculty_id, study_program_id, nrp, name, seat_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iiissi", $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
        return $stmt->execute();
    }

    public static function update($conn, $id, $facultyId, $studyProgramId, $nrp, $name, $seatId) {
        $stmt = $conn->prepare("UPDATE graduates SET faculty_id = ?, study_program_id = ?, nrp = ?, name = ?, seat_id = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("iissii", $facultyId, $studyProgramId, $nrp, $name, $seatId, $id);
        return $stmt->execute();
    }

    public static function getBySeatRowBeyondPosition($conn, $seatRowId, $newCapacity) {
        $stmt = $conn->prepare("
            SELECT g.id, g.name, g.nrp, s.local
            FROM graduates g
            JOIN seats s ON g.seat_id = s.id
            WHERE s.seat_row_id = ? AND s.local > ?
            ORDER BY s.local ASC
        ");
        $stmt->bind_param("ii", $seatRowId, $newCapacity);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
            WHERE sr.graduation_session_id = ? AND sr.`row` = ? AND sr.side = ? AND s.local = ?
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

    public static function countActiveInPublishedSessions($conn) {
        $sql = "SELECT COUNT(g.id) AS total 
                FROM graduates g
                JOIN graduation_sessions s ON s.id = g.graduation_session_id
                WHERE s.status = 'published'";
        $result = $conn->query($sql);
        return (int) ($result ? $result->fetch_assoc()['total'] : 0);
    }

    public static function nrpExists($conn, $nrp, $excludeId = null): bool {
        if ($excludeId !== null) {
            $stmt = $conn->prepare("SELECT id FROM graduates WHERE nrp = ? AND id != ?");
            $stmt->bind_param("si", $nrp, $excludeId);
        } else {
            $stmt = $conn->prepare("SELECT id FROM graduates WHERE nrp = ?");
            $stmt->bind_param("s", $nrp);
        }
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public static function setSeatGlobalNumber($conn, $seatId, $globalNumber) {
        $stmt = $conn->prepare("UPDATE seats SET global = ? WHERE id = ?");
        $stmt->bind_param("ii", $globalNumber, $seatId);
        return $stmt->execute();
    }
}
