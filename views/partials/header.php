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
<body class="font-sans antialiased text-slate-800 bg-slate-50 flex h-screen overflow-hidden">

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
                ? 'font-bold text-white bg-its-blue-light shadow-sm'
                : 'text-blue-100 hover:bg-its-blue-light/70 hover:text-white font-medium';
        }
    ?>
    <aside class="w-64 bg-its-blue text-white flex flex-col justify-between hidden sm:flex flex-shrink-0 border-r border-its-blue-light/20">
        <div>
            <!-- Header Brand -->
            <div class="h-20 flex items-center px-6 border-b border-its-blue-light/20">
                <img src="/images/logo.png" alt="ITS Logo" class="w-10 h-10 mr-3 object-contain flex-shrink-0 drop-shadow-xs">
                <div>
                    <h1 class="font-bold text-sm tracking-tight leading-snug">Denah Wisuda ITS</h1>
                    <p class="text-[11px] text-its-yellow font-semibold tracking-wider uppercase">Admin Portal</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-4 space-y-1.5">
                <!-- Dashboard -->
                <a href="/dashboard"
                    class="flex items-center px-4 py-2.5 text-xs rounded-xl transition-all <?= navClass('/dashboard') ?>">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Fakultas -->
                <a href="/faculties"
                    class="flex items-center px-4 py-2.5 text-xs rounded-xl transition-all <?= navClass('/faculties') ?>">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0h4"></path>
                    </svg>
                    <span>Data Fakultas</span>
                </a>

                <!-- Program Studi -->
                <a href="/study-programs"
                    class="flex items-center px-4 py-2.5 text-xs rounded-xl transition-all <?= navClass('/study-programs') ?>">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                    <span>Data Program Studi</span>
                </a>

                <!-- Periode Wisuda -->
                <a href="/graduation-events"
                    class="flex items-center px-4 py-2.5 text-xs rounded-xl transition-all <?= navClass('/graduation-events', '/graduation-sessions', '/seat-rows', '/graduates') ?>">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Periode Wisuda</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-its-blue-light/20">
            <form method="POST" action="/logout">
                <button type="submit"
                    class="w-full flex items-center px-4 py-2.5 text-xs font-semibold text-red-300 hover:bg-red-500/15 hover:text-red-200 rounded-xl transition-all cursor-pointer">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Logout Admin</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- KONTEN HALAMAN AKAN DITAMPILKAN DI SINI -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6 md:p-8">