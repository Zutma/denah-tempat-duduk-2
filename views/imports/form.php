<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Breadcrumb Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
            <a href="/graduation-events" class="hover:text-its-blue transition-colors">Acara Wisuda</a>
            <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></nav>
            <a href="/graduates?session_id=<?= htmlspecialchars($sessionId ?? '') ?>" class="hover:text-its-blue transition-colors">Wisudawan</a>
            <svg class="w-3 h-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></nav>
            <span class="text-slate-600 font-bold">Import CSV</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800 tracking-tight">Import Data Wisudawan</h1>
        <p class="text-xs text-slate-500 mt-0.5">Unggah file CSV sesuai template untuk mengimpor data mahasiswa sekaligus.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="/imports/template" 
            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all border border-slate-200 shadow-2xs">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template CSV
        </a>
    </div>
</div>

<div class="max-w-3xl">
    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 mb-6 text-xs text-amber-900">
        <h4 class="font-bold mb-1.5 flex items-center gap-2 text-amber-950">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            Alur Kerja Import Data:
        </h4>
        <ol class="list-decimal list-inside space-y-1 text-xs text-amber-800 pl-1">
            <li>Download file <strong>Template CSV</strong> menggunakan tombol di atas.</li>
            <li>Buka file CSV tersebut di Microsoft Excel atau Google Sheets.</li>
            <li>Salin data kolom dari file Excel Pusat ke kolom template yang sesuai.</li>
            <li>Simpan file tersebut (tetap menggunakan format CSV).</li>
            <li>Upload file CSV yang telah diisi melalui formulir di bawah.</li>
        </ol>
    </div>

    <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-6">
        <form method="POST" action="/imports/process" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId ?? '') ?>">
            
            <div>
                <label for="file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih File CSV (.csv) <span class="text-red-500">*</span></label>
                <input type="file" id="file" name="file" accept=".csv"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-its-blue/5 file:text-its-blue hover:file:bg-its-blue/15 border border-slate-200 rounded-xl cursor-pointer bg-slate-50"
                    required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/graduates?session_id=<?= htmlspecialchars($sessionId ?? '') ?>" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-its-blue-light text-white rounded-xl text-xs font-bold hover:bg-its-blue transition-all shadow-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    Upload &amp; Import Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>