<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth-check.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$routes = [
    '' => '__PUBLIC__',

    // '' => 'dashboard.php',
    'dashboard' => 'dashboard.php',

    'faculties' => 'faculties/index.php',
    'faculties/create' => 'faculties/form.php',
    'faculties/edit' => 'faculties/form.php',
    'faculties/delete' => 'faculties/delete.php',

    'study-programs' => 'study-programs/index.php',
    'study-programs/create' => 'study-programs/form.php',
    'study-programs/edit' => 'study-programs/form.php',
    'study-programs/delete' => 'study-programs/delete.php',

    'graduation-events' => 'graduation-events/index.php',
    'graduation-events/create' => 'graduation-events/form.php',
    'graduation-events/edit' => 'graduation-events/form.php',
    'graduation-events/delete' => 'graduation-events/delete.php',

    'graduation-sessions' => 'graduation-sessions/index.php',
    'graduation-sessions/create' => 'graduation-sessions/form.php',
    'graduation-sessions/edit' => 'graduation-sessions/form.php',
    'graduation-sessions/delete' => 'graduation-sessions/delete.php',

    'seat-rows' => 'seat-rows/index.php',
    'seat-rows/delete' => 'seat-rows/delete.php',

    'graduates' => 'graduates/index.php',
    'graduates/create' => 'graduates/form.php',
    'graduates/delete' => 'graduates/delete.php',

    'imports/create' => 'imports/form.php',
    'imports/process' => 'imports/process.php',
];

if ($uri === '') {
    require __DIR__ . '/../app/public-pages/denah.php';
    exit;
}

require __DIR__ . '/../includes/auth-check.php';

if (array_key_exists($uri, $routes)) {
    require __DIR__ . '/../app/pages/' . $routes[$uri];
} else {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan";
}