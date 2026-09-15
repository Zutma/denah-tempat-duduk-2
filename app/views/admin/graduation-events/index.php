<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Periode Wisuda</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola seluruh periode acara wisuda ITS.</p>
    </div>
    <a href="/graduation-events/create"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Periode
    </a>
</div>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduation-events/bulk-delete" class="hidden">
    <?= Csrf::field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<!-- Floating Bulk Action Bar -->
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-6 py-3 border border-slate-700 hidden items-center gap-5 z-50 transition-all">
    <div class="flex items-center gap-2 text-sm font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2.5 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>item terpilih</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="submitBulkDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs font-bold transition-colors flex items-center gap-2 shadow-xs cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        <span>Hapus Terpilih</span>
    </button>
    <button type="button" onclick="unselectAll()" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 font-semibold cursor-pointer">
        Batal
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($events)): ?>
        <div class="col-span-full text-center py-12 text-slate-400 italic bg-white rounded-2xl border border-slate-200/80">
            Belum ada periode wisuda. Klik "Tambah Periode" untuk membuat yang pertama.
        </div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-6 hover:border-its-blue/30 transition-all flex flex-col justify-between relative group">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="flex items-start gap-3 flex-1 group">
                            <div class="w-10 h-10 rounded-xl bg-its-blue/10 text-its-blue flex items-center justify-center shrink-0 group-hover:bg-its-blue-light group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-800 group-hover:text-its-blue-light transition-colors line-clamp-2"><?= htmlspecialchars($event['name']) ?></h3>
                                <p class="text-xs font-semibold text-slate-400 mt-1"><?= $event['session_count'] ?? 0 ?> Sesi</p>
                            </div>
                        </a>
                        <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer mt-1" value="<?= $event['id'] ?>">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                    <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="px-3 py-2 text-xs font-bold text-its-blue bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15 mr-auto">
                        Lihat Sesi
                    </a>
                    <a href="/graduation-events/edit?id=<?= $event['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                        Edit
                    </a>
                    <form method="POST" action="/graduation-events/delete" class="inline" onsubmit="return confirm('Yakin menghapus Periode <?= htmlspecialchars(addslashes($event['name'])) ?> beserta seluruh Sesi & Wisudawan di dalamnya?')">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= $event['id'] ?>">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80 cursor-pointer">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="/js/admin/table-utils.js"></script>
<script>
    initBulkTable();
    function submitBulkDelete() {
        submitBulkDeleteForm('bulkDeleteForm', 'Yakin mau hapus {count} periode wisuda terpilih beserta seluruh data Sesi & Wisudawan di dalamnya?');
    }
</script>