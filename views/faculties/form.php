<?php require __DIR__ . '/../partials/header.php'; ?>

<h1><?= $faculty ? 'Edit' : 'Tambah' ?> Fakultas</h1>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <?php if (!empty($faculty['id'])): ?>
        <input type="hidden" name="id" value="<?= $faculty['id'] ?>">
    <?php endif; ?>

    <label>Kode:</label><br>
    <input type="text" name="code" value="<?= htmlspecialchars($faculty['code'] ?? '') ?>"><br>

    <label>Nama:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($faculty['name'] ?? '') ?>"><br>

    <label>Warna:</label><br>
    <input type="text" name="color" value="<?= htmlspecialchars($faculty['color'] ?? '') ?>"><br>

    <button type="submit">Simpan</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>