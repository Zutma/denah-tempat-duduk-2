<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Header & Breadcrumb Terpadu -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
            <a href="/graduation-events" class="hover:text-its-blue transition-colors">Periode Wisuda</a>
            <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? '' ?>" class="hover:text-its-blue transition-colors">
                <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
            </a>
            <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-700 font-bold">Wisudawan</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">
            Data Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
        </h1>
    </div>
    
    <a href="/graduates/create?session_id=<?= $sessionId ?>"
        class="inline-flex items-center gap-2 px-4 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Wisudawan
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-3.5 mb-5 text-xs font-semibold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200/80 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi & Floating Bulk Bar -->
<form id="bulkDeleteForm" method="POST" action="/graduates/bulk-delete" class="hidden">
    <input type="hidden" name="session_id" value="<?= $sessionId ?>">
    <input type="hidden" name="page" value="<?= $page ?>">
</form>

<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-5 py-2.5 border border-slate-700 hidden items-center gap-4 z-50 transition-all">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>item terpilih</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="submitBulkDelete()" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
        Hapus Terpilih
    </button>
    <button type="button" onclick="unselectAll()" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 font-medium cursor-pointer">
        Batal
    </button>
</div>

<!-- Import Card Form -->
<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
        <div>
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                Import Data Wisudawan
                <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-md">CSV Format</span>
            </h3>
        </div>
        <a href="/imports/template" class="text-xs font-bold text-its-blue hover:underline">Download Template CSV</a>
    </div>

    <form method="POST" action="/imports/process" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">
        <input type="file" name="file" accept=".csv" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 border border-slate-200 rounded-xl cursor-pointer bg-slate-50" required>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition-all whitespace-nowrap cursor-pointer shadow-xs">
            Import CSV
        </button>
    </form>
</div>

<!-- Card Tabel Utama -->
<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <div class="p-4 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm">
            <input type="text" id="searchInput" placeholder="Cari NRP, Nama, Fakultas, Prodi..."
                class="w-full pl-3.5 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
                Total: <?= count($graduates ?? []) ?> Wisudawan
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left text-slate-800">
            <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-center w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer">
                    </th>
                    <th class="px-6 py-3 text-left">NRP</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Fakultas</th>
                    <th class="px-6 py-3 text-center">Jenjang</th>
                    <th class="px-6 py-3 text-left">Prodi</th>
                    <th class="px-6 py-3 text-center">Baris</th>
                    <th class="px-6 py-3 text-center">Sisi</th>
                    <th class="px-6 py-3 text-center">Kursi</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($graduates)): ?>
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-slate-400 italic">Belum ada data wisudawan untuk sesi ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($graduates as $g): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $g['id'] ?>">
                            </td>
                            <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-800"><?= htmlspecialchars($g['nrp']) ?></td>
                            <td class="px-6 py-3.5 font-bold text-slate-800"><?= htmlspecialchars($g['name']) ?></td>
                            <td class="px-6 py-3.5 font-semibold text-slate-700"><?= htmlspecialchars($g['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-block px-2 py-0.5 text-[11px] font-bold font-mono rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    <?= htmlspecialchars($g['degree_level'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-700"><?= htmlspecialchars($g['prodi_name'] ?? '-') ?></td>
                            <td class="px-6 py-3.5 text-center font-bold text-slate-800"><?= htmlspecialchars($g['row'] ?? '-') ?></td>
                            <td class="px-6 py-3.5 text-center">
                                <?php if (!empty($g['side'])): ?>
                                    <span class="inline-block px-2 py-0.5 text-[11px] font-bold rounded <?= $g['side'] == 'left' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>">
                                        <?= $g['side'] == 'left' ? 'Kiri' : 'Kanan' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3.5 text-center font-bold text-slate-800"><?= htmlspecialchars($g['number'] ?? '-') ?></td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                <a href="/graduates/delete?id=<?= $g['id'] ?>&session_id=<?= $sessionId ?>&page=<?= $page ?>"
                                    onclick="return confirm('Yakin hapus data wisudawan ini?')"
                                    class="px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center gap-1.5 flex-wrap">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="px-3 py-1.5 text-xs font-bold bg-its-blue-light text-white rounded-lg shadow-2xs"><?= $i ?></span>
                <?php else: ?>
                    <a href="?session_id=<?= $sessionId ?>&page=<?= $i ?>"
                        class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkToolbar = document.getElementById('bulkToolbar');
        const selectedCount = document.getElementById('selectedCount');
        const searchInput = document.getElementById('searchInput');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                document.querySelectorAll('tbody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
                });
            });
        }

        window.updateToolbarState = function() {
            const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
            bulkToolbar.classList.toggle('hidden', checkedCount === 0);
            bulkToolbar.classList.toggle('flex', checkedCount > 0);
            if (selectedCount) selectedCount.textContent = checkedCount;
        };

        window.unselectAll = function() {
            if (selectAll) selectAll.checked = false;
            rowCheckboxes.forEach(cb => cb.checked = false);
            window.updateToolbarState();
        };

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                window.updateToolbarState();
            });
            rowCheckboxes.forEach(cb => cb.addEventListener('change', window.updateToolbarState));
        }
    });

    function submitBulkDelete() {
        const checked = document.querySelectorAll('.rowCheckbox:checked');
        if (checked.length === 0) return;
        if (confirm(`Hapus ${checked.length} data wisudawan terpilih?`)) {
            const form = document.getElementById('bulkDeleteForm');
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'ids[]'; input.value = cb.value;
                form.appendChild(input);
            });
            form.submit();
        }
    }
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>