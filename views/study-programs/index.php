<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Daftar Program Studi</h1>
<a href="/study-programs/create" class="button">+ Tambah Program Studi</a>

<table>
    <tr>
        <th>Nama Prodi</th><th>Jenjang</th><th>Fakultas</th><th>Aksi</th>
    </tr>
    <?php foreach ($studyPrograms as $sp): ?>
    <tr>
        <td><?= htmlspecialchars($sp['name']) ?></td>
        <td><?= htmlspecialchars($sp['degree_level']) ?></td>
        <td><?= htmlspecialchars($sp['faculty_name']) ?></td>
        <td>
            <a href="/study-programs/edit?id=<?= $sp['id'] ?>">Edit</a>
            <a href="/study-programs/delete?id=<?= $sp['id'] ?>" class="delete" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>