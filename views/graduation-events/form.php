<?php require __DIR__ . '/../partials/header.php'; ?>

<h1><?= $event ? 'Edit' : 'Tambah' ?> Event Wisuda</h1>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <?php if (!empty($event['id'])): ?>
        <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <?php endif; ?>

    <label>Nama Event:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($event['name'] ?? '') ?>" placeholder="Contoh: Wisuda ke-133"><br>

    <button type="submit">Simpan</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>