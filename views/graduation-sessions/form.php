<?php require __DIR__ . '/../partials/header.php'; ?>

<h1><?= $session ? 'Edit' : 'Tambah' ?> Sesi</h1>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <?php if (!empty($session['id'])): ?>
        <input type="hidden" name="id" value="<?= $session['id'] ?>">
    <?php else: ?>
        <input type="hidden" name="event_id" value="<?= $eventId ?>">
    <?php endif; ?>

    <label>Tanggal:</label><br>
    <input type="date" name="date" value="<?= htmlspecialchars($session['date'] ?? '') ?>"><br>

    <label>Sesi Ke- (boleh kosong):</label><br>
    <input type="number" name="session" value="<?= htmlspecialchars($session['session'] ?? '') ?>"><br>

    <label>Status:</label><br>
    <select name="status">
        <?php foreach (['draft', 'published', 'archived'] as $status): ?>
            <option value="<?= $status ?>" <?= ($session['status'] ?? 'draft') == $status ? 'selected' : '' ?>>
                <?= ucfirst($status) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Simpan</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>