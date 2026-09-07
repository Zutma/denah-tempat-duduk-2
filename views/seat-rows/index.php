<?php require __DIR__ . '/../partials/header.php'; ?>

<p>
    <a href="/graduation-events">Wisuda</a> &gt;
    Sesi <?= htmlspecialchars($session['date']) ?> &gt;
    Kelola Kursi
</p>

<h1>Kelola Kursi — Sesi <?= htmlspecialchars($session['date']) ?></h1>

<?php if (!empty($messages)): ?>
    <ul style="color:green">
        <?php foreach ($messages as $m): ?><li><?= htmlspecialchars($m) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul class="error-list">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
<?php endif; ?>

<h3>Tambah Baris Baru (Bisa Banyak Sekaligus)</h3>

<form method="POST" id="seatRowForm">
    <table id="barisTable">
        <tr>
            <th>Nama Baris</th>
            <th>Kapasitas Kiri</th>
            <th>Kapasitas Kanan</th>
            <th>Aksi</th>
        </tr>
        <tr class="baris-row">
            <td><input type="text" name="baris[0][row]" maxlength="1"></td>
            <td><input type="number" name="baris[0][kapasitas_kiri]"></td>
            <td><input type="number" name="baris[0][kapasitas_kanan]"></td>
            <td><button type="button" onclick="hapusBaris(this)">Hapus baris ini</button></td>
        </tr>
    </table>

    <button type="button" onclick="tambahBaris()">+ Tambah Baris</button><br><br>
    <button type="submit">Simpan Semua</button>
</form>

<hr>

<h3>Daftar Baris</h3>
<table>
    <tr><th>Baris</th><th>Sisi</th><th>Kapasitas</th><th>Kursi Ter-generate</th><th>Aksi</th></tr>
    <?php foreach ($seatRows as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['row']) ?></td>
        <td><?= $row['side'] == 'left' ? 'Kiri' : 'Kanan' ?></td>
        <td><?= $row['capacity'] ?></td>
        <td><?= $row['seat_count'] ?></td>
        <td>
            <a href="/seat-rows/delete?id=<?= $row['id'] ?>&session_id=<?= $session['id'] ?>"
               class="delete"
               onclick="return confirm('Menghapus baris ini akan menghapus semua kursi di dalamnya. Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<script>
let barisIndex = 1;

function tambahBaris() {
    const table = document.getElementById('barisTable');
    const newRow = document.createElement('tr');
    newRow.className = 'baris-row';
    newRow.innerHTML = `
        <td><input type="text" name="baris[${barisIndex}][row]" maxlength="1"></td>
        <td><input type="number" name="baris[${barisIndex}][kapasitas_kiri]"></td>
        <td><input type="number" name="baris[${barisIndex}][kapasitas_kanan]"></td>
        <td><button type="button" onclick="hapusBaris(this)">Hapus baris ini</button></td>
    `;
    table.appendChild(newRow);
    barisIndex++;
}

function hapusBaris(button) {
    const row = button.closest('tr');
    const table = document.getElementById('barisTable');
    if (table.querySelectorAll('.baris-row').length > 1) {
        row.remove();
    } else {
        alert('Minimal harus ada 1 baris input.');
    }
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>