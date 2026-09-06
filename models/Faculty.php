<?php

function getAllFaculties($conn) {
    $result = $conn->query("SELECT * FROM faculties ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getFacultyById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM faculties WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createFaculty($conn, $code, $name, $color) {
    $stmt = $conn->prepare("INSERT INTO faculties (code, name, color, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sss", $code, $name, $color);
    return $stmt->execute();
}

function updateFaculty($conn, $id, $code, $name, $color) {
    $stmt = $conn->prepare("UPDATE faculties SET code = ?, name = ?, color = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssi", $code, $name, $color, $id);
    return $stmt->execute();
}

function deleteFaculty($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM faculties WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}