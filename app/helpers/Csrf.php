<?php

class Csrf {
    public static function token(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function field(): string {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token()) . '">';
    }

    public static function validate(): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $sent = $_POST['_csrf'] ?? '';
        return !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $sent);
    }
}