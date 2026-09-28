<?php

class User {
    public static function findByUsername($conn, $username) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}