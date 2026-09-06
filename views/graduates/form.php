<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Tambah Wisudawan</h1>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="session_id" value="<?= $sessionId ?>">

    <label>NRP:</label><br>
    <input type="text" name="nrp" value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>"><br>

    <label>Nama:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"><br>

    <label>Fakultas:</label><br>
    <select name="faculty_id">
        <option value="">-- Pilih --</option>
        <?php foreach ($faculties as $f): ?>
            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Program Studi:</label><br>
    <select name="study_program_id">
        <option value="">-- Pilih --</option>
        <?php foreach ($studyPrograms as $sp): ?>
            <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Kursi (opsional):</label><br>
    <select name="seat_id">
        <option value="">-- Belum Ditentukan --</option>
        <?php foreach ($seats as $seat): ?>
            <option value="<?= $seat['id'] ?>">
                Baris <?= $seat['row'] ?> <?= $seat['side'] == 'left' ? 'Kiri' : 'Kanan' ?> — No. <?= $seat['position'] ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit">Simpan</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>