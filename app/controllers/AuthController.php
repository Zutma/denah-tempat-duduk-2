<?php

class AuthController extends BaseController {

    public function login($conn) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::findByUsername($conn, $username);

            // Wajib gunakan password_verify murni tanpa perbandingan plaintext
            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = "Username atau password salah.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                header('Location: /dashboard');
                exit;
            }
        }

        $pageTitle = 'Login Admin - Denah Wisuda ITS';
        require_once BASE_PATH . '/app/views/admin/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
        }

        session_unset();
        session_destroy();

        header('Location: /login');
        exit;
    }
}