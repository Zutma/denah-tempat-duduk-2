<?php

class Graduate {
    // ambil data wisudawan per sesi pake pagination
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

    // hitung total wisudawan per sesi
    public static function countBySession($conn, $sessionId) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM graduates WHERE graduation_session_id = ?");
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // cari kursi kosong di sesi tertentu
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

    // tambah wisudawan baru
    public static function create($conn, $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId) {
        $stmt = $conn->prepare("INSERT INTO graduates (graduation_session_id, faculty_id, study_program_id, nrp, name, seat_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iiissi", $sessionId, $facultyId, $studyProgramId, $nrp, $name, $seatId);
        return $stmt->execute();
    }

    // hapus wisudawan
    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM graduates WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // hapus banyak wisudawan
    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM graduates WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }

    // cari atau buat fakultas otomatis pas import
    public static function findOrCreateFaculty($conn, $code) {
        $codeClean = strtoupper(trim($code));

        // cek kode persis
        $stmt = $conn->prepare("SELECT id, name FROM faculties WHERE UPPER(code) = ? OR UPPER(name) = ?");
        $stmt->bind_param("ss", $codeClean, $codeClean);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) return ['id' => $row['id'], 'created' => false, 'matched_name' => $row['name'] ?: $codeClean];

        // kalau gak ada bikin baru
        $stmt = $conn->prepare("INSERT INTO faculties (code, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->bind_param("ss", $codeClean, $codeClean);
        $stmt->execute();
        return ['id' => $conn->insert_id, 'created' => true, 'matched_name' => $codeClean];
    }

    // cari atau buat prodi pas import
    public static function findOrCreateStudyProgram($conn, $facultyId, $rawName, $degreeLevel) {
        $rawClean = trim($rawName);
        $degreeClean = strtoupper(trim($degreeLevel));

        // ambil prodi yang udah ada di fakultas ini
        $stmt = $conn->prepare("SELECT id, name, degree_level FROM study_programs WHERE faculty_id = ?");
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $existingProdis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // ngerapihin string biar gampang nemu prodi kembar
        $normalize = function($str) {
            $str = preg_replace('/(TEK|REK|KIM|IND|MAN|MANAJ|TEKNO|TEKNOL|KOMP|PEND|ADM|INF|INFORM|SIST|AKUN|FIST|BIO|MAT|STAT)\b/i', '$1', $str);
            $str = preg_replace('/[^a-zA-Z0-9]/', ' ', $str);
            return strtolower(trim(preg_replace('/\s+/', ' ', $str)));
        };

        $targetNormalized = $normalize($rawClean);

        // cocokin ama prodi yang ada
        foreach ($existingProdis as $p) {
            $pNameNormalized = $normalize($p['name']);

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

        // kalau gak cocok bikin prodi baru
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

    // cari id kursi berdasar posisi baris
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

    // cek apakah kursi sudah ada terisi
    public static function seatIsTaken($conn, $seatId) {
        $stmt = $conn->prepare("SELECT id FROM graduates WHERE seat_id = ?");
        $stmt->bind_param("i", $seatId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() !== null;
    }

    // hitung total wisudawan di sesi status published
    public static function countActiveInPublishedSessions($conn) {
        $sql = "SELECT COUNT(g.id) AS total 
                FROM graduates g
                JOIN graduation_sessions s ON s.id = g.graduation_session_id
                WHERE s.status = 'published'";
        $result = $conn->query($sql);
        return (int) ($result ? $result->fetch_assoc()['total'] : 0);
    }

    // cek nrp udah terdaftar belum
    public static function nrpExists($conn, $nrp): bool {
        $stmt = $conn->prepare("SELECT id FROM graduates WHERE nrp = ?");
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
