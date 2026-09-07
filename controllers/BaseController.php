<?php

class BaseController {
    protected function render($viewPath, $data = []) {
        extract($data);
        require __DIR__ . '/../views/' . $viewPath . '.php';
    }

    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }

    protected function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login.php');
        }
    }
}
