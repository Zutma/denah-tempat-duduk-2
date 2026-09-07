<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-3">
    <a href="/graduation-events" class="hover:text-sky-600 transition-colors">Wisuda</a>
    <span class="text-gray-300">/</span>
    <a href="/graduation-sessions?event_id=<?= $session['graduation_event_id'] ?? '' ?>"
        class="hover:text-sky-600 transition-colors">
        <?= htmlspecialchars($session['event_name'] ?? 'Detail Event') ?>
    </a>
    <span class="text-gray-300">/</span>
    <a href="/graduates?session_id=<?= $sessionId ?>" class="hover:text-sky-600 transition-colors">Data Wisudawan</a>
    <span class="text-gray-300">/</span>
    <span class="text-gray-800 font-semibold">Tambah</span>
</nav>

<h1 class="text-xl font-bold text-gray-800 mb-6">
    Tambah Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
</h1>

<div class="max-w-screen-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <?php if (!empty($errors)): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="session_id" value="<?= $sessionId ?>">

        <div class="mb-4">
            <label for="nrp" class="block text-sm font-medium text-gray-700 mb-1">NRP</label>
            <input type="text" id="nrp" name="nrp" value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>"
                placeholder="Contoh: 5025211001"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 outline-none"
                required>
        </div>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                placeholder="Nama: Malik Gntg"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 outline-none"
                required>
        </div>

        <div class="mb-4">
            <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
            <select id="faculty_id" name="faculty_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 outline-none"
                required>
                <option value="">-- Pilih Fakultas --</option>
                <?php foreach ($faculties as $faculty): ?>
                    <option value="<?= $faculty['id'] ?>" <?= ($_POST['faculty_id'] ?? '') == $faculty['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($faculty['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="study_program_id" class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
            <select id="study_program_id" name="study_program_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 outline-none"
                required>
                <option value="">-- Pilih Prodi --</option>
                <?php foreach ($studyPrograms as $sp): ?>
                    <option value="<?= $sp['id'] ?>" <?= ($_POST['study_program_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sp['name']) ?> (<?= htmlspecialchars($sp['degree_level']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-6">
            <label for="seat_id" class="block text-sm font-medium text-gray-700 mb-1">Kursi (Opsional)</label>
            <select id="seat_id" name="seat_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 outline-none">
                <option value="">-- Belum Ditentukan --</option>
                <?php foreach ($seats as $seat): ?>
                    <option value="<?= $seat['id'] ?>" <?= ($_POST['seat_id'] ?? '') == $seat['id'] ? 'selected' : '' ?>>
                        Baris <?= htmlspecialchars($seat['row']) ?> <?= $seat['side'] == 'left' ? 'Kiri' : 'Kanan' ?> — No. <?= htmlspecialchars($seat['number'] ?? $seat['position']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
            <a href="/graduates?session_id=<?= $sessionId ?>"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-500 text-white rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                Simpan
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>