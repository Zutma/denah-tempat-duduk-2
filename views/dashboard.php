<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Page Header & Meta Ringkas -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Dashboard Overview</h1>
        <p class="text-xs text-slate-500 mt-0.5">Ringkasan status sesi wisuda dan alokasi tempat duduk.</p>
    </div>
    <!-- Meta Info Sekunder -->
    <div class="flex items-center gap-3 px-3.5 py-2 bg-white rounded-xl border border-slate-200/80 shadow-2xs self-start sm:self-auto">
        <div class="text-xs text-slate-600 font-medium">
            <span class="font-bold text-slate-800"><?= $totalFaculties ?></span> Fakultas
        </div>
        <div class="h-3 w-px bg-slate-200"></div>
        <div class="text-xs text-slate-600 font-medium">
            <span class="font-bold text-slate-800"><?= $totalProdi ?></span> Program Studi
        </div>
    </div>
</div>

<!-- STAT METRICS GRID: STATUS SESI (LOGIS & BERSINKRON) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <!-- Card 1: Draft Sesi -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Sesi Draft</p>
            <h3 class="text-2xl font-extrabold text-slate-800 mt-0.5"><?= $draftSessionsCount ?? 0 ?></h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Sesi belum dipublikasi</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0 font-bold text-sm">
            ✏️
        </div>
    </div>

    <!-- Card 2: Published / Aktif -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Sesi Aktif (Published)</p>
            <h3 class="text-2xl font-extrabold text-slate-800 mt-0.5"><?= $publishedSessionsCount ?? 0 ?></h3>
            <p class="text-[11px] text-slate-500 mt-0.5">
                <span class="font-bold text-slate-700"><?= number_format($activeGraduatesCount ?? 0) ?></span> Wisudawan
            </p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center shrink-0 font-bold text-sm">
            🌐
        </div>
    </div>

    <!-- Card 3: Archived -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs flex items-center justify-between">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sesi Diarsipkan</p>
            <h3 class="text-2xl font-extrabold text-slate-800 mt-0.5"><?= $archivedSessionsCount ?? 0 ?></h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Selesai dilaksanakan</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 border border-slate-200/60 flex items-center justify-center shrink-0 font-bold text-sm">
            📦
        </div>
    </div>
</div>

<!-- SECTION DUA KOLOM: DAFTAR PERIODE & QUICK ACTIONS -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tabel Periode Wisuda (Tanpa Badge Status Periode yang Ambigu) -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden">
        <div class="p-4 bg-white border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Periode Wisuda</h2>
            <a href="/graduation-events" class="text-xs font-bold text-its-blue hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-800 border-collapse">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3">Nama Periode</th>
                        <th class="px-5 py-3 text-center">Jumlah Sesi</th>
                        <th class="px-5 py-3 text-center">Total Wisudawan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($recentEvents)): ?>
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400 italic">Belum ada periode wisuda terdaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentEvents as $e): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-800">
                                    <?= htmlspecialchars($e['name']) ?>
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-slate-600">
                                    <?= $e['session_count'] ?? 0 ?> Sesi
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-slate-700">
                                    <?= number_format($e['graduate_count'] ?? 0) ?> Orang
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <a href="/graduation-sessions?event_id=<?= $e['id'] ?>" class="px-2.5 py-1.5 text-xs font-semibold text-its-blue bg-its-blue/5 hover:bg-its-blue/10 rounded-lg transition-colors border border-its-blue/15">
                                        Kelola Sesi
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex flex-col justify-between">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-4">Akses Cepat</h2>
            <div class="space-y-3">
                <a href="/graduation-events/create" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 hover:bg-its-blue/5 border border-slate-200/80 group transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-its-blue-light/10 text-its-blue-light flex items-center justify-center font-bold text-sm">
                            +
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 group-hover:text-its-blue transition-colors">Buat Periode Wisuda</p>
                            <p class="text-[10px] text-slate-400">Tambah acara wisuda baru</p>
                        </div>
                    </div>
                    <span class="text-slate-400 group-hover:text-its-blue text-xs font-bold transition-colors">→</span>
                </a>

                <a href="/" target="_blank" class="flex items-center justify-between p-3.5 rounded-xl bg-its-blue text-white group transition-all hover:bg-its-blue-light">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center font-bold text-sm">
                            🌐
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Lihat Denah Publik</p>
                            <p class="text-[10px] text-blue-100">Buka denah interaktif</p>
                        </div>
                    </div>
                    <span class="text-white text-xs font-bold">→</span>
                </a>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400 font-medium">Sistem Denah Tempat Duduk Wisuda ITS</p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>