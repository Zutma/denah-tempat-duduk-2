<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight"><?= !empty($faculty['id']) ? 'Edit Fakultas' : 'Tambah Fakultas' ?></h1>
        <p class="text-xs text-slate-500 mt-1">Lengkapi data informasi fakultas dan warna identitas penanda tempat duduk.</p>
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
    <form method="POST" class="flex flex-col gap-5">
        <?php if (!empty($faculty['id'])): ?>
            <input type="hidden" name="id" value="<?= $faculty['id'] ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kode Fakultas <span class="text-red-500">*</span></label>
                <input type="text" id="code" name="code" value="<?= htmlspecialchars($faculty['code'] ?? '') ?>"
                    placeholder="Contoh: FSAD" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light outline-none uppercase placeholder:normal-case transition-all">
            </div>

            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Fakultas <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($faculty['name'] ?? '') ?>"
                    placeholder="Contoh: Fakultas Sains dan Analitika Data" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light outline-none transition-all">
            </div>
        </div>

        <div x-data="{ hexColor: '<?= htmlspecialchars($faculty['color'] ?? '#127BBE') ?>' }">
            <label for="color" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Warna Penanda Kursi <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-3">
                <input type="color" x-model="hexColor"
                    class="h-10 w-12 p-0.5 border border-slate-200 rounded-xl cursor-pointer bg-white shadow-2xs">
                <input type="text" id="color" name="color" x-model="hexColor" placeholder="#127BBE" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light outline-none transition-all">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="/faculties"
                class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                <?= !empty($faculty['id']) ? 'Update Fakultas' : 'Simpan Fakultas' ?>
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>