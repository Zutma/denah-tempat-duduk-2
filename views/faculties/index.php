<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-gray-800">Daftar Fakultas</h1>
    <a href="/faculties/create"
        class="inline-flex items-center gap-2 px-3 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Fakultas
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/faculties/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    <!-- Header Aksi Massal -->
    <div class="p-4 border-b border-gray-200 bg-white flex justify-end items-center">
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
                    <th class="px-6 py-3 text-left">Kode</th>
                    <th class="px-6 py-3 text-left">Nama Fakultas</th>
                    <th class="px-6 py-3 text-left">Warna</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($faculties)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data fakultas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($faculties as $faculty): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" class="rowCheckbox w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer" value="<?= $faculty['id'] ?>">
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($faculty['code']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($faculty['name']) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-block w-4 h-4 rounded-full border border-gray-300 flex-shrink-0"
                                        style="background-color: <?= htmlspecialchars($faculty['color']) ?>"></span>
                                    <span class="font-mono text-xs text-gray-600"><?= htmlspecialchars($faculty['color']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                <a href="/faculties/edit?id=<?= $faculty['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                <a href="/faculties/delete?id=<?= $faculty['id'] ?>"
                                    onclick="return confirm('Yakin hapus?')"
                                    class="text-red-600 hover:text-red-800 font-medium">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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
        if (checked.length === 0) { alert('Pilih minimal satu fakultas!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} fakultas yang dipilih?`)) {
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