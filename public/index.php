<?php
// Aktifkan Kompresi Output Server (Gzip)
if (!ob_start("ob_gzhandler")) {
    ob_start();
}

// 1. Deklarasikan Alamat Acuan Utama
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/denah');

function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

// 2. Load Koneksi Database
require_once BASE_PATH . '/app/config/database.php';

// 3. Autoloader Otomatis untuk Controller, Model & Helper
spl_autoload_register(function ($class) {
    // Cek di folder controllers
    $controllerFile = BASE_PATH . '/app/controllers/' . $class . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }

    // Cek di folder models
    $modelFile = BASE_PATH . '/app/models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }

    // Cek di folder helpers
    $helperFile = BASE_PATH . '/app/helpers/' . $class . '.php';
    if (file_exists($helperFile)) {
        require_once $helperFile;
        return;
    }
});

// 4. Tangkap URI Browser & Bersihkan Subfolder /denah/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// Potong prefix 'denah' jika ada di URL
if (strpos($uri, 'denah') === 0) {
    $uri = substr($uri, 5);
    $uri = trim($uri, '/');
}

// 5. Daftar Rute Aplikasi (Clean Routes)
$routes = [
    ''                              => [PublicController::class, 'denah'],
    'login'                         => [AuthController::class, 'login'],
    'logout'                        => [AuthController::class, 'logout'],
    'dashboard'                     => [DashboardController::class, 'index'],

    'faculties'                     => [FacultyController::class, 'index'],
    'faculties/create'              => [FacultyController::class, 'form'],
    'faculties/edit'                => [FacultyController::class, 'form'],
    'faculties/delete'              => [FacultyController::class, 'delete'],
    'faculties/bulk-delete'         => [FacultyController::class, 'bulkDelete'],

    'study-programs'                => [StudyProgramController::class, 'index'],
    'study-programs/create'         => [StudyProgramController::class, 'form'],
    'study-programs/edit'           => [StudyProgramController::class, 'form'],
    'study-programs/delete'         => [StudyProgramController::class, 'delete'],
    'study-programs/bulk-delete'    => [StudyProgramController::class, 'bulkDelete'],

    'graduation-events'             => [GraduationEventController::class, 'index'],
    'graduation-events/create'      => [GraduationEventController::class, 'form'],
    'graduation-events/edit'        => [GraduationEventController::class, 'form'],
    'graduation-events/delete'      => [GraduationEventController::class, 'delete'],
    'graduation-events/bulk-delete' => [GraduationEventController::class, 'bulkDelete'],

    'graduation-sessions'            => [GraduationSessionController::class, 'index'],
    'graduation-sessions/create'     => [GraduationSessionController::class, 'form'],
    'graduation-sessions/edit'       => [GraduationSessionController::class, 'form'],
    'graduation-sessions/delete'     => [GraduationSessionController::class, 'delete'],
    'graduation-sessions/bulk-delete' => [GraduationSessionController::class, 'bulkDelete'],

    'seat-rows'                     => [SeatRowController::class, 'index'],
    'seat-rows/delete'              => [SeatRowController::class, 'delete'],
    'seat-rows/bulk-delete'         => [SeatRowController::class, 'bulkDelete'],

    'graduates'                     => [GraduateController::class, 'index'],
    'graduates/create'              => [GraduateController::class, 'form'],
    'graduates/delete'              => [GraduateController::class, 'delete'],
    'graduates/bulk-delete'         => [GraduateController::class, 'bulkDelete'],

    'imports/create'                => [ImportController::class, 'form'],
    'imports/process'               => [ImportController::class, 'process'],
    'imports/template'              => [ImportController::class, 'downloadTemplate'],
];

// 6. Eksekusi Router
if (array_key_exists($uri, $routes)) {
    [$controllerClass, $method] = $routes[$uri];
    $controller = new $controllerClass();
    $controller->$method($conn);
} else {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan";
}