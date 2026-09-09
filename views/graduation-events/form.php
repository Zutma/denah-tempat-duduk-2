<?php require __DIR__ . '/../partials/header.php'; ?>

<h1 class="text-xl font-bold text-gray-800 mb-6"><?= !empty($event['id']) ? 'Edit Periode Wisuda' : 'Tambah Periode Wisuda' ?></h1>

<div class="p-6 rounded-xl shadow-sm border border-gray-200 bg-white">
    <form method="POST" class="flex flex-col gap-4">
        <?php if (!empty($event['id'])): ?>
            <input type="hidden" name="id" value="<?= $event['id'] ?>">
        <?php endif; ?>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1"><?= !empty($event['id']) ? 'Nama Periode' : 'Periode Wisuda' ?></label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name'] ?? '') ?>"
                placeholder="Contoh: Wisuda ke-133"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-light focus:border-its-blue-light">
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
            <a href="/graduation-events"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-6 py-2 bg-its-blue-light hover:bg-its-blue text-white rounded-lg text-sm font-medium transition-colors shadow-sm cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <?= !empty($event['id']) ? 'Update' : 'Simpan' ?>
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>