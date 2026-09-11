<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">
            Tambah Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
        </h1>
        <p class="text-xs text-slate-500 mt-1">Lengkapi identitas wisudawan dan alokasikan tempat duduk jika tersedia.</p>
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
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="nrp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NRP / NIM <span class="text-red-500">*</span></label>
                <input type="text" id="nrp" name="nrp" value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>" required
                    placeholder="Contoh: 5025211001"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
            </div>

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required
                    placeholder="Contoh: Ahmad Subagyo"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="faculty_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fakultas <span class="text-red-500">*</span></label>
                <select id="faculty_id" name="faculty_id" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
                    <option value="">-- Pilih Fakultas --</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>" <?= ($_POST['faculty_id'] ?? '') == $faculty['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($faculty['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="study_program_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program Studi <span class="text-red-500">*</span></label>
                <select id="study_program_id" name="study_program_id" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
                    <option value="">-- Pilih Prodi --</option>
                    <?php foreach ($studyPrograms as $sp): ?>
                        <option value="<?= $sp['id'] ?>" <?= ($_POST['study_program_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sp['name']) ?> (<?= htmlspecialchars($sp['degree_level']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label for="seat_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alokasi Kursi (Opsional)</label>
            <select id="seat_id" name="seat_id"
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all outline-none">
                <option value="">-- Belum Ditentukan / Plot Otomatis Nanti --</option>
                <?php foreach ($seats as $seat): ?>
                    <option value="<?= $seat['id'] ?>" <?= ($_POST['seat_id'] ?? '') == $seat['id'] ? 'selected' : '' ?>>
                        Baris <?= htmlspecialchars($seat['row']) ?> <?= $seat['side'] == 'left' ? 'Kiri' : 'Kanan' ?> — No. <?= htmlspecialchars($seat['number'] ?? $seat['position']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="/graduates?session_id=<?= $sessionId ?>"
                class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                Simpan Wisudawan
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>