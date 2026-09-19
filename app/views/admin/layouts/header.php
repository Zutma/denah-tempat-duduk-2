<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Portal - Sistem Wisuda ITS') ?></title>
    <link rel="stylesheet" href="<?= url('css/tailwind-built.css') ?>">
    <script>window.APP_BASE_URL = '<?= BASE_URL ?>';</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 flex h-screen overflow-hidden">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- notif -->
<div class="fixed top-5 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-3 max-w-md w-full px-4 pointer-events-none">
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

    <?php if (isset($_SESSION['warning'])): ?>
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="pointer-events-auto p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm shadow-lg flex items-center justify-between transition-all">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span class="font-semibold"><?= htmlspecialchars($_SESSION['warning']) ?></span>
            </div>
            <button @click="show = false" class="text-amber-500 hover:text-amber-700 font-bold ml-2">×</button>
        </div>
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>
</div>