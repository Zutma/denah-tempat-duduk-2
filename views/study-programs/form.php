<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Data Program Studi</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola daftar program studi dan jenjang pendidikan.</p>
    </div>
    <a href="/study-programs/create"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Tambah Program Studi</span>
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-5 text-sm font-semibold text-green-800 bg-green-50 rounded-xl border border-green-200/80 shadow-2xs">
        ✅ <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-4 bg-white">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
            <input type="text" id="searchInput" placeholder="Cari prodi atau fakultas..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
            Total: <?= count($studyPrograms ?? []) ?> Program Studi
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-800 border-collapse">
            <thead class="bg-its-blue/5 border-b border-its-blue/10 text-its-blue uppercase font-bold text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center w-12"><input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer"></th>
                    <th class="px-6 py-4 text-left">NAMA PROGRAM STUDI</th>
                    <th class="px-6 py-4 text-center">JENJANG</th>
                    <th class="px-6 py-4 text-left">FAKULTAS</th>
                    <th class="px-6 py-4 text-right">AKSI</th>
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
                            <td class="px-5 py-4 text-center"><input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $sp['id'] ?>"></td>
                            <td class="px-6 py-4 font-bold text-slate-800 text-sm"><?= htmlspecialchars($sp['name']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 text-xs font-bold font-mono rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                                    <?= htmlspecialchars($sp['degree_level']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600"><?= htmlspecialchars($sp['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="/study-programs/edit?id=<?= $sp['id'] ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/15 rounded-lg transition-colors border border-its-blue/15">
                                    Edit
                                </a>
                                <a href="/study-programs/delete?id=<?= $sp['id'] ?>" onclick="return confirm('Yakin hapus program studi ini?')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80">
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

<?php require __DIR__ . '/../partials/footer.php'; ?>