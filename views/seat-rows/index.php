<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-3">
    <a href="/graduation-events" class="hover:text-its-blue-light transition-colors">Wisuda</a>
    <span class="text-gray-300">/</span>
    <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? '' ?>" class="hover:text-its-blue-light transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-800 font-semibold">Kelola Kursi</span>
</nav>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Kelola Kursi — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
        </h1>
        <p class="text-sm text-gray-500 mt-1">Pengaturan baris dan kapasitas kursi untuk sesi ini.</p>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
        <ul class="list-disc pl-5 space-y-1">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- FORM BATCH GENERATE (Alpine.js) -->
<div x-data="batchSeatManager()" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
    <div class="flex items-center justify-between gap-4 mb-4">
        <h3 class="text-base font-bold text-gray-800">Buat Baris Kursi</h3>

        <button type="button" @click="addRow()"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-its-blue-light hover:bg-its-blue rounded-xl shadow-sm transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Baris Input</span>
        </button>
    </div>

    <form method="POST" action="/seat-rows?session_id=<?= $session['id'] ?>" x-show="rows.length > 0" x-cloak>
        <input type="hidden" name="session_id" value="<?= $session['id'] ?>">

<div class="px-5 py-4 bg-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
  <div class="relative flex-1 max-w-xs">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
    <input type="text" id="searchInput" placeholder="Cari baris atau kapasitas..."
      class="w-full pl-9 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light transition-all outline-none"/>
  </div>
  <div class="flex items-center gap-2">
    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-its-blue/5 text-its-blue border border-its-blue/15">
      Total: <?= count($seatRows ?? []) ?> Baris
    </span>
  </div>
</div>
        <div class="overflow-x-auto mb-4 border border-gray-200 rounded-xl">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-its-blue/5 text-its-blue uppercase text-[11px] font-bold tracking-wider border-b border-its-blue/10">
                    <tr>
                        <th class="px-4 py-3 w-44">Baris</th>
                        <th class="px-4 py-3">Kapasitas Kiri</th>
                        <th class="px-4 py-3">Kapasitas Kanan</th>
                        <th class="px-4 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="(item, index) in rows" :key="index">
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-3">
                                <select :name="`rows[${index}][row]`" x-model="item.row"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-its-blue-light outline-none transition-all"
                                    required>
                                    <option value="" disabled>Pilih Baris</option>
                                    <?php foreach ($allLetters as $letter): ?>
                                        <option value="<?= $letter ?>"
                                            :disabled="usedLetters.includes('<?= $letter ?>')"
                                            class="<?= in_array($letter, $usedLetters ?? []) ? 'bg-gray-100 text-gray-400 font-normal' : '' ?>">
                                            Baris <?= $letter ?>
                                            <?= in_array($letter, $usedLetters ?? []) ? '(Sudah Ada)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="p-3">
                                <input type="number" :name="`rows[${index}][left_capacity]`"
                                    x-model.number="item.left_capacity" placeholder="20" min="0" max="100"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-800 focus:ring-2 focus:ring-its-blue-light outline-none transition-all"
                                    required>
                            </td>
                            <td class="p-3">
                                <input type="number" :name="`rows[${index}][right_capacity]`"
                                    x-model.number="item.right_capacity" placeholder="20" min="0" max="100"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-800 focus:ring-2 focus:ring-its-blue-light outline-none transition-all"
                                    required>
                            </td>
                            <td class="p-3 text-center whitespace-nowrap">
                                <button type="button" @click="removeRow(index)"
                                    class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 hover:underline transition-colors cursor-pointer">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end pt-3 border-t border-gray-100">
            <button type="submit"
                class="px-4 py-2 bg-its-blue-light hover:bg-its-blue text-white text-sm font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                Simpan Semua Baris
            </button>
        </div>
    </form>
</div>

<!-- TABEL DAFTAR BARIS YANG SUDAH TER-GENERATE -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-sm font-bold text-gray-800">Daftar Baris Kursi</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-900">
            <thead class="bg-its-blue/5 border-b border-its-blue/10 text-its-blue uppercase font-bold text-[11px] tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Baris</th>
                    <th class="px-6 py-3 text-center">Sisi</th>
                    <th class="px-6 py-3 text-center">Kapasitas</th>
                    <th class="px-6 py-3 text-center">Kursi Ter-generate</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($seatRows)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-xs text-gray-400 italic">Belum ada baris kursi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($seatRows as $row): ?>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900"><?= htmlspecialchars($row['row']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-md border <?= $row['side'] == 'left' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' ?>">
                                    <?= $row['side'] == 'left' ? 'Kiri' : 'Kanan' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 font-medium"><?= $row['capacity'] ?></td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-800"><?= $row['seat_count'] ?></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="/seat-rows/delete?id=<?= $row['id'] ?>&session_id=<?= $session['id'] ?>"
                                    onclick="return confirm('Yakin hapus? Semua kursi di baris ini akan terhapus.')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200/60">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
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
    function batchSeatManager() {
        return {
            allLetters: <?= json_encode($allLetters) ?>,
            usedLetters: <?= json_encode($usedLetters ?? []) ?>,
            rows: [],
            init() { this.rows = []; },
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
                    nextLetter = this.allLetters.find(l =>
                        !this.usedLetters.includes(l) && !this.rows.some(r => r.row === l)
                    ) || 'A';
                }
                this.rows.push({ row: nextLetter, left_capacity: 20, right_capacity: 20 });
            },
            removeRow(index) { this.rows.splice(index, 1); }
        }
    }
</script>
<script>
  document.getElementById('searchInput').addEventListener('input', function(){
    const term = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(term) ? '' : 'none';
    });
  });
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>