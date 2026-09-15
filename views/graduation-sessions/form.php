<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight"><?= !empty($session['id']) ? 'Edit Sesi Wisuda' : 'Tambah Sesi Wisuda' ?></h1>
        <p class="text-xs text-slate-500 mt-1">Lengkapi informasi tanggal, nomor sesi, dan status publikasi.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200/80 rounded-xl">
        <ul class="list-disc list-inside text-xs font-semibold text-red-700 space-y-1">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="p-6 rounded-2xl shadow-2xs border border-slate-200/80 bg-white w-full">
    <form method="POST" class="space-y-5">
        <?php if (!empty($session['id'])): ?>
            <input type="hidden" name="id" value="<?= $session['id'] ?>">
        <?php else: ?>
            <input type="hidden" name="event_id" value="<?= $eventId ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Sesi <span class="text-red-500">*</span></label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($session['date'] ?? '') ?>" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
            </div>

            <div>
                <label for="session" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sesi Ke-</label>
                <input type="number" id="session" name="session" value="<?= htmlspecialchars($session['session'] ?? '') ?>" placeholder="Contoh: 1, 2, dst."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
            </div>

            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Publikasi <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
                    <option value="draft" <?= ($session['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($session['status'] ?? 'draft') == 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="archived" <?= ($session['status'] ?? 'draft') == 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? $eventId ?>"
                class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                <?= !empty($session['id']) ? 'Update Sesi' : 'Simpan Sesi' ?>
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>