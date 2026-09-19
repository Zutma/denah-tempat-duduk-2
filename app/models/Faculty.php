<?php

class Faculty {
    // ambil semua fakultas
    public static function all($conn) {
        $result = $conn->query("SELECT * FROM faculties ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // cari fakultas id
    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM faculties WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // simpan fakultas baru
    public static function create($conn, $code, $name, $color) {
        $stmt = $conn->prepare("INSERT INTO faculties (code, name, color, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sss", $code, $name, $color);
        return $stmt->execute();
    }

    // update fakultas
    public static function update($conn, $id, $code, $name, $color) {
        $stmt = $conn->prepare("UPDATE faculties SET code = ?, name = ?, color = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssi", $code, $name, $color, $id);
        return $stmt->execute();
    }

    // hapus fakultas
    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM faculties WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // hapus banyak fakultas sekaligus
    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM faculties WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }

    // hitung total fakultas
    public static function count($conn) {
        $result = $conn->query("SELECT COUNT(*) AS total FROM faculties");
        return (int) ($result->fetch_assoc()['total'] ?? 0);
    }
}
