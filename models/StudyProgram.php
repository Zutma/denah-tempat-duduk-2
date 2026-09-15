<?php

class StudyProgram {
    public static function all($conn) {
        $sql = "SELECT sp.*, f.name AS faculty_name 
                FROM study_programs sp
                JOIN faculties f ON sp.faculty_id = f.id
                ORDER BY sp.name";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function find($conn, $id) {
        $stmt = $conn->prepare("SELECT * FROM study_programs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function create($conn, $facultyId, $name, $degreeLevel) {
        $stmt = $conn->prepare("INSERT INTO study_programs (faculty_id, name, degree_level, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iss", $facultyId, $name, $degreeLevel);
        return $stmt->execute();
    }

    public static function update($conn, $id, $facultyId, $name, $degreeLevel) {
        $stmt = $conn->prepare("UPDATE study_programs SET faculty_id = ?, name = ?, degree_level = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("issi", $facultyId, $name, $degreeLevel, $id);
        return $stmt->execute();
    }

    public static function delete($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM study_programs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function bulkDelete($conn, array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmt = $conn->prepare("DELETE FROM study_programs WHERE id IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
}