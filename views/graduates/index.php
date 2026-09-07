<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-3">
    <a href="/graduation-events" class="hover:text-sky-600 transition-colors">Wisuda</a>
    <span class="text-gray-300">/</span>
    <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?>" class="hover:text-sky-600 transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-800 font-semibold">Data Wisudawan</span>
</nav>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h1 class="text-xl font-bold text-gray-800">
        Data Wisudawan — Sesi <?= date('d F Y', strtotime($session['date'])) ?>
    </h1>
    <div class="flex items-center gap-2">
        <a href="/graduates/create?session_id=<?= $sessionId ?>"
            class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
            </svg>
            Tambah Wisudawan
        </a>
    </div>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['failed'])): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
        <?php $failed = $_SESSION['failed']; unset($_SESSION['failed']); ?>
        <?php if (is_array($failed) && count($failed) > 0): ?>
            <p class="font-semibold mb-1">Beberapa data gagal di-import / kursi tidak cukup:</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                <?php foreach ($failed as $f): ?>
                    <li><?= htmlspecialchars($f) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= htmlspecialchars($failed) ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- IMPORT FORM (inline, identik dengan Laravel) -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
    <h3 class="text-sm font-semibold text-gray-800 mb-3">📥 Import Data Wisudawan (Excel)</h3>
    <form method="POST" action="/imports/process" enctype="multipart/form-data"
        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">
        <input type="file" name="file" accept=".xlsx,.xls,.csv"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-gray-300 rounded-lg cursor-pointer"
            required>
        <button type="submit"
            class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors whitespace-nowrap">
            Upload &amp; Import
        </button>
    </form>
</div>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduates/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="session_id" value="<?= $sessionId ?>">
</form>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    <div class="p-4 border-b border-gray-200 bg-white flex justify-between items-center">
        <h3 class="text-sm font-semibold text-gray-800">Daftar Wisudawan</h3>
        <button type="button" onclick="submitBulkDelete()" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-md text-xs font-semibold hover:bg-red-100 hover:text-red-700 transition-colors">
            Hapus Terpilih
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-900">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 uppercase font-semibold text-xs">
                <tr>
                    <th class="px-4 py-3 text-center w-10">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer">
                    </th>
                    <th class="px-6 py-3 text-left">NRP</th>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Fakultas</th>
                    <th class="px-6 py-3 text-center">Jenjang</th>
                    <th class="px-6 py-3 text-left">Prodi</th>
                    <th class="px-6 py-3 text-center">Baris</th>
                    <th class="px-6 py-3 text-center">Sisi</th>
                    <th class="px-6 py-3 text-center">No. Kursi (Lokal)</th>
                    <th class="px-6 py-3 text-center">No. Kursi (Global)</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($graduates)): ?>
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-gray-400">
                            Belum ada data wisudawan untuk sesi ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($graduates as $g): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer" value="<?= $g['id'] ?>">
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-800"><?= htmlspecialchars($g['nrp']) ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($g['name']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($g['faculty_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-700 border border-gray-200">
                                    <?= htmlspecialchars($g['degree_level'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($g['prodi_name'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center font-bold text-gray-800"><?= htmlspecialchars($g['row'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if (!empty($g['side'])): ?>
                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded <?= $g['side'] == 'left' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' ?>">
                                        <?= $g['side'] == 'left' ? 'Kiri' : 'Kanan' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600"><?= htmlspecialchars($g['position'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-700"><?= htmlspecialchars($g['number'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="/graduates/delete?id=<?= $g['id'] ?>&session_id=<?= $sessionId ?>"
                                    onclick="return confirm('Yakin hapus data wisudawan ini?')"
                                    class="text-red-600 hover:text-red-800 font-medium">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center gap-1 flex-wrap">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="px-3 py-1.5 text-xs font-bold bg-sky-500 text-white rounded-md"><?= $i ?></span>
                <?php else: ?>
                    <a href="?session_id=<?= $sessionId ?>&page=<?= $i ?>"
                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            });
            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    selectAll.checked = Array.from(rowCheckboxes).every(c => c.checked);
                });
            });
        }
    });
    function submitBulkDelete() {
        const checked = document.querySelectorAll('.rowCheckbox:checked');
        if (checked.length === 0) { alert('Pilih minimal satu data wisudawan!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} data wisudawan yang dipilih?`)) {
            const form = document.getElementById('bulkDeleteForm');
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