<h1 class="text-2xl font-bold text-slate-800 tracking-tight mb-6"><?= !empty($session['id']) ? 'Edit Sesi Wisuda' : 'Tambah Sesi Wisuda' ?></h1>

<div class="p-6 rounded-2xl shadow-2xs border border-slate-200/80 bg-white">
    <form method="POST" class="flex flex-col gap-4">
        <?php if (!empty($session['id'])): ?>
            <input type="hidden" name="id" value="<?= $session['id'] ?>">
        <?php else: ?>
            <input type="hidden" name="event_id" value="<?= $eventId ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Sesi</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($session['date'] ?? '') ?>" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light">
            </div>

            <div>
                <label for="session" class="block text-sm font-medium text-slate-700 mb-1">Sesi Ke-</label>
                <input type="number" id="session" name="session" value="<?= htmlspecialchars($session['session'] ?? '') ?>" placeholder="Contoh: 1, 2, dst."
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status Publikasi</label>
                <select id="status" name="status" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light bg-white cursor-pointer">
                    <option value="draft" <?= ($session['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($session['status'] ?? 'draft') == 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="archived" <?= ($session['status'] ?? 'draft') == 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? $eventId ?>"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-6 py-2 bg-its-blue-light hover:bg-its-blue text-white font-medium text-sm rounded-xl shadow-sm transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <?= !empty($session['id']) ? 'Update' : 'Simpan' ?>
            </button>
        </div>
    </form>
</div>

<?php if (!empty($errors)): ?>
    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>