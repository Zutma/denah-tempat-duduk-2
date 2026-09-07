<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Daftar Fakultas</h1>
<a href="/faculties/create" class="button">+ Tambah Fakultas</a>

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
            <a href="/faculties/edit?id=<?= $faculty['id'] ?>">Edit</a>
            <a href="/faculties/delete?id=<?= $faculty['id'] ?>" class="delete" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>