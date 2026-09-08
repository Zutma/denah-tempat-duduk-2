<?php

class GraduationEvent {
    public static function all($conn) {
        $result = $conn->query("SELECT * FROM graduation_events ORDER BY id DESC");
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
}