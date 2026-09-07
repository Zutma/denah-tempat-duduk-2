<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../models/User.php';

if (isset($_SESSION['user_id'])) {
    header("Location: /dashboard.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUserByEmail($conn, $email);

    if (!$user || !password_verify($password, $user['password'])) {
        $errors[] = "Email atau password salah.";
    } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: /dashboard");
        exit;
    }
}

$pageTitle = 'Login';
require __DIR__ . '/../views/auth/login.php';