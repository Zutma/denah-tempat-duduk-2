<?php require __DIR__ . '/../partials/header.php'; ?>

<div style="max-width:900px; margin:0 auto;">

    <h1 style="text-align:center;">
        <?= $activeSession ? htmlspecialchars($activeSession['event_name'] . ' — Sesi ' . $activeSession['session']) : 'Denah Wisuda' ?>
    </h1>

    <?php if (!empty($publishedSessions)): ?>
        <form method="GET" style="margin-bottom:15px;">
            <select name="session_id" onchange="this.form.submit()" style="width:100%; padding:10px;">
                <option value="">-- Pilih Acara Wisuda --</option>
                <?php foreach ($publishedSessions as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($activeSession && $activeSession['id'] == $s['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['event_name'] . ' — Sesi ' . $s['session'] . ' (' . $s['date'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php endif; ?>

    <?php if (!$activeSession || $message): ?>
        <div style="background:#fff3cd; border:1px solid #ffc107; padding:15px; border-radius:6px; text-align:center;">
            <?= htmlspecialchars($message ?? 'Belum ada data.') ?>
        </div>
    <?php else: ?>

        <form method="GET" style="margin-bottom:10px;">
            <input type="hidden" name="session_id" value="<?= $activeSession['id'] ?>">
            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>"
                   placeholder="Cari nama atau NRP, lalu tekan Enter..."
                   style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
        </form>

        <?php if ($searchQuery !== ''): ?>
            <div style="background:white; border:1px solid #ddd; border-radius:6px; padding:10px; margin-bottom:15px;">
                <p style="font-size:13px; color:#666;">
                    Ditemukan <strong><?= count($searchResults) ?></strong> hasil
                    — <a href="?session_id=<?= $activeSession['id'] ?>">Reset</a>
                </p>
                <?php if (empty($searchResults)): ?>
                    <p style="font-size:14px; color:#999;">Tidak ada hasil untuk "<?= htmlspecialchars($searchQuery) ?>".</p>
                <?php else: ?>
                    <?php foreach ($searchResults as $r): ?>
                        <a href="#seat-<?= $r['seat_id'] ?>" onclick="highlightSeat(<?= $r['seat_id'] ?>)"
                           style="display:flex; justify-content:space-between; padding:8px; border-bottom:1px solid #eee; text-decoration:none; color:inherit;">
                            <span>
                                <strong><?= htmlspecialchars($r['name']) ?></strong><br>
                                <small>NRP: <?= htmlspecialchars($r['nrp']) ?> — <?= htmlspecialchars($r['prodi_name']) ?></small>
                            </span>
                            <span style="background:#e0f2fe; color:#0369a1; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:bold;">
                                <?= $r['row'] ? htmlspecialchars($r['row'] . $r['number']) : 'Belum ada kursi' ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="background:#1e293b; color:white; text-align:center; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:bold; letter-spacing:2px;">
            PANGGUNG UTAMA / REKTORAT
        </div>

        <div style="overflow-x:auto; padding-bottom:15px;">
            <div style="display:flex; justify-content:center; gap:30px; min-width:max-content;">

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ($leftRows as $row): ?>
                        <div style="display:flex; align-items:center; gap:8px; background:white; padding:8px; border-radius:8px; border:1px solid #e2e8f0;">
                            <span style="font-weight:bold; color:#94a3b8; width:20px;"><?= htmlspecialchars($row['row']) ?></span>
                            <div style="display:flex; gap:6px;">
                                <?php foreach ($row['seats'] as $seat): ?>
                                    <?php require __DIR__ . '/_seat-button.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="border-left:2px dashed #cbd5e1;"></div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <?php foreach ($rightRows as $row): ?>
                        <div style="display:flex; align-items:center; gap:8px; background:white; padding:8px; border-radius:8px; border:1px solid #e2e8f0;">
                            <div style="display:flex; gap:6px;">
                                <?php foreach ($row['seats'] as $seat): ?>
                                    <?php require __DIR__ . '/_seat-button.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <span style="font-weight:bold; color:#94a3b8; width:20px;"><?= htmlspecialchars($row['row']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <div id="infoKursi" style="display:none; background:white; border:2px solid #0284c7; border-radius:8px; padding:15px; margin-top:15px;">
            <p><strong>Kursi:</strong> <span id="info-kode"></span></p>
            <p><strong>Nama:</strong> <span id="info-nama"></span></p>
            <p><strong>NRP:</strong> <span id="info-nrp"></span></p>
            <p><strong>Prodi/Fakultas:</strong> <span id="info-prodi"></span></p>
        </div>

    <?php endif; ?>
</div>

<script>
function tampilkanInfo(seatId, kode, nama, nrp, prodi) {
    document.getElementById('infoKursi').style.display = 'block';
    document.getElementById('info-kode').innerText = kode;
    document.getElementById('info-nama').innerText = nama;
    document.getElementById('info-nrp').innerText = nrp;
    document.getElementById('info-prodi').innerText = prodi;
}

function highlightSeat(seatId) {
    const el = document.getElementById('seat-' + seatId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.click();
    }
}
</script>

<?php require __DIR__ . '/../public/header.php'; ?>