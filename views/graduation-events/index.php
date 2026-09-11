<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Header & Breadcrumb Terpadu -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
            <span class="text-slate-700 font-bold">Periode Wisuda</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Daftar Periode Wisuda</h1>
    </div>
    <a href="/graduation-events/create"
        class="inline-flex items-center gap-2 px-4 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Periode
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-3.5 mb-5 text-xs font-semibold text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200/80 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="p-3.5 mb-5 text-xs font-semibold text-red-800 bg-red-50 rounded-xl border border-red-200/80 flex items-center gap-2">
        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduation-events/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Floating Bulk Action Bar -->
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

<?php if (!empty($events)): ?>
    <div class="mb-5 bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm">
            <input type="text" id="searchInput" placeholder="Cari periode wisuda..."
                class="w-full pl-3.5 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <div class="flex items-center gap-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
                Total: <?= count($events) ?> Periode
            </span>
            <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded focus:ring-its-blue-light cursor-pointer">
                <label for="selectAll" class="text-xs font-bold text-slate-700 cursor-pointer select-none">Pilih Semua</label>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php if (empty($events)): ?>
        <div class="col-span-full text-center py-12 text-slate-400 italic bg-white rounded-2xl border border-slate-200/80">
            Belum ada periode wisuda. Klik "Tambah Periode" untuk membuat yang pertama.
        </div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="relative bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-5 hover:border-its-blue/30 transition-all flex flex-col justify-between">
                <div class="absolute top-4 right-4 z-10">
                    <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded focus:ring-its-blue-light cursor-pointer" value="<?= $event['id'] ?>">
                </div>

                <div>
                    <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="block group">
                        <div class="flex items-start gap-3 mb-2 pr-6">
                            <div class="w-9 h-9 rounded-xl bg-its-blue/10 text-its-blue flex items-center justify-center shrink-0 group-hover:bg-its-blue-light group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-800 group-hover:text-its-blue-light transition-colors line-clamp-2"><?= htmlspecialchars($event['name']) ?></h3>
                                <p class="text-xs font-semibold text-slate-400 mt-0.5"><?= $event['session_count'] ?? 0 ?> Sesi</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="flex items-center justify-end gap-1.5 mt-5 pt-3 border-t border-slate-100">
                    <a href="/graduation-sessions?event_id=<?= $event['id'] ?>"
                        class="px-2.5 py-1.5 text-xs font-bold text-its-blue bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15 mr-auto">
                        Lihat Sesi
                    </a>
                    <a href="/graduation-events/edit?id=<?= $event['id'] ?>"
                        class="px-2.5 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors border border-slate-200">
                        Edit
                    </a>
                    <a href="/graduation-events/delete?id=<?= $event['id'] ?>"
                        onclick="return confirm('Yakin hapus? Semua sesi & data di dalamnya ikut terhapus.')"
                        class="px-2.5 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80">
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
                document.querySelectorAll('.grid > div').forEach(card => {
                    card.style.display = card.textContent.toLowerCase().includes(query) ? '' : 'none';
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
        if (confirm(`Hapus ${checked.length} periode terpilih?`)) {
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