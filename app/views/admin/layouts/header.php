<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Portal - Sistem Wisuda ITS') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Work Sans', 'sans-serif'] },
                    colors: {
                        'its-blue': '#233F7C',
                        'its-blue-light': '#127BBE',
                        'its-blue-sky': '#75BDE0',
                        'its-yellow': '#FDBB16',
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 flex h-screen overflow-hidden">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Container Toast Notifikasi Global (Muncul otomatis jika Controller mengirim pesan) -->
<div class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">
    <?php if (isset($_SESSION['success'])): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-cloak
             class="pointer-events-auto p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-lg flex items-center justify-between transition-all">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-semibold"><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 font-bold ml-2">×</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="pointer-events-auto p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-lg flex items-center justify-between transition-all">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-semibold"><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
            <button @click="show = false" class="text-red-500 hover:text-red-700 font-bold ml-2">×</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</div>