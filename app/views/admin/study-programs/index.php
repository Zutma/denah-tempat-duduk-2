<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Program Studi</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola daftar program studi dan jenjang pendidikan.</p>
    </div>
    <a href="<?= url('study-programs/create') ?>"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap self-start sm:self-auto">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Program Studi
    </a>
</div>

<form id="bulkDeleteForm" method="POST" action="<?= url('study-programs/bulk-delete') ?>" class="hidden">
    <?= Csrf::field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-6 py-3 border border-slate-700 hidden items-center gap-5 z-50 transition-all">
    <div class="flex items-center gap-2 text-sm font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2.5 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>item terpilih</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="submitBulkDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-full text-xs font-bold transition-colors flex items-center gap-2 shadow-xs cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        Hapus Terpilih
    </button>
    <button type="button" onclick="unselectAll()" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 font-semibold cursor-pointer">
        Batal
    </button>
</div>

<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="searchInput" placeholder="Cari nama prodi atau fakultas..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
            <span>Total:</span>
            <span class="text-its-blue-light font-extrabold"><?= count($studyPrograms ?? []) ?> Program Studi</span>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-800 border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-bold text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center w-12">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer">
                    </th>
                    <th class="px-6 py-4 text-left">Nama Prodi</th>
                    <th class="px-6 py-4 text-center">Jenjang</th>
                    <th class="px-6 py-4 text-left">Fakultas</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($studyPrograms)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400 italic">Belum ada data program studi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($studyPrograms as $sp): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $sp['id'] ?>">
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?= htmlspecialchars($sp['name']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold font-mono rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                                    <?= htmlspecialchars($sp['degree_level']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700"><?= htmlspecialchars($sp['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="<?= url('study-programs/edit?id=' . $sp['id']) ?>"
                                     class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                                     Edit
                                </a>
                                <form method="POST" action="<?= url('study-programs/delete') ?>" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                     <?= Csrf::field() ?>
                                     <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                     <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80 cursor-pointer">
                                         Hapus
                                     </button>
                                 </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= url('js/admin/table-utils.js') ?>"></script>
<script>
    initBulkTable();
    function submitBulkDelete() {
        submitBulkDeleteForm('bulkDeleteForm', 'Yakin mau hapus {count} program studi yang dipilih?');
    }
</script>