<?php

class PublicSeat {
    // ambil sesi status published
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

    // ambil detail 1 sesi published
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

    // ambil susunan baris dan kursi per sayap
    public static function getSeatRowsWithSeats($conn, $sessionId, $side) {
        $stmt = $conn->prepare("
            SELECT 
                sr.id AS row_id, sr.`row`, sr.side, sr.capacity,
                s.id AS seat_id, s.local, s.global, s.category,
                g.id AS graduate_id, g.name AS graduate_name, g.nrp,
                g.faculty_id, f.code AS faculty_code, f.color AS faculty_color,
                sp.name AS prodi_name, sp.degree_level AS degree
            FROM seat_rows sr
            LEFT JOIN seats s ON s.seat_row_id = sr.id
            LEFT JOIN graduates g ON g.seat_id = s.id
            LEFT JOIN faculties f ON g.faculty_id = f.id
            LEFT JOIN study_programs sp ON g.study_program_id = sp.id
            WHERE sr.graduation_session_id = ? AND sr.side = ?
            ORDER BY sr.`row`, s.local
        ");
        $stmt->bind_param("is", $sessionId, $side);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $rowsMap = [];
        foreach ($results as $row) {
            $rowId = $row['row_id'];
            if (!isset($rowsMap[$rowId])) {
                $rowsMap[$rowId] = [
                    'id'       => $rowId,
                    'row'      => $row['row'],
                    'side'     => $row['side'],
                    'capacity' => $row['capacity'],
                    'seats'    => []
                ];
            }

            if (!empty($row['seat_id'])) {
                $rowsMap[$rowId]['seats'][] = [
                    'id'            => (int)$row['seat_id'],
                    'seat_row_id'   => (int)$rowId,
                    'local'         => (int)$row['local'],
                    'global'        => $row['global'] !== null ? (int)$row['global'] : null,
                    'category'      => $row['category'],
                    'is_taken'      => !empty($row['graduate_id']),
                    'graduate_name' => $row['graduate_name'] ?? null,
                    'nrp'           => $row['nrp'] ?? null,
                    'prodi_name'    => $row['prodi_name'] ?? null,
                    'degree'        => $row['degree'] ?? null,
                    'faculty_id'    => $row['faculty_id'] ? (int)$row['faculty_id'] : null,
                    'faculty_code'  => $row['faculty_code'] ?? null,
                    'faculty_color' => $row['faculty_color'] ?? '#cbd5e1'
                ];
            }
        }

        return array_values($rowsMap);
    }

    // pencarian nama/nrp wisudawan di denah publik
    public static function searchGraduates($conn, $sessionId, $keyword) {
        $searchTerm = '%' . $keyword . '%';
        $stmt = $conn->prepare("
            SELECT g.name, g.nrp, sp.name AS prodi_name, s.id AS seat_id, sr.`row`, sr.`side`, s.global
            FROM graduates g
            JOIN study_programs sp ON g.study_program_id = sp.id
            LEFT JOIN seats s ON g.seat_id = s.id
            LEFT JOIN seat_rows sr ON s.seat_row_id = sr.id
            WHERE g.graduation_session_id = ? AND (g.name LIKE ? OR g.nrp LIKE ?)
            LIMIT 500
        ");
        $stmt->bind_param("iss", $sessionId, $searchTerm, $searchTerm);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // olah data denah lengkap buat ditampilkan di halaman publik
    public static function getProcessedSeatData($conn, $sessionId) {
        $leftRows = self::getSeatRowsWithSeats($conn, $sessionId, 'left');
        $rightRows = self::getSeatRowsWithSeats($conn, $sessionId, 'right');

        $stmtGrads = $conn->prepare("
            SELECT g.seat_id, g.name, g.nrp, 
                   sp.name AS prodi_name, sp.degree_level,
                   f.name AS faculty_name, f.code AS faculty_code, f.color AS faculty_color
            FROM graduates g
            JOIN study_programs sp ON g.study_program_id = sp.id
            JOIN faculties f ON g.faculty_id = f.id
            WHERE g.graduation_session_id = ? AND g.seat_id IS NOT NULL
        ");
        $stmtGrads->bind_param("i", $sessionId);
        $stmtGrads->execute();
        $rawGraduates = $stmtGrads->get_result()->fetch_all(MYSQLI_ASSOC);

        $graduatesBySeat = [];
        foreach ($rawGraduates as $rg) {
            $graduatesBySeat[$rg['seat_id']] = $rg;
        }

        $seatMapInfo = [];
        $allGraduatesList = [];
        $rowLabels = [];

        foreach ($leftRows as $lr) { $rowLabels[$lr['row']] = true; }
        foreach ($rightRows as $rr) { $rowLabels[$rr['row']] = true; }
        ksort($rowLabels);

        $leftByRow = [];
        foreach ($leftRows as $lr) { $leftByRow[$lr['row']] = $lr; }
        $rightByRow = [];
        foreach ($rightRows as $rr) { $rightByRow[$rr['row']] = $rr; }

        foreach (array_keys($rowLabels) as $label) {
            if (!empty($leftByRow[$label]['seats'])) {
                foreach ($leftByRow[$label]['seats'] as $s) {
                    $gNum = $s['global'];
                    $seatMapInfo[$s['id']] = [
                        'code' => ($gNum !== null) ? $label . sprintf('%03d', $gNum) : '-',
                        'num'  => $gNum
                    ];
                }
            }
            if (!empty($rightByRow[$label]['seats'])) {
                foreach ($rightByRow[$label]['seats'] as $s) {
                    $gNum = $s['global'];
                    $seatMapInfo[$s['id']] = [
                        'code' => ($gNum !== null) ? $label . sprintf('%03d', $gNum) : '-',
                        'num'  => $gNum
                    ];
                }
            }
        }

        foreach ($graduatesBySeat as $seatId => $g) {
            $facCode = !empty($g['faculty_code']) ? $g['faculty_code'] : ($g['faculty_name'] ?? '-');
            $allGraduatesList[] = [
                'seat_id'      => (int)$seatId,
                'seat_code'    => $seatMapInfo[$seatId]['code'] ?? '-',
                'name'         => $g['name'],
                'nrp'          => $g['nrp'],
                'prodi'        => $g['prodi_name'] ?? '-',
                'degree'       => $g['degree_level'] ?? '',
                'faculty'      => $facCode,
                'faculty_name' => $g['faculty_name'] ?? '-',
                'color'        => $g['faculty_color'] ?? '#cbd5e1'
            ];
        }

        return [
            'leftRows'         => $leftRows,
            'rightRows'        => $rightRows,
            'seatMapInfo'      => $seatMapInfo,
            'allGraduatesList' => $allGraduatesList
        ];
    }
}