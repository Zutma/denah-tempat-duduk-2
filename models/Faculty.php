<?php

class Faculty {
    public static function all($conn) {
        $result = $conn->query("SELECT * FROM faculties ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM faculties WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function create($conn, $code, $name, $color) {
        $stmt = $conn->prepare("INSERT INTO faculties (code, name, color, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sss", $code, $name, $color);
        return $stmt->execute();
    }

    public static function update($conn, $id, $code, $name, $color) {
        $stmt = $conn->prepare("UPDATE faculties SET code = ?, name = ?, color = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssi", $code, $name, $color, $id);
        return $stmt->execute();
    }

    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM faculties WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}