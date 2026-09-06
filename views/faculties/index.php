<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Daftar Fakultas</h1>
<a href="/faculty-form.php" class="button">+ Tambah Fakultas</a>

<table>
    <tr>
        <th>Kode</th><th>Nama</th><th>Warna</th><th>Aksi</th>
    </tr>
    <?php foreach ($faculties as $faculty): ?>
    <tr>
        <td><?= htmlspecialchars($faculty['code']) ?></td>
        <td><?= htmlspecialchars($faculty['name']) ?></td>
        <td><?= htmlspecialchars($faculty['color']) ?></td>
        <td>
            <a href="/faculty-form.php?id=<?= $faculty['id'] ?>">Edit</a>
            <a href="/faculty-delete.php?id=<?= $faculty['id'] ?>" class="delete" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>