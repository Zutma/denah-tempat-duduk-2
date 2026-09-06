<?php

function getAllStudyPrograms($conn) {
    $sql = "SELECT sp.*, f.name AS faculty_name 
            FROM study_programs sp
            JOIN faculties f ON sp.faculty_id = f.id
            ORDER BY sp.name";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getStudyProgramById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM study_programs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createStudyProgram($conn, $facultyId, $name, $degreeLevel) {
    $stmt = $conn->prepare("INSERT INTO study_programs (faculty_id, name, degree_level, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("iss", $facultyId, $name, $degreeLevel);
    return $stmt->execute();
}

function updateStudyProgram($conn, $id, $facultyId, $name, $degreeLevel) {
    $stmt = $conn->prepare("UPDATE study_programs SET faculty_id = ?, name = ?, degree_level = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("issi", $facultyId, $name, $degreeLevel, $id);
    return $stmt->execute();
}

function deleteStudyProgram($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM study_programs WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}