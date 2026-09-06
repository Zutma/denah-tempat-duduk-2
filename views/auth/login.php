<?php require __DIR__ . '/../partials/header.php'; ?>

<div style="max-width:400px; margin:50px auto; background:white; padding:30px; border-radius:8px;">
    <h1>Login Admin</h1>

    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br>

        <label>Password:</label><br>
        <input type="password" name="password"><br>

        <button type="submit">Login</button>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>