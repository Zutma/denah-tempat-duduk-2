<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {
    public function login($conn) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($conn, $email);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = "Email atau password salah.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $this->redirect('/dashboard');
            }
        }

        $pageTitle = 'Login';
        $this->render('auth/login', [
            'errors' => $errors,
            'pageTitle' => $pageTitle
        ]);
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        $this->redirect('/login.php');
    }
}
