<h1 class="text-2xl font-bold text-slate-800 tracking-tight mb-6"><?= !empty($faculty['id']) ? 'Edit Fakultas' : 'Tambah Fakultas' ?></h1>

<div class="p-6 rounded-2xl shadow-2xs border border-slate-200/80 bg-white">
    <form method="POST" class="flex flex-col gap-4">
        <?= Csrf::field() ?>
        <?php if (!empty($faculty['id'])): ?>
            <input type="hidden" name="id" value="<?= $faculty['id'] ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="code" class="block text-sm font-medium text-slate-700 mb-1">Kode Fakultas</label>
                <input type="text" id="code" name="code" value="<?= htmlspecialchars($faculty['code'] ?? '') ?>"
                    placeholder="Contoh: FSAD" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light uppercase placeholder:normal-case">
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Fakultas</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($faculty['name'] ?? '') ?>"
                    placeholder="Contoh: Fakultas Sains dan Analitika Data" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light">
            </div>
        </div>

        <div x-data="{ hexColor: '<?= htmlspecialchars($faculty['color'] ?? '#127BBE') ?>' }">
            <label for="color" class="block text-sm font-medium text-slate-700 mb-1">Warna Penanda Kursi</label>
            <div class="flex items-center gap-3 max-w-xs">
                <input type="color" x-model="hexColor"
                    class="h-10 w-14 p-1 border border-slate-300 rounded-lg cursor-pointer bg-white">
                <input type="text" id="color" name="color" x-model="hexColor" placeholder="#127BBE" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/faculties"
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
                <?= !empty($faculty['id']) ? 'Update' : 'Simpan' ?>
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