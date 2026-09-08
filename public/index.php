<?php
require_once __DIR__ . '/../includes/db.php';

// Load All Controllers
require_once __DIR__ . '/../controllers/PublicController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/FacultyController.php';
require_once __DIR__ . '/../controllers/StudyProgramController.php';
require_once __DIR__ . '/../controllers/GraduationEventController.php';
require_once __DIR__ . '/../controllers/GraduationSessionController.php';
require_once __DIR__ . '/../controllers/SeatRowController.php';
require_once __DIR__ . '/../controllers/GraduateController.php';
require_once __DIR__ . '/../controllers/ImportController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$routes = [
    '' => [PublicController::class, 'denah'],
    'login' => [AuthController::class, 'login'],
    'logout' => [AuthController::class, 'logout'],
    'dashboard' => [DashboardController::class, 'index'],

    'faculties' => [FacultyController::class, 'index'],
    'faculties/create' => [FacultyController::class, 'form'],
    'faculties/edit' => [FacultyController::class, 'form'],
    'faculties/delete' => [FacultyController::class, 'delete'],

    'study-programs' => [StudyProgramController::class, 'index'],
    'study-programs/create' => [StudyProgramController::class, 'form'],
    'study-programs/edit' => [StudyProgramController::class, 'form'],
    'study-programs/delete' => [StudyProgramController::class, 'delete'],

    'graduation-events' => [GraduationEventController::class, 'index'],
    'graduation-events/create' => [GraduationEventController::class, 'form'],
    'graduation-events/edit' => [GraduationEventController::class, 'form'],
    'graduation-events/delete' => [GraduationEventController::class, 'delete'],

    'graduation-sessions' => [GraduationSessionController::class, 'index'],
    'graduation-sessions/create' => [GraduationSessionController::class, 'form'],
    'graduation-sessions/edit' => [GraduationSessionController::class, 'form'],
    'graduation-sessions/delete' => [GraduationSessionController::class, 'delete'],

    'seat-rows' => [SeatRowController::class, 'index'],
    'seat-rows/delete' => [SeatRowController::class, 'delete'],

    'graduates' => [GraduateController::class, 'index'],
    'graduates/create' => [GraduateController::class, 'form'],
    'graduates/delete' => [GraduateController::class, 'delete'],

    'imports/create' => [ImportController::class, 'form'],
    'imports/process' => [ImportController::class, 'process'],
];

if (array_key_exists($uri, $routes)) {
    [$controllerClass, $method] = $routes[$uri];
    $controller = new $controllerClass();
    $controller->$method($conn);
} else {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan";
}