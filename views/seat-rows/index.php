<?php require __DIR__ . '/../partials/header.php'; ?>

<nav class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-4">
    <a href="/graduation-events" class="hover:text-its-blue-light transition-colors">Wisuda</a>
    <span class="text-slate-300">/</span>
    <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? '' ?>" class="hover:text-its-blue-light transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Kelola Kursi</span>
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

<div x-data="seatRowManager()" class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-white flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
            <input type="text" id="searchInput" placeholder="Cari nama baris atau kapasitas..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none" />
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-its-blue/5 text-its-blue border border-its-blue/15">
                <span>Total:</span>
                <span class="text-its-blue-light font-extrabold"><?= count($groupedSeatRows) ?> Baris</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600 font-semibold"><?= $totalSeatCountAll ?> Kursi</span>
            </span>

            <button type="button" @click="toggleBulkForm()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-its-blue hover:bg-its-blue-light rounded-xl shadow-xs transition-all cursor-pointer select-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span x-text="showBulkForm ? 'Tutup Form' : 'Tambah Baris'"></span>
            </button>
        </div>
    </div>

    <div x-show="showBulkForm" x-cloak class="m-5 p-5 bg-slate-50/90 rounded-2xl border border-slate-200/90">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200/60">
            <h4 class="text-sm font-extrabold uppercase tracking-wider text-its-blue">Form Input Baris Baru</h4>
            <span class="text-xs text-slate-500">Isi data baris lalu klik simpan</span>
        </div>

        <form method="POST" action="/seat-rows?session_id=<?= $session['id'] ?>">
            <input type="hidden" name="session_id" value="<?= $session['id'] ?>">

            <div class="overflow-x-auto mb-4 border border-slate-200 rounded-xl bg-white">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-its-blue/5 text-its-blue uppercase text-xs font-extrabold tracking-wider border-b border-its-blue/10">
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
                                    <select :name="`rows[${index}][row]`" x-model="item.row" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 outline-none cursor-pointer" required>
                                        <option value="" disabled>Pilih Baris</option>
                                        <?php foreach ($allLetters as $letter): ?>
                                            <option value="<?= $letter ?>" :disabled="usedLetters.includes('<?= $letter ?>')">
                                                Baris <?= $letter ?> <?= in_array($letter, $usedLetters ?? []) ? '(Sudah Ada)' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="p-3">
                                    <input type="number" :name="`rows[${index}][left_capacity]`" x-model.number="item.left_capacity" placeholder="20" min="1" max="100" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:bg-white outline-none" required>
                                </td>
                                <td class="p-3">
                                    <input type="number" :name="`rows[${index}][right_capacity]`" x-model.number="item.right_capacity" placeholder="20" min="1" max="100" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-semibold text-slate-800 focus:bg-white outline-none" required>
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" @click="removeRow(index)" class="w-9 h-9 inline-flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" @click="addRow()" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-its-blue bg-white hover:bg-its-blue/5 border border-its-blue-sky/40 rounded-xl transition-all cursor-pointer shadow-2xs">
                    + Tambah Form Baris Lagi
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" @click="cancelBulkForm()" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="rows.length === 0" class="inline-flex items-center gap-2 px-6 py-2.5 bg-its-blue hover:bg-its-blue-light disabled:opacity-50 text-white text-xs font-extrabold rounded-xl shadow-xs transition-all cursor-pointer">
                        💾 Simpan Semua
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-800 border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200/80 text-its-blue uppercase font-extrabold text-xs tracking-wider">
                <tr>
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
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-400 italic">
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
                                <a href="/seat-rows/delete?id=<?= $deleteParam ?>&session_id=<?= $session['id'] ?>" onclick="return confirm('Yakin menghapus Baris <?= htmlspecialchars($rLabel) ?>?')" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200/80 rounded-lg transition-colors cursor-pointer">
                                    🗑️ Hapus
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
    function seatRowManager() {
        return {
            showBulkForm: <?= !empty($errors) ? 'true' : 'false' ?>,
            allLetters: <?= json_encode($allLetters) ?>,
            usedLetters: <?= json_encode($usedLetters ?? []) ?>,
            rows: [],
            init() { if (this.showBulkForm && this.rows.length === 0) this.addRow(); },
            toggleBulkForm() { this.showBulkForm = !this.showBulkForm; if (this.showBulkForm && this.rows.length === 0) this.addRow(); },
            cancelBulkForm() { this.showBulkForm = false; this.rows = []; },
            addRow() {
                let lastRow = this.rows.length > 0 ? this.rows[this.rows.length - 1].row : '';
                let currentIndex = lastRow ? this.allLetters.indexOf(lastRow) : -1;
                let nextLetter = '';
                for (let i = currentIndex + 1; i < this.allLetters.length; i++) {
                    let letter = this.allLetters[i];
                    if (!this.usedLetters.includes(letter) && !this.rows.some(r => r.row === letter)) {
                        nextLetter = letter; break;
                    }
                }
                if (!nextLetter) {
                    nextLetter = this.allLetters.find(l => !this.usedLetters.includes(l) && !this.rows.some(r => r.row === l)) || 'A';
                }
                this.rows.push({ row: nextLetter, left_capacity: 20, right_capacity: 20 });
            },
            removeRow(index) {
                this.rows.splice(index, 1);
                if (this.rows.length === 0) this.showBulkForm = false;
            }
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('.seat-row-item').forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        }
    });
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>