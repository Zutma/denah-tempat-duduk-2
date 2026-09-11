<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight"><?= !empty($studyProgram['id']) ? 'Edit Program Studi' : 'Tambah Program Studi' ?></h1>
        <p class="text-xs text-slate-500 mt-1">Lengkapi formulir di bawah ini untuk <?= !empty($studyProgram['id']) ? 'memperbarui data' : 'menambahkan' ?> program studi ke sistem.</p>
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
        <?php if (!empty($studyProgram['id'])): ?>
            <input type="hidden" name="id" value="<?= $studyProgram['id'] ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="faculty_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fakultas <span class="text-red-500">*</span></label>
                <select id="faculty_id" name="faculty_id" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
                    <option value="">-- Pilih Fakultas --</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>"
                            <?= (isset($studyProgram['faculty_id']) && $studyProgram['faculty_id'] == $faculty['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($faculty['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="degree_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                <input type="text" id="degree_level" name="degree_level" value="<?= htmlspecialchars($studyProgram['degree_level'] ?? '') ?>" required
                    placeholder="Contoh: S1, S2, S3, D4"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 uppercase placeholder:normal-case focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
            </div>
        </div>

        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Program Studi <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($studyProgram['name'] ?? '') ?>" required
                placeholder="Contoh: Teknik Informatika"
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="/study-programs"
                class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                <?= !empty($studyProgram['id']) ? 'Update Program Studi' : 'Simpan Program Studi' ?>
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>