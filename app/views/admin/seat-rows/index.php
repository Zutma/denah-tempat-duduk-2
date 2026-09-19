<nav class="flex items-center gap-2 text-sm font-medium text-slate-400 mb-1">
    <a href="<?= url('graduation-events') ?>" class="hover:text-its-blue transition-colors">Periode Wisuda</a>
    <span class="text-slate-300">/</span>
    <a href="<?= url('graduation-sessions?event_id=' . ($session['graduation_event_id'] ?? '')) ?>" class="hover:text-its-blue transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-700 font-bold">Kelola Kursi</span>
</nav>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            Kelola Kursi — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
        </h1>
        <p class="text-sm text-slate-500 mt-1">Pengaturan baris dan kapasitas kursi untuk sesi wisuda ini.</p>
    </div>
</div>

<?php
    $groupedSeatRows = [];
    $totalSeatCountAll = 0;
    if (!empty($seatRows)) {
        foreach ($seatRows as $sr) {
            $r = $sr['row'];
            if (!isset($groupedSeatRows[$r])) {
                $groupedSeatRows[$r] = [
                    'row' => $r,
                    'left' => null,
                    'right' => null,
                    'total_seats' => 0
                ];
            }
            if ($sr['side'] === 'left') {
                $groupedSeatRows[$r]['left'] = $sr;
                $groupedSeatRows[$r]['total_seats'] += $sr['capacity'];
            } else if ($sr['side'] === 'right') {
                $groupedSeatRows[$r]['right'] = $sr;
                $groupedSeatRows[$r]['total_seats'] += $sr['capacity'];
            }
            $totalSeatCountAll += $sr['seat_count'];
        }
    }
?>

