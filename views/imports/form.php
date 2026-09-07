<?php require __DIR__ . '/../partials/header.php'; ?>

<h1>Import Data Wisudawan (Format CSV)</h1>

<p><strong>Catatan:</strong> Excel harus di-export ke format CSV dulu (File &gt; Save As &gt; CSV).</p>
<p>Kolom wajib (baris pertama file): <code>fakultas,prodi,jenjang,kursi,sisi,nomor,nrp,nama</code></p>

<form method="POST" action="/imports/process" enctype="multipart/form-data">
    <input type="hidden" name="session_id" value="<?= $sessionId ?>">
    <input type="file" name="file" accept=".csv"><br><br>
    <button type="submit">Upload &amp; Import</button>
</form>

<?php require __DIR__ . '/../partials/footer.php'; ?>