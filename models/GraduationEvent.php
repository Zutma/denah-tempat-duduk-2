<?php

function getAllEvents($conn) {
    $result = $conn->query("SELECT * FROM graduation_events ORDER BY id DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getEventById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM graduation_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createEvent($conn, $name) {
    $stmt = $conn->prepare("INSERT INTO graduation_events (name, created_at, updated_at) VALUES (?, NOW(), NOW())");
    $stmt->bind_param("s", $name);
    return $stmt->execute();
}

function updateEvent($conn, $id, $name) {
    $stmt = $conn->prepare("UPDATE graduation_events SET name = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    return $stmt->execute();
}

function deleteEvent($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM graduation_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}