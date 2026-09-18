<?php

class BaseController {
    /**
     * Pengecekan Session Login Admin
     */
    protected function checkAuth(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function checkCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate()) {
                http_response_code(419);
                die("Sesi form kadaluarsa. Silakan muat ulang halaman dan coba lagi.");
            }
        }
    }

    /**
     * Render Tampilan Admin (Otomatis Proteksi Auth + Include Layout)
     */
    public function renderAdmin(string $view, array $data = []): void {
        $this->checkAuth();
        
        extract($data);
        
        $viewPath = BASE_PATH . "/app/views/admin/{$view}.php";
        if (!file_exists($viewPath)) {
            $viewPath = BASE_PATH . "/app/views/admin/{$view}/index.php";
        }
        
        require_once BASE_PATH . '/app/views/admin/layouts/header.php';
        require_once BASE_PATH . '/app/views/admin/layouts/sidebar.php';
        require_once $viewPath;
        require_once BASE_PATH . '/app/views/admin/layouts/footer.php';
    }

    /**
     * Render Tampilan Publik (Bebas Tanpa Auth & Layout Admin)
     */
    public function renderPublic(string $view, array $data = []): void {
        extract($data);
        
        $viewPath = BASE_PATH . "/app/views/public/{$view}.php";
        if (!file_exists($viewPath)) {
            $viewPath = BASE_PATH . "/app/views/public/{$view}/index.php";
        }

        require_once $viewPath;
    }

    /**
     * Helper Redirect (Otomatis Menyesuaikan Subfolder /denah)
     */
    protected function redirect(string $url): void {
        if (strpos($url, '/') === 0) {
            $url = url($url);
        }
        
        header("Location: {$url}");
        exit;
    }
}