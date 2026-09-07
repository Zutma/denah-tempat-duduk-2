<?php require __DIR__ . '/../partials/header.php'; ?>

<p>
    <a href="/graduation-events">Wisuda</a> &gt;
    Sesi <?= htmlspecialchars($session['date']) ?> &gt;
    Data Wisudawan
</p>

<h1>Data Wisudawan — Sesi <?= htmlspecialchars($session['date']) ?></h1>

<a href="/graduates/create?session_id=<?= $sessionId ?>" class="button">+ Tambah Wisudawan</a>
<a href="/imports/create?session_id=<?= $sessionId ?>" class="button">Import Excel</a>

<?php if (isset($_GET['success'])): ?>
    <p style="color:green"><?= htmlspecialchars($_GET['success']) ?></p>
<?php endif; ?>

<table>
    <tr>
        <th>NRP</th><th>Nama</th><th>Fakultas</th><th>Jenjang</th><th>Prodi</th>
        <th>Baris</th><th>Sisi</th><th>No. Lokal</th><th>No. Global</th><th>Aksi</th>
    </tr>
    <?php foreach ($graduates as $g): ?>
    <tr>
        <td><?= htmlspecialchars($g['nrp']) ?></td>
        <td><?= htmlspecialchars($g['name']) ?></td>
        <td><?= htmlspecialchars($g['faculty_name']) ?></td>
        <td><?= htmlspecialchars($g['degree_level'] ?? '-') ?></td>
        <td><?= htmlspecialchars($g['prodi_name']) ?></td>
        <td><?= htmlspecialchars($g['row'] ?? '-') ?></td>
        <td><?= $g['side'] ? ($g['side'] == 'left' ? 'Kiri' : 'Kanan') : '-' ?></td>
        <td><?= htmlspecialchars($g['position'] ?? '-') ?></td>
        <td><?= htmlspecialchars($g['number'] ?? '-') ?></td>
        <td>
            <a href="/graduates/delete?id=<?= $g['id'] ?>&session_id=<?= $sessionId ?>"
               class="delete" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<div style="margin-top:20px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong><?= $i ?></strong>
        <?php else: ?>
            <a href="?session_id=<?= $sessionId ?>&page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>