<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Daftar Event Wisuda</h1>
<a href="/graduation-event-form.php" class="button">+ Tambah Event</a>

<table>
    <tr><th>Nama Event</th><th>Aksi</th></tr>
    <?php foreach ($events as $event): ?>
    <tr>
        <td>
            <a href="/graduation-sessions.php?event_id=<?= $event['id'] ?>">
                <?= htmlspecialchars($event['name']) ?>
            </a>
        </td>
        <td>
            <a href="/graduation-event-form.php?id=<?= $event['id'] ?>">Edit</a>
            <a href="/graduation-event-delete.php?id=<?= $event['id'] ?>" class="delete" onclick="return confirm('PERINGATAN: Menghapus event ini akan menghapus SEMUA sesi, kursi, dan wisudawan di dalamnya. Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require __DIR__ . '/../partials/footer.php'; ?>