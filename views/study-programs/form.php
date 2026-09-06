<?php require __DIR__ . '/../partials/header.php'; ?>

<h1><?= $studyProgram ? 'Edit' : 'Tambah' ?> Program Studi</h1>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <?php if (!empty($studyProgram['id'])): ?>
        <input type="hidden" name="id" value="<?= $studyProgram['id'] ?>">
    <?php endif; ?>

    <label>Fakultas:</label><br>
    <select name="faculty_id">
        <option value="">-- Pilih Fakultas --</option>
        <?php foreach ($faculties as $faculty): ?>
            <option value="<?= $faculty['id'] ?>"
                <?= (isset($studyProgram['faculty_id']) && $studyProgram['faculty_id'] == $faculty['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($faculty['name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Nama Prodi:</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($studyProgram['name'] ?? '') ?>"><br>

    <label>Jenjang (S1/S2/S3/D4):</label><br>
    <input type="text" name="degree_level" value="<?= htmlspecialchars($studyProgram['degree_level'] ?? '') ?>"><br>

    <button type="submit">Simpan</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>