<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Wisuda</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola periode acara wisuda dan sesi di dalamnya.</p>
    </div>
    <a href="/graduation-events/create"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-semibold hover:bg-its-blue transition-all shadow-sm hover:shadow-md cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Periode
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduation-events/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Floating Bulk Action Bar (Tanpa Menggeser Layout) -->
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

<?php if (!empty($events)): ?>
    <!-- Top Control Bar (Search, Total Badge, Select All) -->
    <div class="mb-4 bg-white px-4 py-3 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" id="searchInput" placeholder="Cari nama event..."
                class="w-full pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light transition-all outline-none">
        </div>
        <div class="flex items-center gap-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-its-blue/5 text-its-blue border border-its-blue/15">
                Total: <?= count($events) ?> Event Wisuda
            </span>
            <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-gray-300 rounded focus:ring-its-blue-light cursor-pointer">
                <label for="selectAll" class="text-xs font-semibold text-gray-700 cursor-pointer select-none">Pilih Semua</label>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (empty($events)): ?>
        <div class="col-span-full text-center py-12 text-gray-400 italic bg-white rounded-xl border border-gray-200">
            Belum ada event wisuda. Klik "+ Tambah Periode" untuk membuat yang pertama.
        </div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="relative bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
                <!-- Checkbox di Pojok Kanan Atas -->
                <div class="absolute top-4 right-4">
                    <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-gray-300 rounded focus:ring-its-blue-light cursor-pointer" value="<?= $event['id'] ?>">
                </div>

                <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="block group">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-2xl">🏛️</span>
                        <h3 class="font-bold text-gray-800 group-hover:text-its-blue-light transition-colors pr-6"><?= htmlspecialchars($event['name']) ?></h3>
                    </div>
                    <p class="text-xs font-medium text-gray-500 ml-11"><?= $event['session_count'] ?? 0 ?> sesi</p>
                </a>
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                    <a href="/graduation-events/edit?id=<?= $event['id'] ?>"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="/graduation-events/delete?id=<?= $event['id'] ?>"
                        onclick="return confirm('Yakin hapus? Semua sesi & data di dalamnya ikut terhapus.')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
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
                const cards = document.querySelectorAll('.grid > div');
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(query) ? '' : 'none';
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
        if (checked.length === 0) { alert('Pilih minimal satu event!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} event terpilih? (Semua sesi di dalamnya akan ikut terhapus)`)) {
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