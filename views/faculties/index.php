<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Fakultas</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data fakultas dan identitas warna penanda tempat duduk.</p>
    </div>
    <a href="/faculties/create"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-semibold hover:bg-its-blue transition-all shadow-sm hover:shadow-md cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Fakultas
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/faculties/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
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
            <input type="text" id="searchInput" placeholder="Cari kode atau nama..."
                class="w-full pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light transition-all outline-none">
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-its-blue/5 text-its-blue border border-its-blue/15">
                Total: <?= count($faculties ?? []) ?> Fakultas
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
                    <th class="px-6 py-3 text-left">Kode</th>
                    <th class="px-6 py-3 text-left">Nama Fakultas</th>
                    <th class="px-6 py-3 text-left">Warna</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($faculties)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data fakultas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($faculties as $faculty): ?>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-gray-300 rounded focus:ring-its-blue-light cursor-pointer" value="<?= $faculty['id'] ?>">
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono bg-slate-100 text-slate-800 rounded-md border border-slate-200/80">
                                    <?= htmlspecialchars($faculty['code']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($faculty['name']) ?></td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg">
                                    <span class="inline-block w-3.5 h-3.5 rounded-full border border-slate-300 flex-shrink-0 shadow-xs"
                                        style="background-color: <?= htmlspecialchars($faculty['color']) ?>"></span>
                                    <span class="font-mono text-xs text-slate-600 font-semibold"><?= htmlspecialchars($faculty['color']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="/faculties/edit?id=<?= $faculty['id'] ?>"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <a href="/faculties/delete?id=<?= $faculty['id'] ?>"
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
        if (checked.length === 0) { alert('Pilih minimal satu fakultas!'); return; }
        
        if (confirm(`Yakin mau hapus ${checked.length} fakultas yang dipilih?`)) {
            const form = document.getElementById('bulkDeleteForm');
            // Bersihkan input lama jika ada
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            
            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden'; 
                input.name = 'ids[]'; 
                input.value = cb.value;
                form.appendChild(input);
            });
            form.submit();
        }
    }
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>