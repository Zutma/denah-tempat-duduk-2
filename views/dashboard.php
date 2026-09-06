<?php require __DIR__ . '/partials/header.php'; ?>

<h1>Selamat Datang, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
<p>Pilih menu di sebelah kiri untuk mengelola data wisuda.</p>

<?php require __DIR__ . '/partials/footer.php'; ?>