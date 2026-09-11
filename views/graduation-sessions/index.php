<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-400 mb-1">
            <a href="/graduation-events" class="hover:text-its-blue transition-colors">Periode Wisuda</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-bold"><?= htmlspecialchars($event['name']) ?></span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Sesi Wisuda</h1>
    </div>

    <a href="/graduation-sessions/create?event_id=<?= $event['id'] ?>"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Tambah Sesi Baru
    </a>
</div>

<div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
    <div class="p-5 bg-white border-b border-slate-100 flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
            <input type="text" id="searchInput" placeholder="Cari tanggal atau status..."
                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>
        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
            Total: <?= count($sessions ?? []) ?> Sesi
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-800 border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-bold text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center w-12"><input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer"></th>
                    <th class="px-6 py-4 text-left">Tanggal Pelaksanaan</th>
                    <th class="px-6 py-4 text-center">Sesi</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php if (empty($sessions)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400 italic">Belum ada sesi untuk event ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sessions as $session): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 text-center"><input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $session['id'] ?>"></td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?= date('d F Y', strtotime($session['date'])) ?></td>
                            <td class="px-6 py-4 text-center text-slate-700 font-semibold">Sesi <?= htmlspecialchars($session['session'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 text-xs font-bold rounded-md border capitalize <?= $session['status'] === 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                    <?= htmlspecialchars($session['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="/seat-rows?session_id=<?= $session['id'] ?>" class="px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                                    Kelola Kursi
                                </a>
                                <a href="/graduates?session_id=<?= $session['id'] ?>" class="px-3 py-1.5 text-xs font-bold text-its-blue bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                                    Wisudawan
                                </a>
                                <a href="/graduation-sessions/edit?id=<?= $session['id'] ?>" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors border border-slate-200">
                                    Edit
                                </a>
                                <a href="/graduation-sessions/delete?id=<?= $session['id'] ?>" onclick="return confirm('Yakin hapus?')" class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
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