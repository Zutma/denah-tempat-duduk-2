<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-400 mb-1">
            <a href="/graduation-events" class="hover:text-its-blue transition-colors">Acara Wisuda</a>
            <span class="text-slate-300">/</span>
            <a href="/graduates?session_id=<?= htmlspecialchars($sessionId ?? '') ?>" class="hover:text-its-blue transition-colors">Wisudawan</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600 font-bold">Import CSV</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Import Data Wisudawan</h1>
        <p class="text-sm text-slate-500 mt-1">Unggah file CSV sesuai template untuk mengimpor data mahasiswa sekaligus.</p>
    </div>
    <a href="/imports/template" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-slate-700 hover:bg-slate-50 rounded-xl text-sm font-bold transition-all border border-slate-200 shadow-2xs">
        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Download Template CSV
    </a>
</div>

<div class="max-w-3xl">
    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-5 mb-6 text-sm text-amber-900">
        <h4 class="font-bold mb-2 flex items-center gap-2 text-amber-950 text-base">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            Alur Kerja Import Data:
        </h4>
        <ol class="list-decimal list-inside space-y-1.5 text-sm text-amber-800 pl-1">
            <li>Download file <strong>Template CSV</strong> menggunakan tombol di atas.</li>
            <li>Buka file CSV tersebut di Microsoft Excel atau Google Sheets.</li>
            <li>Salin data kolom dari file Excel Pusat ke kolom template yang sesuai.</li>
            <li>Simpan file tersebut (tetap menggunakan format CSV).</li>
            <li>Upload file CSV yang telah diisi melalui formulir di bawah.</li>
        </ol>
    </div>

    <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-6">
        <form method="POST" action="/imports/process" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId ?? '') ?>">

            <div>
                <label for="file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih File CSV (.csv) <span class="text-red-500">*</span></label>
                <input type="file" id="file" name="file" accept=".csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-its-blue/5 file:text-its-blue hover:file:bg-its-blue/15 border border-slate-200 rounded-xl cursor-pointer bg-slate-50" required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/graduates?session_id=<?= htmlspecialchars($sessionId ?? '') ?>" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-its-blue-light text-white rounded-xl text-sm font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer">
                    Upload &amp; Import Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>