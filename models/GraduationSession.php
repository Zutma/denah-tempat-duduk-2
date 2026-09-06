<?php

function getSessionsByEvent($conn, $eventId) {
    $stmt = $conn->prepare("SELECT * FROM graduation_sessions WHERE graduation_event_id = ? ORDER BY date");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getSessionById($conn, $id) {
    $stmt = $conn->prepare("SELECT gs.*, ge.name AS event_name 
                             FROM graduation_sessions gs
                             JOIN graduation_events ge ON gs.graduation_event_id = ge.id
                             WHERE gs.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createSession($conn, $eventId, $date, $sessionNumber, $status) {
    $stmt = $conn->prepare("INSERT INTO graduation_sessions (graduation_event_id, date, session, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("isis", $eventId, $date, $sessionNumber, $status);
    return $stmt->execute();
}

function updateSession($conn, $id, $date, $sessionNumber, $status) {
    $stmt = $conn->prepare("UPDATE graduation_sessions SET date = ?, session = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sisi", $date, $sessionNumber, $status, $id);
    return $stmt->execute();
}

function deleteSession($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM graduation_sessions WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}