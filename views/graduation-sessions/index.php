<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Header & Breadcrumb Terpadu -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
            <a href="/graduation-events" class="hover:text-its-blue transition-colors">Periode Wisuda</a>
            <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-700 font-bold"><?= htmlspecialchars($event['name']) ?></span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Daftar Sesi Wisuda</h1>
    </div>
    
    <a href="/graduation-sessions/create?event_id=<?= $event['id'] ?>"
        class="inline-flex items-center gap-2 px-4 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Sesi Baru
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
<form id="bulkDeleteForm" method="POST" action="/graduation-sessions/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
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

<!-- Card Tabel Utama -->
<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <div class="p-4 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm">
            <input type="text" id="searchInput" placeholder="Cari tanggal atau status..."
                class="w-full pl-3.5 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
                Total: <?= count($sessions ?? []) ?> Sesi
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
                    <th class="px-6 py-3 text-left">Tanggal Exec</th>
                    <th class="px-6 py-3 text-center">Sesi</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($sessions)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">Belum ada sesi untuk event ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sessions as $session): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $session['id'] ?>">
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-800">
                                <?= date('d F Y', strtotime($session['date'])) ?>
                            </td>
                            <td class="px-6 py-3.5 text-center text-slate-600 font-semibold">Sesi <?= htmlspecialchars($session['session'] ?? '-') ?></td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-[11px] font-bold rounded-md border capitalize <?= $session['status'] === 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                    <?= htmlspecialchars($session['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="/seat-rows?session_id=<?= $session['id'] ?>" class="px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                                    Kelola Kursi
                                </a>
                                <a href="/graduates?session_id=<?= $session['id'] ?>" class="px-2.5 py-1.5 text-xs font-bold text-its-blue bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                                    Wisudawan
                                </a>
                                <a href="/graduation-sessions/edit?id=<?= $session['id'] ?>" class="px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                                    Edit
                                </a>
                                <a href="/graduation-sessions/delete?id=<?= $session['id'] ?>" onclick="return confirm('Yakin hapus?')" class="px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
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
        if (confirm(`Hapus ${checked.length} sesi terpilih?`)) {
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