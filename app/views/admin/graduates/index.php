
<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-sm font-medium text-slate-400 mb-1">
    <a href="<?= url('graduation-events') ?>" class="hover:text-its-blue transition-colors">Periode Wisuda</a>
    <span class="text-slate-300">/</span>
    <a href="<?= url('graduation-sessions?event_id=' . ($session['graduation_event_id'] ?? '')) ?>" class="hover:text-its-blue transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-700 font-bold">Wisudawan</span>
</nav>

<div class="flex items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
        Data Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
    </h1>

    <a href="<?= url('graduates/create?session_id=' . $sessionId) ?>"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Wisudawan
    </a>
</div>

<!-- Import Card Form (Tombol Upload & Import Hijau) -->
<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-5 mb-6">
    <div class="flex items-center justify-between gap-3 mb-3">
        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
            Import Data Wisudawan
            <span class="px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-md">CSV Format</span>
        </h3>
        <a href="<?= url('imports/template') ?>" class="text-xs font-bold text-its-blue hover:underline">Download Template CSV</a>
    </div>

    <form method="POST" action="<?= url('imports/process') ?>" enctype="multipart/form-data" class="flex items-center gap-4">
        <?= Csrf::field() ?>
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">
        <input type="file" name="file" accept=".csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 border border-slate-200 rounded-xl cursor-pointer bg-slate-50" required>
        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-all whitespace-nowrap cursor-pointer shadow-xs">
            Upload &amp; Import
        </button>
    </form>
</div>

<!-- Rekapitulasi Hasil Impor (Logika Terbaru) -->
<?php if (isset($_SESSION['import_success'])): ?>
    <div class="mb-6 p-5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs space-y-3">
        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Hasil Rekapitulasi Impor CSV
        </h3>

        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-lg">
                <?= $_SESSION['import_success'] ?> Data Baru Masuk
            </span>
            <?php if (!empty($_SESSION['import_skipped'])): ?>
                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg">
                    <?= $_SESSION['import_skipped'] ?> Data Duplikat Di-skip
                </span>
            <?php endif; ?>
            <?php if (!empty($_SESSION['import_failed'])): ?>
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-lg">
                    <?= count($_SESSION['import_failed']) ?> Baris Error Sistem
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['import_details'])): ?>
            <div class="mt-2 max-h-40 overflow-y-auto bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs font-mono text-slate-600 space-y-1">
                <?php foreach ($_SESSION['import_details'] as $detail): ?>
                    <p class="text-slate-600">• <?= htmlspecialchars($detail) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php 
        unset($_SESSION['import_success'], $_SESSION['import_skipped'], $_SESSION['import_failed'], $_SESSION['import_details']);
    ?>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="<?= url('graduates/bulk-delete') ?>" class="hidden">
    <?= Csrf::field() ?>
    <input type="hidden" name="session_id" value="<?= $sessionId ?>">
    <input type="hidden" name="page" value="<?= $page ?? 1 ?>">
</form>

<!-- Floating Bulk Action Bar (Gaya Melayang Versi Lama) -->
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-5 py-3 border border-slate-700 hidden items-center gap-4 z-50 transition-all transform duration-200">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>item terpilih</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="submitBulkDelete()" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        Hapus Terpilih
    </button>
    <button type="button" onclick="unselectAll()" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 font-medium cursor-pointer">
        Batal
    </button>
</div>

<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <div class="p-5 bg-white border-b border-slate-100 flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
            <input type="text" id="searchInput" placeholder="Cari NRP, Nama, Fakultas, Prodi..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
            Total: <?= count($graduates ?? []) ?> Wisudawan
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-800 border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-bold text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center w-12"><input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer"></th>
                    <th class="px-6 py-4 text-left">NRP</th>
                    <th class="px-6 py-4 text-left">Nama</th>
                    <th class="px-6 py-4 text-left">Fakultas</th>
                    <th class="px-6 py-4 text-center">Jenjang</th>
                    <th class="px-6 py-4 text-left">Prodi</th>
                    <th class="px-6 py-4 text-center">Baris</th>
                    <th class="px-6 py-4 text-center">Sisi</th>
                    <th class="px-6 py-4 text-center">Kursi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($graduates)): ?>
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-sm text-slate-400 italic">Belum ada data wisudawan untuk sesi ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($graduates as $g): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 text-center"><input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $g['id'] ?>"></td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-800"><?= htmlspecialchars($g['nrp']) ?></td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?= htmlspecialchars($g['name']) ?></td>
                            <td class="px-6 py-4 font-semibold text-slate-700"><?= htmlspecialchars($g['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold font-mono rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    <?= htmlspecialchars($g['degree_level'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700"><?= htmlspecialchars($g['prodi_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center font-bold text-slate-800"><?= htmlspecialchars($g['row'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if (!empty($g['side'])): ?>
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded <?= $g['side'] == 'left' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>">
                                        <?= $g['side'] == 'left' ? 'Kiri' : 'Kanan' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-800"><?= htmlspecialchars($g['number'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form method="POST" action="<?= url('graduates/delete') ?>" class="inline" onsubmit="return confirm('Yakin hapus data wisudawan ini?')">
                                     <?= Csrf::field() ?>
                                     <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                     <input type="hidden" name="session_id" value="<?= $sessionId ?>">
                                     <input type="hidden" name="page" value="<?= $page ?? 1 ?>">
                                     <button type="submit" class="px-3.5 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80 cursor-pointer">
                                         Hapus
                                     </button>
                                 </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Stabil (Logika Terbaru) -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-white">
            <p class="text-xs font-medium text-slate-500">
                Halaman <span class="font-bold text-slate-700"><?= $page ?></span> dari <span class="font-bold text-slate-700"><?= $totalPages ?></span>
            </p>
            <div class="flex items-center gap-1.5">
                <?php if ($page > 1): ?>
                    <a href="<?= url('graduates?session_id=' . $sessionId . '&page=' . ($page - 1)) ?>"
                        class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                        ← Sebelumnya
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= url('graduates?session_id=' . $sessionId . '&page=' . $i) ?>"
                        class="w-8 h-8 inline-flex items-center justify-center text-xs font-bold rounded-lg transition-colors border
                            <?= $i == $page
                                ? 'bg-its-blue-light text-white border-its-blue-light'
                                : 'text-slate-600 bg-white hover:bg-slate-100 border-slate-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('graduates?session_id=' . $sessionId . '&page=' . ($page + 1)) ?>"
                        class="px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                        Selanjutnya →
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= url('js/admin/table-utils.js') ?>"></script>
<script>
    initBulkTable();
    function submitBulkDelete() {
        submitBulkDeleteForm('bulkDeleteForm', 'Yakin mau hapus {count} data wisudawan yang dipilih?');
    }
</script>