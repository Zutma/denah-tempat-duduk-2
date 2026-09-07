<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-bold text-gray-800">Wisuda</h1>
    <a href="/graduation-events/create"
        class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
        </svg>
        Tambah Event
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg border border-green-200">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Form Tersembunyi untuk Bulk Delete -->
<form id="bulkDeleteForm" method="POST" action="/graduation-events/bulk-delete" class="hidden">
    <input type="hidden" name="_method" value="DELETE">
</form>

<?php if (!empty($events)): ?>
    <!-- Baris Aksi Massal untuk Layout Grid -->
    <div class="mb-4 flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center gap-2 px-2">
            <input type="checkbox" id="selectAll" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer">
            <label for="selectAll" class="text-sm font-medium text-gray-700 cursor-pointer">Pilih Semua</label>
        </div>
        <button type="button" onclick="submitBulkDelete()" class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-md text-xs font-semibold hover:bg-red-100 hover:text-red-700 transition-colors">
            Hapus Terpilih
        </button>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (empty($events)): ?>
        <div class="col-span-full text-center py-12 text-gray-400">
            Belum ada event wisuda. Klik "+ Tambah Event" untuk membuat yang pertama.
        </div>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="relative bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <!-- Checkbox di Pojok Kanan Atas -->
                <div class="absolute top-4 right-4">
                    <input type="checkbox" class="rowCheckbox w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500 cursor-pointer" value="<?= $event['id'] ?>">
                </div>

                <a href="/graduation-sessions?event_id=<?= $event['id'] ?>" class="block">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-2xl">🏛️</span>
                        <h3 class="font-semibold text-gray-900 pr-6"><?= htmlspecialchars($event['name']) ?></h3>
                    </div>
                    <p class="text-sm text-gray-500 ml-11"><?= $event['session_count'] ?? 0 ?> sesi</p>
                </a>
                <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                    <a href="/graduation-events/edit?id=<?= $event['id'] ?>"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                        Edit
                    </a>
                    <a href="/graduation-events/delete?id=<?= $event['id'] ?>"
                        onclick="return confirm('Yakin hapus? Semua sesi & data di dalamnya ikut terhapus.')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                        Hapus
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
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
        if (checked.length === 0) { alert('Pilih minimal satu event!'); return; }
        if (confirm(`Yakin mau hapus ${checked.length} event terpilih? (Semua sesi di dalamnya akan ikut terhapus)`)) {
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