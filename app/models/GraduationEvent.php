<?php

class GraduationEvent {
    public static function all($conn) {
        $result = $conn->query("
            SELECT ge.*,
                   (SELECT COUNT(*) FROM graduation_sessions gs WHERE gs.graduation_event_id = ge.id) AS session_count
            FROM graduation_events ge
            ORDER BY ge.id DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM graduation_events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function create($conn, $name) {
        $stmt = $conn->prepare("INSERT INTO graduation_events (name, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    public static function update($conn, $id, $name) {
        $stmt = $conn->prepare("UPDATE graduation_events SET name = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM graduation_events WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM graduation_events WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }

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
