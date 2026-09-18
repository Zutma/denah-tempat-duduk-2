<?php

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
            $this->checkCsrf();
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::findByUsername($conn, $username);

            $authenticated = false;
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $authenticated = true;
                } elseif ($password === $user['password']) {
                    // Fallback jika password di DB diisi manual berupa plaintext.
                    // Otomatis re-hash ke Bcrypt demi keamanan.
                    $authenticated = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $newHash, $user['id']);
                    $updateStmt->execute();
                }
            }

            if (!$authenticated) {
                $errors[] = "Username atau password salah.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                $this->redirect('/dashboard');
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

        $this->redirect('/login');
    }
}