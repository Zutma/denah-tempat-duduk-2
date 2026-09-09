<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin - Sistem Wisuda') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Work Sans', 'sans-serif'],
                    },
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
<body class="font-sans antialiased text-gray-900 bg-gray-50 flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <?php
        function isPathActive(string ...$prefixes): bool {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            foreach ($prefixes as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    return true;
                }
            }
            return false;
        }
        function navClass(string ...$prefixes): string {
            return isPathActive(...$prefixes)
                ? 'font-semibold text-white bg-its-blue-light shadow-md'
                : 'text-blue-100 hover:bg-its-blue-light/70';
        }
    ?>
    <aside class="w-64 bg-its-blue text-white flex flex-col justify-between hidden sm:flex flex-shrink-0">
        <div>
            <div class="h-20 flex items-center px-6">
                <img src="/images/logo.png" alt="Logo" class="w-10 h-10 mr-3 object-contain flex-shrink-0">
                <div>
                    <h1 class="font-bold text-sm tracking-wide">Denah Wisuda ITS</h1>
                    <p class="text-xs text-slate-400">Admin Portal</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-4 space-y-1">
                <a href="/dashboard"
                    class="flex items-center px-4 py-3 text-sm rounded-lg transition-colors <?= navClass('/dashboard') ?>">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="/faculties"
                    class="flex items-center px-4 py-3 text-sm rounded-lg transition-colors <?= navClass('/faculties') ?>">
                    <span class="mr-3">👤</span> Data Fakultas
                </a>
                <a href="/study-programs"
                    class="flex items-center px-4 py-3 text-sm rounded-lg transition-colors <?= navClass('/study-programs') ?>">
                    <span class="mr-3">🎓</span> Data Program Studi
                </a>
                <a href="/graduation-events"
                    class="flex items-center px-4 py-3 text-sm rounded-lg transition-colors <?= navClass('/graduation-events', '/graduation-sessions', '/seat-rows', '/graduates') ?>">
                    <span class="mr-3">🏛️</span> Wisuda
                </a>
            </nav>
        </div>

        <div class="p-4">
            <form method="POST" action="/logout">
                <button type="submit"
                    class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-lg transition-colors cursor-pointer">
                    <span class="mr-3">🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- KONTEN HALAMAN AKAN DITAMPILKAN DI SINI -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">