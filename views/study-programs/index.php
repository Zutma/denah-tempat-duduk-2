<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Data Program Studi</h1>
        <p class="text-xs text-slate-500 mt-1">Kelola daftar program studi dan jenjang pendidikan.</p>
    </div>
    <a href="/study-programs/create"
        class="inline-flex items-center gap-2 px-4 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Program Studi</span>
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-xs font-semibold text-green-700 bg-green-50 rounded-xl border border-green-200/80 shadow-2xs">
        ✅ <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/study-programs/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Floating Bulk Action Bar -->
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-5 py-2.5 border border-slate-700 hidden items-center gap-4 z-50 transition-all">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>item terpilih</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="submitBulkDelete()" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        <span>Hapus Terpilih</span>
    </button>
    <button type="button" onclick="unselectAll()" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 font-semibold cursor-pointer">
        Batal
    </button>
</div>

<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <!-- Card Header Control Bar -->
    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
        <div class="relative flex-1 max-w-sm">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">🔍</span>
            <input type="text" id="searchInput" placeholder="Cari prodi atau fakultas..."
                class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none shadow-2xs">
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
                <span>Total:</span>
                <span class="text-its-blue-light font-extrabold"><?= count($studyPrograms ?? []) ?> Program Studi</span>
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left text-slate-800 border-collapse">
            <thead class="bg-its-blue/5 border-b border-its-blue/10 text-its-blue uppercase font-bold text-[10px] tracking-wider">
                <tr>
                    <th class="px-4 py-3.5 text-center w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded focus:ring-its-blue-light cursor-pointer">
                    </th>
                    <th class="px-6 py-3.5 text-left">NAMA PROGRAM STUDI</th>
                    <th class="px-6 py-3.5 text-center">JENJANG</th>
                    <th class="px-6 py-3.5 text-left">FAKULTAS</th>
                    <th class="px-6 py-3.5 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($studyPrograms)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-xs text-slate-400 italic">Belum ada data program studi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($studyPrograms as $sp): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded focus:ring-its-blue-light cursor-pointer" value="<?= $sp['id'] ?>">
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?= htmlspecialchars($sp['name']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                                    <?= htmlspecialchars($sp['degree_level']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600"><?= htmlspecialchars($sp['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="/study-programs/edit?id=<?= $sp['id'] ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/15 rounded-lg transition-colors border border-its-blue/15">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <a href="/study-programs/delete?id=<?= $sp['id'] ?>"
                                    onclick="return confirm('Yakin hapus program studi ini?')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Hapus</span>
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
        if (checked.length === 0) { alert('Pilih minimal satu program studi!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} program studi yang dipilih?`)) {
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