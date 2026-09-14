<?php

class AuthController extends BaseController {

    public function login($conn) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Jika sudah login, lempar ke dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? $_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($conn, $username);

            $isPasswordValid = false;
            if ($user) {
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    $isPasswordValid = true;
                }
            }

            if (!$user || !$isPasswordValid) {
                $errors[] = "Username atau password salah.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                header('Location: /dashboard');
                exit;
            }
        }

        $pageTitle = 'Login Admin - Denah Wisuda ITS';

        // Panggil view login dari app/views/admin/auth/login.php
        require_once BASE_PATH . '/app/views/admin/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        // Redirect bersih ke route URL /login (bukan /login.php!)
        header('Location: /login');
        exit;
    }
}