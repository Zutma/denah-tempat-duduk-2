<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Sistem Wisuda') ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php if (isset($_SESSION['user_name'])): ?>
        <?php require __DIR__ . '/sidebar.php'; ?>
    <?php endif; ?>