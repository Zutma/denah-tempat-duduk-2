<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-3">
    <a href="/graduation-events" class="hover:text-its-blue-light transition-colors">Wisuda</a>
    <span class="text-gray-300">/</span>
    <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? '' ?>" class="hover:text-its-blue-light transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-800 font-semibold">Data Wisudawan</span>
</nav>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Data Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
        </h1>
        <p class="text-sm text-gray-500 mt-1">Daftar mahasiswa wisudawan terdaftar pada sesi ini.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="/graduates/create?session_id=<?= $sessionId ?>"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-semibold hover:bg-its-blue transition-all shadow-sm hover:shadow-md cursor-pointer whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
            </svg>
            Tambah Wisudawan
        </a>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-800 bg-green-100/90 rounded-xl border border-green-200 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['import_warnings'])): ?>
    <div class="p-4 mb-4 text-sm text-amber-800 bg-amber-50 rounded-xl border border-amber-200 shadow-sm">
        <div class="flex items-center gap-2 font-bold mb-1 text-amber-900">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span>Perhatian / Warning Pemetaan Data:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 text-xs text-amber-800">
            <?php foreach ($_SESSION['import_warnings'] as $w): ?>
                <li><?= htmlspecialchars($w) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['import_warnings']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['import_info'])): ?>
    <div class="p-4 mb-4 text-sm text-sky-800 bg-sky-50 rounded-xl border border-sky-200 shadow-sm">
        <div class="flex items-center gap-2 font-bold mb-1 text-sky-900">
            <svg class="w-5 h-5 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Informasi Smart Matching (Otomatis Cocok):</span>
        </div>
        <ul class="list-disc list-inside space-y-1 text-xs text-sky-800">
            <?php foreach ($_SESSION['import_info'] as $info): ?>
                <li><?= htmlspecialchars($info) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['import_info']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['import_failed'])): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-xl border border-red-200 shadow-sm">
        <?php $failed = $_SESSION['import_failed']; unset($_SESSION['import_failed']); ?>
        <?php if (is_array($failed) && count($failed) > 0): ?>
            <p class="font-semibold mb-1 flex items-center gap-2 text-red-900">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Beberapa data gagal di-import / posisi kursi bermasalah:</span>
            </p>
            <ul class="list-disc list-inside space-y-1 text-xs text-red-800">
                <?php foreach ($failed as $f): ?>
                    <li><?= htmlspecialchars($f) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= htmlspecialchars($failed) ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- IMPORT FORM (inline) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
        <div>
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <span>📥 Import Data Wisudawan</span>
                <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">CSV File</span>
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Download template CSV, copy-paste data dari Excel Pusat ke template, lalu upload kembali file CSV tersebut.
            </p>
        </div>
        <a href="/imports/template" 
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold transition-colors shrink-0 shadow-sm cursor-pointer">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Template CSV
        </a>
    </div>

    <form method="POST" action="/imports/process" enctype="multipart/form-data"
        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">
        <input type="file" name="file" accept=".csv"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-gray-300 rounded-lg cursor-pointer"
            required>
        <button type="submit"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors whitespace-nowrap cursor-pointer shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            Upload &amp; Import
        </button>
    </form>
</div>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduates/bulk-delete" class="hidden">
    <input type="hidden" name="session_id" value="<?= $sessionId ?>">
    <input type="file" class="hidden">
    <input type="hidden" name="page" value="<?= $page ?>">
</form>

<!-- Floating Bulk Action Bar (Tanpa Menggeser Layout Tabel) -->
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-5 py-3 border border-slate-700 hidden items-center gap-4 z-50 transition-all transform duration-200">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>item terpilih</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="submitBulkDelete()" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        Hapus Terpilih
    </button>
    <button type="button" onclick="unselectAll()" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 font-medium cursor-pointer">
        Batal
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Card Header Control Bar -->
    <div class="px-5 py-4 bg-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" id="searchInput" placeholder="Cari NRP, Nama, Fakultas, Prodi..."
                class="w-full pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light transition-all outline-none">
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-its-blue/5 text-its-blue border border-its-blue/15">
                Total: <?= count($graduates ?? []) ?> Wisudawan
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-900">
            <thead class="bg-its-blue/5 border-b border-its-blue/10 text-its-blue uppercase font-bold text-[11px] tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-center w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-gray-300 rounded focus:ring-its-blue-light cursor-pointer">
                    </th>
                    <th class="px-6 py-3 text-left">NRP</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Fakultas</th>
                    <th class="px-6 py-3 text-center">Jenjang</th>
                    <th class="px-6 py-3 text-left">Prodi</th>
                    <th class="px-6 py-3 text-center">Baris</th>
                    <th class="px-6 py-3 text-center">Sisi</th>
                    <th class="px-6 py-3 text-center">No. Kursi (Lokal)</th>
                    <th class="px-6 py-3 text-center">No. Kursi (Global)</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($graduates)): ?>
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-gray-400 italic">
                            Belum ada data wisudawan untuk sesi ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($graduates as $g): ?>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-gray-300 rounded focus:ring-its-blue-light cursor-pointer" value="<?= $g['id'] ?>">
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-800"><?= htmlspecialchars($g['nrp']) ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-900"><?= htmlspecialchars($g['name']) ?></td>
                            <td class="px-6 py-4 font-medium text-gray-700"><?= htmlspecialchars($g['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold font-mono rounded-md bg-slate-100 text-slate-800 border border-slate-200/80">
                                    <?= htmlspecialchars($g['degree_level'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700"><?= htmlspecialchars($g['prodi_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800"><?= htmlspecialchars($g['row'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if (!empty($g['side'])): ?>
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-md <?= $g['side'] == 'left' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>">
                                        <?= $g['side'] == 'left' ? 'Kiri' : 'Kanan' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 font-medium"><?= htmlspecialchars($g['position'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-800"><?= htmlspecialchars($g['number'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="/graduates/delete?id=<?= $g['id'] ?>&session_id=<?= $sessionId ?>&page=<?= $page ?>"
                                    onclick="return confirm('Yakin hapus data wisudawan ini?')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
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
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center gap-1 flex-wrap">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="px-3 py-1.5 text-xs font-bold bg-its-blue-light text-white rounded-md"><?= $i ?></span>
                <?php else: ?>
                    <a href="?session_id=<?= $sessionId ?>&page=<?= $i ?>"
                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
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
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        window.updateToolbarState = function() {
            const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
            
            if (checkedCount > 0) {
                bulkToolbar.classList.remove('hidden');
                bulkToolbar.classList.add('flex');
                selectedCount.textContent = checkedCount;
            } else {
                bulkToolbar.classList.remove('flex');
                bulkToolbar.classList.add('hidden');
            }
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

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    selectAll.checked = Array.from(rowCheckboxes).every(c => c.checked);
                    window.updateToolbarState();
                });
            });
        }
    });

    function submitBulkDelete() {
        const checked = document.querySelectorAll('.rowCheckbox:checked');
        if (checked.length === 0) { alert('Pilih minimal satu data wisudawan!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} data wisudawan yang dipilih?`)) {
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