<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-3">
    <a href="/graduation-events" class="hover:text-sky-600 transition-colors">Wisuda</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-800 font-semibold"><?= htmlspecialchars($event['name']) ?></span>
</nav>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($event['name']) ?></h1>
    <a href="/graduation-sessions/create?event_id=<?= $event['id'] ?>"
        class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Sesi
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduation-sessions/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 border-b border-gray-200 bg-white flex justify-end items-center">
        <button type="button" onclick="submitBulkDelete()" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-md text-xs font-semibold hover:bg-red-100 hover:text-red-700 transition-colors">
            Hapus Terpilih
        </button>
    </div>

    <table class="w-full text-sm text-left text-gray-900">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 uppercase font-semibold text-xs">
            <tr>
                <th class="px-4 py-3 text-center w-10">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer">
                </th>
                <th class="px-6 py-3 text-left">Tanggal</th>
                <th class="px-6 py-3 text-center">Sesi Ke-</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($sessions)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada sesi untuk event ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($sessions as $session): ?>
                    <?php
                        $badgeClass = match($session['status']) {
                            'published' => 'bg-green-100 text-green-700 border-green-200',
                            'archived'  => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            default     => 'bg-gray-100 text-gray-700 border-gray-200',
                        };
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" class="rowCheckbox w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer" value="<?= $session['id'] ?>">
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <?= date('d F Y', strtotime($session['date'])) ?>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600"><?= htmlspecialchars($session['session'] ?? '-') ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-md border capitalize <?= $badgeClass ?>">
                                <?= htmlspecialchars($session['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            <a href="/seat-rows?session_id=<?= $session['id'] ?>" class="text-gray-600 hover:text-gray-900 font-medium">🪑 Kursi</a>
                            <a href="/graduates?session_id=<?= $session['id'] ?>" class="text-gray-600 hover:text-gray-900 font-medium">🎓 Wisudawan</a>
                            <a href="/graduation-sessions/edit?id=<?= $session['id'] ?>" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                            <a href="/graduation-sessions/delete?id=<?= $session['id'] ?>"
                                onclick="return confirm('Yakin hapus? Semua kursi dan wisudawan di dalamnya ikut terhapus.')"
                                class="text-red-600 hover:text-red-800 font-medium">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
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
        if (checked.length === 0) { alert('Pilih minimal satu sesi!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} sesi terpilih?`)) {
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