<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Program Studi</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola daftar program studi dan jenjang pendidikan.</p>
    </div>
    <a href="/study-programs/create"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 text-white rounded-xl text-sm font-semibold hover:bg-sky-600 active:bg-sky-700 transition-all shadow-sm hover:shadow-md cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Program Studi
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/study-programs/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Floating Bulk Action Bar (Tanpa Menggeser Layout Tabel) -->
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-5 py-3 border border-slate-700 hidden items-center gap-4 z-50 transition-all transform duration-200">
    <div class="flex items-center gap-2 text-xs font-medium text-slate-300">
        <span id="selectedCount" class="bg-sky-500 text-white px-2 py-0.5 rounded-full font-bold text-xs">0</span>
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
            <input type="text" id="searchInput" placeholder="Cari nama prodi atau fakultas..."
                class="w-full pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all outline-none">
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200/60">
                Total: <?= count($studyPrograms ?? []) ?> Program Studi
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-900">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 uppercase font-bold text-[11px] tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-center w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer">
                    </th>
                    <th class="px-6 py-3 text-left">Nama Prodi</th>
                    <th class="px-6 py-3 text-center">Jenjang</th>
                    <th class="px-6 py-3 text-left">Fakultas</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($studyPrograms)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data program studi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($studyPrograms as $sp): ?>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer" value="<?= $sp['id'] ?>">
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($sp['name']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold font-mono rounded-md bg-slate-100 text-slate-800 border border-slate-200/80">
                                    <?= htmlspecialchars($sp['degree_level']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-600"><?= htmlspecialchars($sp['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="/study-programs/edit?id=<?= $sp['id'] ?>"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors border border-sky-200/60">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <a href="/study-programs/delete?id=<?= $sp['id'] ?>"
                                    onclick="return confirm('Yakin hapus?')"
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