<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="max-w-3xl mx-auto py-6">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Import Data Wisudawan</h1>
            <p class="text-sm text-gray-500 mt-1">Upload data mahasiswa wisudawan menggunakan file CSV (format template).</p>
        </div>
        <a href="/imports/template" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold transition-colors shadow-sm cursor-pointer">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Template CSV
        </a>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
        <h4 class="font-bold mb-1 flex items-center gap-1.5">
            💡 Alur Kerja Import:
        </h4>
        <ol class="list-decimal list-inside space-y-1 text-xs text-amber-900">
            <li>Download file <strong>Template CSV</strong> menggunakan tombol di atas.</li>
            <li>Buka file CSV tersebut di Microsoft Excel / Google Sheets.</li>
            <li>Copy & Paste data kolom dari file Excel Pusat ke kolom template yang sesuai.</li>
            <li>Simpan (Save) file tersebut (tetap pilih format CSV).</li>
            <li>Upload file CSV yang sudah diisi melalui form di bawah ini.</li>
        </ol>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="/imports/process" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId ?? '') ?>">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File CSV (.csv)</label>
                <input type="file" name="file" accept=".csv"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-gray-300 rounded-lg cursor-pointer"
                    required>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="/graduates?session_id=<?= htmlspecialchars($sessionId ?? '') ?>" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Upload &amp; Import
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>