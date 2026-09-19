<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// aktifin kompresi gzip
if (!ob_start("ob_gzhandler")) {
    ob_start();
}

// set path acuan dan base url
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/denah');

function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

// konek database
require_once BASE_PATH . '/app/config/database.php';

// autoload kelas controller model ama helper
spl_autoload_register(function ($class) {
    $controllerFile = BASE_PATH . '/app/controllers/' . $class . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }

    $modelFile = BASE_PATH . '/app/models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }

    $helperFile = BASE_PATH . '/app/helpers/' . $class . '.php';
    if (file_exists($helperFile)) {
        require_once $helperFile;
        return;
    }
});

// tangkap uri lalu bersihin prefix subfolder
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$basePath = trim(BASE_URL, '/');
if ($basePath !== '' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    $uri = trim($uri, '/');
}

// daftar route clean
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

    'imports/process'               => [ImportController::class, 'process'],
    'imports/template'              => [ImportController::class, 'downloadTemplate'],
];

// eksekusi controller sesuai route
if (array_key_exists($uri, $routes)) {
    [$controllerClass, $method] = $routes[$uri];
    $controller = new $controllerClass();
    $controller->$method($conn);
} else {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan (URI terbaca: '$uri')";
}