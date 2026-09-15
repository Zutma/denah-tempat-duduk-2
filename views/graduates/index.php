<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-400 mb-1">
            <a href="/graduation-events" class="hover:text-its-blue transition-colors">Periode Wisuda</a>
            <span class="text-slate-300">/</span>
            <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? '' ?>" class="hover:text-its-blue transition-colors">
                <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-bold">Wisudawan</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            Data Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
        </h1>
    </div>

    <a href="/graduates/create?session_id=<?= $sessionId ?>"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Wisudawan
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-5 text-sm font-semibold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200/80 flex items-center gap-2">
        ✅ <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Import Card Form -->
<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-5 mb-6">
    <div class="flex items-center justify-between gap-3 mb-3">
        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
            Import Data Wisudawan
            <span class="px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-md">CSV Format</span>
        </h3>
        <a href="/imports/template" class="text-xs font-bold text-its-blue hover:underline">Download Template CSV</a>
    </div>

    <form method="POST" action="/imports/process" enctype="multipart/form-data" class="flex items-center gap-4">
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">
        <input type="file" name="file" accept=".csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 border border-slate-200 rounded-xl cursor-pointer bg-slate-50" required>
        <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition-all whitespace-nowrap cursor-pointer shadow-xs">
            Import CSV
        </button>
    </form>
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
                                <a href="/graduates/delete?id=<?= $g['id'] ?>&session_id=<?= $sessionId ?>&page=<?= $page ?>" onclick="return confirm('Yakin hapus data wisudawan ini?')" class="px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                document.querySelectorAll('tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
                });
            });
        }
    });
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>