<div x-data='seatRowManager({
        hasErrors: <?= !empty($errors) ? "true" : "false" ?>,
        allLetters: <?= htmlspecialchars(json_encode(array_values($allLetters ?? [])), ENT_QUOTES, "UTF-8") ?>,
        usedLetters: <?= htmlspecialchars(json_encode(array_values($usedLetters ?? [])), ENT_QUOTES, "UTF-8") ?>
     })' class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-white flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="searchInput" placeholder="Cari nama baris atau kapasitas..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none" />
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
                <span>Total:</span>
                <span class="text-its-blue-light font-extrabold"><?= count($groupedSeatRows) ?> Baris</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600 font-semibold"><?= $totalSeatCountAll ?> Kursi</span>
            </span>

            <button type="button" @click="toggleBulkForm()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-its-blue-light hover:bg-its-blue rounded-xl shadow-xs transition-all cursor-pointer select-none">
                <svg x-show="!showBulkForm" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <svg x-show="showBulkForm" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                <span x-text="showBulkForm ? 'Tutup Form' : 'Tambah Baris'"></span>
            </button>
        </div>
    </div>

    <div x-show="showBulkForm" x-cloak class="m-5 p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200/60">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Form Input Baris Baru</h4>
            <span class="text-xs text-slate-500">Isi data baris lalu klik simpan</span>
        </div>

        <form method="POST" action="<?= url('seat-rows?session_id=' . $session['id']) ?>">
            <?= Csrf::field() ?>
            <input type="hidden" name="session_id" value="<?= $session['id'] ?>">

            <div class="overflow-x-auto mb-4 border border-slate-200 rounded-xl bg-white shadow-2xs">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-bold text-xs tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 w-56">BARIS</th>
                            <th class="px-5 py-3.5">KAPASITAS KIRI</th>
                            <th class="px-5 py-3.5">KAPASITAS KANAN</th>
                            <th class="px-5 py-3.5 text-center w-24">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in rows" :key="index">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-3">
                                    <select :name="`rows[${index}][row]`" x-model="item.row" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light outline-none cursor-pointer" required>
                                        <option value="" disabled>Pilih Baris</option>
                                        <?php foreach ($allLetters as $letter): ?>
                                            <option value="<?= $letter ?>" :disabled="usedLetters.includes('<?= $letter ?>')">
                                                Baris <?= $letter ?> <?= in_array($letter, $usedLetters ?? []) ? '(Sudah Ada)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="p-3">
                                    <input type="number" :name="`rows[${index}][left_capacity]`" x-model.number="item.left_capacity" placeholder="20" min="0" max="100" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light outline-none" required>
                                </td>
                                <td class="p-3">
                                    <input type="number" :name="`rows[${index}][right_capacity]`" x-model.number="item.right_capacity" placeholder="20" min="0" max="100" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light outline-none" required>
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" @click="removeRow(index)" class="w-9 h-9 inline-flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" @click="addRow()" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-its-blue-light bg-its-blue/5 hover:bg-its-blue/10 border border-its-blue/15 rounded-xl transition-all cursor-pointer">
                    + Tambah Form Baris Lagi
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" @click="cancelBulkForm()" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="rows.length === 0" class="inline-flex items-center gap-2 px-5 py-2.5 bg-its-blue-light hover:bg-its-blue disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                        Simpan Semua
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-800 border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase font-bold text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center w-12"><input type="checkbox" id="selectAll" class="w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer"></th>
                    <th class="px-6 py-4">BARIS</th>
                    <th class="px-6 py-4 text-center">KAPASITAS KIRI</th>
                    <th class="px-6 py-4 text-center">KAPASITAS KANAN</th>
                    <th class="px-6 py-4 text-center">TOTAL KURSI</th>
                    <th class="px-6 py-4 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white" id="seatRowsTableBody">
                <?php if (empty($groupedSeatRows)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 italic">
                            Belum ada data baris kursi. Klik <strong class="text-its-blue-light">+ Tambah Baris</strong> di atas untuk membuat baris baru.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($groupedSeatRows as $rLabel => $rData): ?>
                        <?php
                            $deleteIds = array_filter([$rData['left']['id'] ?? null, $rData['right']['id'] ?? null]);
                            $deleteParam = implode(',', $deleteIds);
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors seat-row-item">
                            <td class="px-5 py-4 text-center"><input type="checkbox" class="rowCheckbox w-4 h-4 text-its-blue-light border-slate-300 rounded cursor-pointer" value="<?= $deleteParam ?>"></td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-its-blue/10 text-its-blue border border-its-blue-sky/30 font-extrabold text-sm">
                                        <?= htmlspecialchars($rLabel) ?>
                                    </span>
                                    <span class="font-extrabold text-slate-800 text-sm">Baris <?= htmlspecialchars($rLabel) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                <?= $rData['left'] ? htmlspecialchars($rData['left']['capacity']) . ' Kursi' : '<span class="text-slate-400 italic font-normal">Tidak ada</span>' ?>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-slate-700">
                                <?= $rData['right'] ? htmlspecialchars($rData['right']['capacity']) . ' Kursi' : '<span class="text-slate-400 italic font-normal">Tidak ada</span>' ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-its-yellow/20 text-its-blue border border-its-yellow/60">
                                    <?= $rData['total_seats'] ?> Kursi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <form method="POST" action="<?= url('seat-rows/delete') ?>" class="inline" onsubmit="return confirm('Yakin menghapus Baris <?= htmlspecialchars($rLabel) ?>?')">
                                     <?= Csrf::field() ?>
                                     <input type="hidden" name="id" value="<?= $deleteParam ?>">
                                     <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
                                     <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200/80 rounded-lg transition-colors cursor-pointer">
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

<form id="bulkDeleteForm" method="POST" action="<?= url('seat-rows/bulk-delete') ?>" class="hidden">
    <?= Csrf::field() ?>
    <input type="hidden" name="session_id" value="<?= $session['id'] ?>">
    <input type="hidden" name="_method" value="DELETE">
</form>
<div id="bulkToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl px-6 py-3 border border-slate-700 hidden items-center gap-5 z-50 transition-all">
    <div class="flex items-center gap-2 text-sm font-medium text-slate-300">
        <span id="selectedCount" class="bg-its-blue-light text-white px-2.5 py-0.5 rounded-full font-bold text-xs">0</span>
        <span>baris terpilih</span>
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

<script src="<?= url('js/admin/seat-row-manager.js') ?>"></script>
<script>
    initSeatRowSearch();
    function submitBulkDelete() {
        submitSeatRowBulkDelete();
    }
</script>