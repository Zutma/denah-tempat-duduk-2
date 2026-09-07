<?php require __DIR__ . '/../partials/header.php'; ?>

<h1 class="text-xl font-bold text-gray-800 mb-6"><?= !empty($faculty['id']) ? 'Edit' : 'Tambah' ?> Fakultas</h1>

<div class="p-6 rounded-xl shadow-sm border border-gray-200 bg-white">
    <form method="POST" class="flex flex-col gap-4">
        <?php if (!empty($faculty['id'])): ?>
            <input type="hidden" name="id" value="<?= $faculty['id'] ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                <input type="text" id="code" name="code" value="<?= htmlspecialchars($faculty['code'] ?? '') ?>"
                    placeholder="Contoh: FSAD"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 uppercase placeholder:normal-case font-semibold">
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($faculty['name'] ?? '') ?>"
                    placeholder="Contoh: Fakultas Sains dan Analitika Data"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <div x-data="{ hexColor: '<?= htmlspecialchars($faculty['color'] ?? '#ffffff') ?>' }">
            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Warna Hex (opsional)</label>
            <div class="flex items-center space-x-2">
                <input type="color" x-model="hexColor"
                    class="h-9 w-10 p-0.5 border border-gray-300 rounded-lg cursor-pointer bg-white">
                <input type="text" id="color" name="color" x-model="hexColor" placeholder="#ffffff"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/faculties"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-6 py-2 bg-sky-500 hover:bg-sky-600 text-white font-medium text-sm rounded-lg shadow-sm transition-colors">
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

<?php require __DIR__ . '/../partials/footer.php'; ?>