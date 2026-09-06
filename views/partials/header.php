<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Sistem Wisuda') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php if (isset($_SESSION['user_name'])): ?>
    <div style="background:#2c3e50; color:white; padding:10px 20px; display:flex; justify-content:space-between;">
        <span>Halo, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <a href="/logout.php" style="color:white;">Logout</a>
    </div>
<?php endif; ?>