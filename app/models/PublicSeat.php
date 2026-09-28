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
        // 1. Ambil baris dan seluruh kursi beserta relasinya dalam SEKALI query (JOIN)
        $stmt = $conn->prepare("
            SELECT 
                sr.id AS row_id, sr.`row`, sr.side, sr.index, sr.capacity,
                s.id AS seat_id, s.position, s.number, s.category,
                g.name AS graduate_name, g.nrp, 
                sp.name AS prodi_name, 
                f.name AS faculty_name, f.code AS faculty_code, f.color AS faculty_color
            FROM seat_rows sr
            LEFT JOIN seats s ON s.seat_row_id = sr.id
            LEFT JOIN graduates g ON g.seat_id = s.id
            LEFT JOIN study_programs sp ON g.study_program_id = sp.id
            LEFT JOIN faculties f ON g.faculty_id = f.id
            WHERE sr.graduation_session_id = ? AND sr.side = ?
            ORDER BY sr.`row`, s.position
        ");
        $stmt->bind_param("is", $sessionId, $side);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // 2. Grouping data di level PHP secara rapi
        $rowsMap = [];
        foreach ($results as $row) {
            $rowId = $row['row_id'];
            if (!isset($rowsMap[$rowId])) {
                $rowsMap[$rowId] = [
                    'id'       => $rowId,
                    'row'      => $row['row'],
                    'side'     => $row['side'],
                    'index'    => $row['index'],
                    'capacity' => $row['capacity'],
                    'seats'    => []
                ];
            }

            // Jika kursi ada, masukkan ke array seats
            if (!empty($row['seat_id'])) {
                $rowsMap[$rowId]['seats'][] = [
                    'id'            => $row['seat_id'],
                    'seat_row_id'   => $rowId,
                    'position'      => $row['position'],
                    'number'        => $row['number'],
                    'category'      => $row['category'],
                    'graduate_name' => $row['graduate_name'],
                    'nrp'           => $row['nrp'],
                    'prodi_name'    => $row['prodi_name'],
                    'faculty_name'  => $row['faculty_name'],
                    'faculty_code'  => $row['faculty_code'],
                    'faculty_color' => $row['faculty_color']
                ];
            }
        }

        return array_values($rowsMap);
    }

    public static function searchGraduates($conn, $sessionId, $keyword) {
        $searchTerm = '%' . $keyword . '%';
        $stmt = $conn->prepare("
            SELECT g.name, g.nrp, sp.name AS prodi_name, s.id AS seat_id, sr.`row`, sr.`side`, s.number
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

    /**
     * Memproses mapping kode kursi global dan daftar wisudawan secara bersih di Model
     */
    public static function getProcessedSeatData($conn, $sessionId) {
        $leftRows = self::getSeatRowsWithSeats($conn, $sessionId, 'left');
        $rightRows = self::getSeatRowsWithSeats($conn, $sessionId, 'right');

        $seatMapInfo = [];
        $allGraduatesList = [];
        $globalCounter = 1;
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
                    $num = $globalCounter++;
                    $seatMapInfo[$s['id']] = [
                        'code' => $label . sprintf('%03d', $num),
                        'num'  => $num
                    ];
                }
            }
            if (!empty($rightByRow[$label]['seats'])) {
                foreach ($rightByRow[$label]['seats'] as $s) {
                    $num = $globalCounter++;
                    $seatMapInfo[$s['id']] = [
                        'code' => $label . sprintf('%03d', $num),
                        'num'  => $num
                    ];
                }
            }
        }

        $collectGraduates = function($rows) use (&$allGraduatesList, $seatMapInfo) {
            foreach ($rows as $r) {
                if (!empty($r['seats'])) {
                    foreach ($r['seats'] as $s) {
                        if (!empty($s['graduate_name'])) {
                            $facCode = !empty($s['faculty_code']) ? $s['faculty_code'] : ($s['faculty_name'] ?? '-');
                            $allGraduatesList[] = [
                                'seat_id'      => (int)$s['id'],
                                'seat_code'    => $seatMapInfo[$s['id']]['code'] ?? '-',
                                'name'         => $s['graduate_name'],
                                'nrp'          => $s['nrp'],
                                'prodi'        => $s['prodi_name'] ?? '-',
                                'faculty'      => $facCode,
                                'faculty_name' => $s['faculty_name'] ?? '-',
                                'color'        => $s['faculty_color'] ?? '#cbd5e1'
                            ];
                        }
                    }
                }
            }
        };

        $collectGraduates($leftRows);
        $collectGraduates($rightRows);

        return [
            'leftRows'         => $leftRows,
            'rightRows'        => $rightRows,
            'seatMapInfo'      => $seatMapInfo,
            'allGraduatesList' => $allGraduatesList
        ];
    }
}