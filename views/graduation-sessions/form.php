<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-3">
    <a href="/graduation-events" class="hover:text-its-blue-light transition-colors">Wisuda</a>
    <span class="text-gray-300">/</span>
    <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? $eventId ?>"
        class="hover:text-its-blue-light transition-colors">
        <?= htmlspecialchars($event['name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-800 font-semibold"><?= !empty($session['id']) ? 'Edit Sesi' : 'Tambah Sesi' ?></span>
</nav>

<h1 class="text-xl font-bold text-gray-800 mb-6"><?= !empty($session['id']) ? 'Edit Sesi' : 'Tambah Sesi — ' . htmlspecialchars($event['name'] ?? '') ?></h1>

<div class="p-6 rounded-xl shadow-sm border border-gray-200 bg-white">
    <form method="POST" class="flex flex-col gap-4">
        <?php if (!empty($session['id'])): ?>
            <input type="hidden" name="id" value="<?= $session['id'] ?>">
        <?php else: ?>
            <input type="hidden" name="event_id" value="<?= $eventId ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="<?= htmlspecialchars($session['date'] ?? '') ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light bg-white"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sesi Ke- (boleh kosong)</label>
                <input type="number" name="session" value="<?= htmlspecialchars($session['session'] ?? '') ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light bg-white">
                    <option value="draft" <?= ($session['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($session['status'] ?? 'draft') == 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="archived" <?= ($session['status'] ?? 'draft') == 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="p-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? $eventId ?>"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors"><svg
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>Batal</a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-6 py-2 bg-its-blue-light hover:bg-its-blue text-white rounded-lg text-sm font-medium transition-colors shadow-sm cursor-pointer"><svg
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <?= !empty($session['id']) ? 'Update' : 'Simpan' ?>
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>