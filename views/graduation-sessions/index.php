<?php require __DIR__ . '/../partials/header.php'; ?>

<p><a href="/graduation-events.php">Wisuda</a> &gt; <?= htmlspecialchars($event['name']) ?></p>
<h1>Daftar Sesi — <?= htmlspecialchars($event['name']) ?></h1>

<a href="/graduation-session-form.php?event_id=<?= $event['id'] ?>" class="button">+ Tambah Sesi</a>

<table>
    <tr><th>Tanggal</th><th>Sesi Ke-</th><th>Status</th><th>Aksi</th></tr>
    <?php foreach ($sessions as $session): ?>
    <tr>
        <td><?= htmlspecialchars($session['date']) ?></td>
        <td><?= htmlspecialchars($session['session'] ?? '-') ?></td>
        <td><?= htmlspecialchars($session['status']) ?></td>
        <td>
            <a href="/seat-rows.php?session_id=<?= $session['id'] ?>">Kursi</a>
            <a href="/graduates.php?session_id=<?= $session['id'] ?>">Wisudawan</a>
            <a href="/graduation-session-form.php?id=<?= $session['id'] ?>">Edit</a>
            <a href="/graduation-session-delete.php?id=<?= $session['id'] ?>" class="delete" onclick="return confirm('PERINGATAN: Menghapus sesi ini akan menghapus SEMUA kursi dan wisudawan di dalamnya. Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>