<?php require __DIR__ . '/../partials/header.php'; ?>

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

<div class="grid grid-cols-3 gap-6">
    <?php if (empty($events)): ?>
        <div class="col-span-full text-center py-12 text-slate-400 italic bg-white rounded-2xl border border-slate-200/80">
            Belum ada periode wisuda. Klik "Tambah Periode" untuk membuat yang pertama.
        </div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-6 hover:border-its-blue/30 transition-all flex flex-col justify-between">
                <div>
                    <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="block group">
                        <div class="flex items-start gap-4 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-its-blue/10 text-its-blue flex items-center justify-center shrink-0 group-hover:bg-its-blue-light group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-slate-800 group-hover:text-its-blue-light transition-colors line-clamp-2"><?= htmlspecialchars($event['name']) ?></h3>
                                <p class="text-xs font-semibold text-slate-400 mt-1"><?= $event['session_count'] ?? 0 ?> Sesi</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                    <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="px-3 py-2 text-xs font-bold text-its-blue bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15 mr-auto">
                        Lihat Sesi
                    </a>
                    <a href="/graduation-events/edit?id=<?= $event['id'] ?>" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors border border-slate-200">
                        Edit
                    </a>
                    <a href="/graduation-events/delete?id=<?= $event['id'] ?>" onclick="return confirm('Yakin hapus?')" class="px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/80">
                        Hapus
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>