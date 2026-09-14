<h1 class="text-2xl font-bold text-slate-800 tracking-tight mb-6">
    Tambah Wisudawan — Sesi <?= !empty($session['date']) ? date('d F Y', strtotime($session['date'])) : '' ?>
</h1>

<script>
    window.allStudyProgramsData = <?= json_encode($studyPrograms ?? []) ?>;
</script>

<div class="p-6 rounded-2xl shadow-2xs border border-slate-200/80 bg-white"
     x-data="{ 
         selectedFaculty: '<?= $_POST['faculty_id'] ?? '' ?>',
         selectedProdi: '<?= $_POST['study_program_id'] ?? '' ?>',
         allStudyPrograms: window.allStudyProgramsData || [],
         get filteredStudyPrograms() {
             if (!this.selectedFaculty) return [];
             return this.allStudyPrograms.filter(sp => String(sp.faculty_id) === String(this.selectedFaculty));
         }
     }">
    <form method="POST" class="flex flex-col gap-4">
        <?= Csrf::field() ?>
        <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId ?? '') ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="nrp" class="block text-sm font-medium text-slate-700 mb-1">NRP / NIM <span class="text-red-500">*</span></label>
                <input type="text" id="nrp" name="nrp" value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>" required
                    placeholder="Contoh: 5025211001"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light">
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required
                    placeholder="Contoh: Ahmad Subagyo"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="faculty_id" class="block text-sm font-medium text-slate-700 mb-1">Fakultas <span class="text-red-500">*</span></label>
                <select id="faculty_id" name="faculty_id" x-model="selectedFaculty" @change="selectedProdi = ''" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light bg-white cursor-pointer">
                    <option value="">-- Pilih Fakultas Dulu --</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>">
                            <?= htmlspecialchars($faculty['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="study_program_id" class="block text-sm font-medium text-slate-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
                <select id="study_program_id" name="study_program_id" x-model="selectedProdi" required
                    :disabled="!selectedFaculty"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light bg-white cursor-pointer disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed">
                    <option value="">-- Pilih Prodi --</option>
                    <template x-for="sp in filteredStudyPrograms" :key="sp.id">
                        <option :value="sp.id" x-text="sp.name + (sp.degree_level ? ' (' + sp.degree_level + ')' : '')"></option>
                    </template>
                </select>
            </div>
        </div>

        <div>
            <label for="seat_id" class="block text-sm font-medium text-slate-700 mb-1">Alokasi Kursi Kosong (Opsional)</label>
            <select id="seat_id" name="seat_id"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light bg-white cursor-pointer">
                <option value="">-- Biarkan Sistem Plot Otomatis Nanti --</option>
                <?php if (empty($seats)): ?>
                    <option value="" disabled>Semua kursi di sesi ini sudah penuh terisi!</option>
                <?php else: ?>
                    <?php foreach ($seats as $seat): ?>
                        <option value="<?= $seat['id'] ?>" <?= ($_POST['seat_id'] ?? '') == $seat['id'] ? 'selected' : '' ?>>
                            Baris <?= htmlspecialchars($seat['row']) ?> <?= $seat['side'] == 'left' ? 'Kiri' : 'Kanan' ?> — No. Kursi <?= htmlspecialchars($seat['number'] ?? $seat['position']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/graduates?session_id=<?= $sessionId ?>"
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
                Simpan Wisudawan
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