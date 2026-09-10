<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Tempat Duduk Wisuda ITS - Interaktif</title>
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
                        friz: ['Friz Quadrata Std', 'serif']
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
    <style>
        @font-face {
            font-family: 'Friz Quadrata Std';
            src: url('https://www.its.ac.id/wp-content/uploads/2026/01/friz-quadrata-std-medium.woff2') format('woff2');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Set Body agar mendukung Pseudo-element */
        body {
            position: relative;
            background-color: #f8fafc;
        }

        /* Background Pattern Batik PNG */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('/images/Vector.svg'); 
            background-repeat: repeat;
            background-position: center top;
            background-size: 380px auto;
            opacity: 0.12; /* Opasitas 4% (pas & tidak pusing) */
            pointer-events: none;
            z-index: 1; /* Dinaikkan ke 1 agar tidak tertutup background body */
        }

        /* Pastikan Konten Utama Berada Di Atas Layer Pattern (z-index > 1) */
        header, main, footer, div[x-data] {
            position: relative;
            z-index: 2;
        }

        /* Ring Highlight saat kursi dipilih */
        .selected-seat-ring {
            box-shadow: 0 0 0 3px #127BBE, 0 4px 14px rgba(18, 123, 190, 0.4);
            transform: scale(1.05);
            position: relative;
            z-index: 20 !important;
        }

        /* Pulse & Glow Animation saat hasil pencarian */
        @keyframes seatPulse {
            0% {
                box-shadow: 0 0 0 3px #127BBE, 0 0 8px rgba(18, 123, 190, 0.5);
                transform: scale(1.03);
            }
            50% {
                box-shadow: 0 0 0 4px #FDBB16, 0 0 18px rgba(253, 187, 22, 0.85);
                transform: scale(1.08);
            }
            100% {
                box-shadow: 0 0 0 3px #127BBE, 0 0 8px rgba(18, 123, 190, 0.5);
                transform: scale(1.03);
            }
        }

        .search-highlight-seat {
            animation: seatPulse 1.4s infinite ease-in-out;
            position: relative;
            z-index: 25 !important;
        }

        /* Custom Scrollbar Tipis ITS Theme */
        .custom-scrollbar::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #127BBE;
        }
</style>
</head>

<body class="its-key-graphic-bg font-sans antialiased text-slate-800 min-h-screen w-full flex flex-col" x-data="seatMapApp()">

<?php
    $seatMapInfo = [];
    $allGraduatesList = [];
    $leftRows = $leftRows ?? [];
    $rightRows = $rightRows ?? [];

    if (!empty($activeSession)) {
        $globalCounter = 1;
        $rowLabels = [];
        foreach ($leftRows as $lr) { $rowLabels[$lr['row']] = true; }
        foreach ($rightRows as $rr) { $rowLabels[$rr['row']] = true; }
        ksort($rowLabels);

        $leftByRow = [];
        foreach ($leftRows as $lr) { $leftByRow[$lr['row']] = $lr; }
        $rightByRow = [];
        foreach ($rightRows as $rr) { $rightByRow[$rr['row']] = $rr; }

        foreach (array_keys($rowLabels) as $label) {
            if (!empty($leftByRow[$label]['seats'])) {
                foreach ($leftByRow[$label]['seats'] as $s) {
                    $num = $globalCounter++;
                    $seatMapInfo[$s['id']] = [
                        'code' => $label . sprintf('%03d', $num),
                        'num' => $num
                    ];
                }
            }
            if (!empty($rightByRow[$label]['seats'])) {
                foreach ($rightByRow[$label]['seats'] as $s) {
                    $num = $globalCounter++;
                    $seatMapInfo[$s['id']] = [
                        'code' => $label . sprintf('%03d', $num),
                        'num' => $num
                    ];
                }
            }
        }

        $collectGraduates = function($rows) use (&$allGraduatesList, $seatMapInfo) {
            foreach ($rows as $r) {
                if (!empty($r['seats'])) {
                    foreach ($r['seats'] as $s) {
                        if (!empty($s['graduate_name'])) {
                            $facCode = !empty($s['faculty_code']) ? $s['faculty_code'] : ($s['faculty_name'] ?? '-');
                            $allGraduatesList[] = [
                                'seat_id' => (int)$s['id'],
                                'seat_code' => $seatMapInfo[$s['id']]['code'] ?? '-',
                                'name' => $s['graduate_name'],
                                'nrp' => $s['nrp'],
                                'prodi' => $s['prodi_name'] ?? '-',
                                'faculty' => $facCode,
                                'faculty_name' => $s['faculty_name'] ?? '-',
                                'color' => $s['faculty_color'] ?? '#cbd5e1'
                            ];
                        }
                    }
                }
            }
        };
        $collectGraduates($leftRows);
        $collectGraduates($rightRows);
    }
?>

    <!-- TOOLBAR ATAS STICKY -->
    <header class="w-full bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-[98vw] mx-auto px-4 py-2.5 flex items-center justify-between gap-3 flex-nowrap">
            
            <!-- Left: Logo & Wordmark Title -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <img src="/images/logo.png" alt="ITS Logo" class="w-9 h-9 object-contain">
                <div class="h-10 w-px bg-slate-300"></div>
                <div class="flex flex-col justify-center leading-none">
                    <p class="font-friz text-[11px] font-bold uppercase tracking-wider text-its-blue-light mb-1">
                        SISTEM INFORMASI WISUDA
                    </p>
                    <h1 class="font-friz text-base md:text-lg font-bold uppercase text-its-blue tracking-tight">
                        DENAH TEMPAT DUDUK
                    </h1>
                </div>
            </div>

            <!-- Middle-Left: Dropdown Sesi Wisuda -->
            <div class="flex-shrink-0 w-64 md:w-80">
                <form method="GET" id="sessionForm" class="m-0">
                    <select name="session_id" id="session_id" 
                        onchange="if(this.value === '') { window.location.href = window.location.pathname; } else { document.getElementById('sessionForm').submit(); }"
                        class="w-full px-3.5 py-1.5 bg-slate-50 border border-slate-300 focus:border-its-blue-light focus:bg-white rounded-lg text-xs font-bold text-its-blue focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 transition-all cursor-pointer shadow-2xs">
                        
                        <option value="" <?= empty($activeSession) ? 'selected' : '' ?>>
                            -- Pilih Acara Wisuda --
                        </option>

                        <?php if (!empty($publishedSessions)): ?>
                            <?php foreach ($publishedSessions as $session): ?>
                                <option value="<?= $session['id'] ?>"
                                    <?= (!empty($activeSession) && $activeSession['id'] == $session['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($session['event_name'] ?? 'Event') ?> — Sesi <?= htmlspecialchars($session['session']) ?> (<?= date('d M Y', strtotime($session['date'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </form>
            </div>

            <!-- Middle-Right: Live Search Input -->
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <div class="relative flex items-center">
                    <span class="absolute left-3 text-slate-400 text-xs">🔍</span>
                    <input type="text" 
                        x-model="searchQuery" 
                        @input="searchedSeatIdClicked = null"
                        <?= empty($activeSession) ? 'disabled' : '' ?>
                        placeholder="<?= empty($activeSession) ? 'Pilih acara wisuda terlebih dahulu...' : 'Cari nama wisudawan, NRP, atau kode kursi...' ?>"
                        class="w-full pl-8 pr-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all shadow-2xs disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">
                </div>

                <!-- Card Hasil Pencarian -->
                <?php if (!empty($activeSession)): ?>
                    <div x-show="searchQuery.trim() !== ''" x-cloak 
                         class="absolute top-full left-0 right-0 mt-2 bg-white border border-its-blue-sky/40 rounded-xl shadow-xl z-50 p-2.5 text-left max-h-72 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                        
                        <div class="flex items-center justify-between text-[11px] text-slate-500 px-2 pb-2">
                            <span>Ditemukan <strong class="text-its-blue font-bold" x-text="filteredGraduates.length"></strong> wisudawan</span>
                            <button type="button" @click="clearSelection()" class="text-its-blue-light font-bold hover:underline cursor-pointer">Reset</button>
                        </div>

                        <template x-if="filteredGraduates.length > 0">
                            <div class="divide-y divide-slate-100">
                                <template x-for="g in filteredGraduates" :key="g.seat_id">
                                    <button type="button"
                                        @click="focusSeat(g.seat_id, g)"
                                        class="w-full p-2 hover:bg-its-blue/5 rounded-lg transition-colors flex items-center justify-between group cursor-pointer focus:outline-none text-left">
                                        <div class="pr-2">
                                            <p class="text-xs font-bold text-slate-800 group-hover:text-its-blue-light" x-text="g.name"></p>
                                            <p class="text-[10px] text-slate-500">
                                                NRP: <span x-text="g.nrp"></span> • <span x-text="g.faculty"></span> • <span x-text="g.prodi"></span>
                                            </p>
                                        </div>
                                        <span class="px-2.5 py-1 bg-its-yellow/20 text-its-blue border border-its-yellow/60 text-[11px] font-extrabold rounded-md group-hover:bg-its-blue group-hover:text-white group-hover:border-its-blue transition-colors whitespace-nowrap"
                                              x-text="'Kursi ' + g.seat_code">
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="filteredGraduates.length === 0">
                            <div class="p-4 text-center text-xs text-slate-500">
                                Tidak ada data wisudawan untuk "<span x-text="searchQuery" class="font-bold text-slate-700"></span>".
                            </div>
                        </template>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Zoom Controls -->
            <div class="flex items-center gap-1 flex-shrink-0 bg-slate-100/90 p-1 rounded-lg border border-slate-200 text-xs shadow-2xs <?= empty($activeSession) ? 'opacity-50 pointer-events-none select-none' : '' ?>">
                <button type="button" 
                    onmousedown="window.startZoomHold(-0.05)" 
                    onmouseup="window.stopZoomHold()" 
                    onmouseleave="window.stopZoomHold()"
                    ontouchstart="window.startZoomHold(-0.05)" 
                    ontouchend="window.stopZoomHold()"
                    class="px-2.5 py-1 bg-white hover:bg-its-blue hover:text-white border border-slate-200 rounded font-extrabold text-slate-700 transition cursor-pointer select-none" title="Tahan untuk Zoom Out">-</button>
                
                <span id="zoomIndicator" class="w-12 text-center font-extrabold text-its-blue">100%</span>
                
                <button type="button" 
                    onmousedown="window.startZoomHold(0.05)" 
                    onmouseup="window.stopZoomHold()" 
                    onmouseleave="window.stopZoomHold()"
                    ontouchstart="window.startZoomHold(0.05)" 
                    ontouchend="window.stopZoomHold()"
                    class="px-2.5 py-1 bg-white hover:bg-its-blue hover:text-white border border-slate-200 rounded font-extrabold text-slate-700 transition cursor-pointer select-none" title="Tahan untuk Zoom In">+</button>
                
                <button type="button" onclick="window.resetZoom()" class="px-2 py-1 text-its-blue-light font-extrabold hover:text-its-blue hover:underline transition cursor-pointer ml-0.5">Reset</button>
            </div>

        </div>
    </header>

    <!-- AREA KONTEN UTAMA HALAMAN DENAH -->
    <main class="w-full flex-grow px-3 md:px-6 pt-4 pb-8 flex flex-col items-center justify-start">

        <!-- Tampilan Jika Sesi Kosong / Belum Ada Event Dipilih -->
        <?php if (empty($activeSession) || isset($message)): ?>
            <div class="bg-white/95 backdrop-blur-md border border-its-blue-sky/40 text-slate-700 p-8 rounded-3xl max-w-md mx-auto my-auto text-center shadow-xl relative overflow-hidden">
                <div class="relative w-28 h-28 mx-auto mb-5 flex items-center justify-center bg-its-blue/5 rounded-full border border-its-blue-sky/30 shadow-inner">
                    <img src="/images/logo.png" alt="ITS Logo" class="w-16 h-16 object-contain drop-shadow-md">
                </div>
                <h3 class="font-extrabold text-its-blue text-lg mb-2">Acara Wisuda Belum Dipilih</h3>
                <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                    <?= htmlspecialchars($message ?? 'Silakan pilih acara wisuda pada dropdown toolbar di atas untuk melihat denah interaktif posisi tempat duduk wisudawan.') ?>
                </p>
            </div>
        <?php else: ?>
            <!-- PANGGUNG UTAMA -->
            <div class="bg-its-blue text-white font-bold tracking-widest py-3 px-4 rounded-xl mb-4 shadow-md w-full max-w-[98vw] mx-auto text-xs md:text-sm text-center border-b-4 border-its-yellow flex-shrink-0">
                PANGGUNG UTAMA / REKTORAT
            </div>

            <!-- CONTAINER UTAMA DENAH -->
            <div class="w-full max-w-[98vw] mx-auto relative flex-grow flex flex-col items-center overflow-hidden">
                
                <div id="denahContainer" class="w-full overflow-auto pb-8 pt-2 cursor-grab active:cursor-grabbing custom-scrollbar">
                    
                    <div id="zoomWrapper" class="inline-block relative transition-all duration-75">
                        
                        <div id="zoomContent" class="flex flex-nowrap p-4 gap-6 md:gap-10 transition-transform duration-75 origin-top-left">

                            <!-- 1. SAYAP KIRI -->
                            <div class="flex flex-col gap-6 items-end flex-shrink-0">
                                <?php if (empty($leftRows)): ?>
                                    <p class="text-slate-400 text-sm italic">Belum ada data kursi sayap kiri.</p>
                                <?php else: ?>
                                    <?php foreach ($leftRows as $row): ?>
                                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-xs border border-slate-200/80 whitespace-nowrap">
                                            <span class="font-extrabold text-its-blue bg-its-blue/10 border border-its-blue-sky/30 px-2 py-1 rounded-md text-xs w-7 text-center"><?= htmlspecialchars($row['row']) ?></span>
                                            <div class="flex flex-row flex-nowrap gap-3">
                                                <?php foreach ($row['seats'] as $seat): ?>
                                                    <?php
                                                        $hasGraduate = !empty($seat['graduate_name']);
                                                        $facultyColor = $hasGraduate && !empty($seat['faculty_color']) ? $seat['faculty_color'] : '#cbd5e1';
                                                        $graduateData = $hasGraduate ? [
                                                            'seat_id' => $seat['id'],
                                                            'seat_code' => $seatMapInfo[$seat['id']]['code'] ?? '-',
                                                            'name' => $seat['graduate_name'],
                                                            'nrp' => $seat['nrp'],
                                                            'prodi' => $seat['prodi_name'] ?? '-',
                                                            'faculty' => !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-'),
                                                            'faculty_name' => $seat['faculty_name'] ?? '-',
                                                            'color' => $facultyColor,
                                                        ] : null;
                                                    ?>
                                                    <div class="flex flex-col items-center relative" :class="{ 'z-30': searchedSeatIds.includes(<?= $seat['id'] ?>) || selectedSeatId === <?= $seat['id'] ?> }">
                                                        <button type="button" id="seat-<?= $seat['id'] ?>"
                                                            @click="selectSeat(<?= $seat['id'] ?>, <?= $graduateData ? htmlspecialchars(json_encode($graduateData), ENT_QUOTES, 'UTF-8') : 'null' ?>)"
                                                            :class="{ 'search-highlight-seat': searchedSeatIds.includes(<?= $seat['id'] ?>), 'selected-seat-ring': selectedSeatId === <?= $seat['id'] ?> && !searchedSeatIds.includes(<?= $seat['id'] ?>) }"
                                                            class="w-12 h-12 md:w-14 md:h-14 rounded-lg flex items-center justify-center font-extrabold text-xs md:text-sm shadow-xs transition-all duration-150 hover:scale-105 cursor-pointer focus:outline-none"
                                                            style="background-color: <?= $hasGraduate ? htmlspecialchars($facultyColor) : '#f1f5f9' ?>; color: <?= $hasGraduate ? '#ffffff' : '#64748b' ?>; border: <?= $hasGraduate ? 'none' : '1px solid #cbd5e1' ?>;">
                                                            <?= htmlspecialchars($seatMapInfo[$seat['id']]['code'] ?? '-') ?>
                                                        </button>
                                                        <?php if ($hasGraduate): ?>
                                                            <?php
                                                                $facCode = !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-');
                                                                $prodiName = $seat['prodi_name'] ?? '-';
                                                            ?>
                                                            <div class="mt-1 flex flex-col items-center leading-tight w-14 md:w-16 cursor-default" title="<?= htmlspecialchars($facCode . ' - ' . $prodiName) ?>">
                                                                <span class="text-[10px] font-bold text-slate-800 truncate w-full text-center"><?= htmlspecialchars($facCode) ?></span>
                                                                <span class="text-[9px] font-medium text-slate-500 truncate w-full text-center"><?= htmlspecialchars($prodiName) ?></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-[10px] mt-1 text-slate-400 font-medium text-center">-</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- 2. LORONG TENGAH -->
                            <div id="lorongTengah" class="flex items-stretch mx-2 flex-shrink-0">
                                <div class="w-12 md:w-16 border-x-2 border-dashed border-slate-400 opacity-60 relative flex items-center justify-center">
                                    <span class="absolute -rotate-90 text-slate-400 font-bold tracking-[0.3em] text-xs whitespace-nowrap">LORONG</span>
                                </div>
                            </div>

                            <!-- 3. SAYAP KANAN -->
                            <div class="flex flex-col gap-6 items-start flex-shrink-0">
                                <?php if (empty($rightRows)): ?>
                                    <p class="text-slate-400 text-sm italic">Belum ada data kursi sayap kanan.</p>
                                <?php else: ?>
                                    <?php foreach ($rightRows as $row): ?>
                                        <div class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-xs border border-slate-200/80 whitespace-nowrap">
                                            <div class="flex flex-row flex-nowrap gap-3">
                                                <?php foreach ($row['seats'] as $seat): ?>
                                                    <?php
                                                        $hasGraduate = !empty($seat['graduate_name']);
                                                        $facultyColor = $hasGraduate && !empty($seat['faculty_color']) ? $seat['faculty_color'] : '#cbd5e1';
                                                        $graduateData = $hasGraduate ? [
                                                            'seat_id' => $seat['id'],
                                                            'seat_code' => $seatMapInfo[$seat['id']]['code'] ?? '-',
                                                            'name' => $seat['graduate_name'],
                                                            'nrp' => $seat['nrp'],
                                                            'prodi' => $seat['prodi_name'] ?? '-',
                                                            'faculty' => !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-'),
                                                            'faculty_name' => $seat['faculty_name'] ?? '-',
                                                            'color' => $facultyColor,
                                                        ] : null;
                                                    ?>
                                                    <div class="flex flex-col items-center relative" :class="{ 'z-30': searchedSeatIds.includes(<?= $seat['id'] ?>) || selectedSeatId === <?= $seat['id'] ?> }">
                                                        <button type="button" id="seat-<?= $seat['id'] ?>"
                                                            @click="selectSeat(<?= $seat['id'] ?>, <?= $graduateData ? htmlspecialchars(json_encode($graduateData), ENT_QUOTES, 'UTF-8') : 'null' ?>)"
                                                            :class="{ 'search-highlight-seat': searchedSeatIds.includes(<?= $seat['id'] ?>), 'selected-seat-ring': selectedSeatId === <?= $seat['id'] ?> && !searchedSeatIds.includes(<?= $seat['id'] ?>) }"
                                                            class="w-12 h-12 md:w-14 md:h-14 rounded-lg flex items-center justify-center font-extrabold text-xs md:text-sm shadow-xs transition-all duration-150 hover:scale-105 cursor-pointer focus:outline-none"
                                                            style="background-color: <?= $hasGraduate ? htmlspecialchars($facultyColor) : '#f1f5f9' ?>; color: <?= $hasGraduate ? '#ffffff' : '#64748b' ?>; border: <?= $hasGraduate ? 'none' : '1px solid #cbd5e1' ?>;">
                                                            <?= htmlspecialchars($seatMapInfo[$seat['id']]['code'] ?? '-') ?>
                                                        </button>
                                                        <?php if ($hasGraduate): ?>
                                                            <?php
                                                                $facCode = !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-');
                                                                $prodiName = $seat['prodi_name'] ?? '-';
                                                            ?>
                                                            <div class="mt-1 flex flex-col items-center leading-tight w-14 md:w-16 cursor-default" title="<?= htmlspecialchars($facCode . ' - ' . $prodiName) ?>">
                                                                <span class="text-[10px] font-bold text-slate-800 truncate w-full text-center"><?= htmlspecialchars($facCode) ?></span>
                                                                <span class="text-[9px] font-medium text-slate-500 truncate w-full text-center"><?= htmlspecialchars($prodiName) ?></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-[10px] mt-1 text-slate-400 font-medium text-center">-</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <span class="font-extrabold text-its-blue bg-its-blue/10 border border-its-blue-sky/30 px-2 py-1 rounded-md text-xs w-7 text-center"><?= htmlspecialchars($row['row']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL DETAIL KURSI -->
            <div x-show="activeModalData" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
                @click.self="closeModal()">
                <div class="bg-white rounded-2xl shadow-2xl border border-its-blue-sky/30 max-w-sm w-full p-6 text-left transform transition-all relative overflow-hidden"
                    @keydown.escape.window="closeModal()">

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full border border-white shadow-xs"
                                :style="`background-color: ${activeModalData?.color || '#cbd5e1'}`"></span>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-its-blue-light">Detail Tempat Duduk</span>
                        </div>
                        <button @click="closeModal()"
                            class="text-slate-400 hover:text-its-blue font-bold text-base cursor-pointer focus:outline-none transition-colors">✕</button>
                    </div>

                    <template x-if="activeModalData">
                        <div class="space-y-3">
                            <div>
                                <span class="inline-block px-3 py-1 bg-its-yellow/20 text-its-blue border border-its-yellow/60 font-extrabold text-sm rounded-lg mb-2">
                                    Kursi <span x-text="activeModalData.seat_code"></span>
                                </span>
                                <h4 class="text-base font-extrabold text-slate-800" x-text="activeModalData.name"></h4>
                                <p class="text-xs font-semibold text-its-blue-light" x-text="`NRP: ${activeModalData.nrp}`"></p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                                <p><span class="font-bold text-slate-700">Program Studi:</span> <span class="font-medium text-slate-800" x-text="activeModalData.prodi"></span></p>
                                <p><span class="font-bold text-slate-700">Fakultas:</span> <span class="font-medium text-slate-800" x-text="activeModalData.faculty_name && activeModalData.faculty_name !== '-' ? activeModalData.faculty_name : activeModalData.faculty"></span></p>
                            </div>
                        </div>
                    </template>

                    <div class="mt-6 pt-3 border-t border-slate-100 text-right">
                        <button @click="closeModal()"
                            class="px-5 py-2 bg-its-blue hover:bg-its-blue-light text-white text-xs font-bold rounded-lg transition-colors cursor-pointer focus:outline-none shadow-xs">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Script Alpine.js & Zoom Controller -->
    <script>
        function seatMapApp() {
            return {
                selectedSeatId: null,
                activeModalData: null,
                searchQuery: <?= json_encode($searchQuery ?? '') ?>,
                graduatesList: <?= json_encode($allGraduatesList ?? []) ?>,
                searchedSeatIdClicked: null,

                get filteredGraduates() {
                    const q = this.searchQuery.trim().toLowerCase();
                    if (!q) return [];
                    return this.graduatesList.filter(g => {
                        return (g.name && g.name.toLowerCase().includes(q))
                            || (g.nrp && g.nrp.toLowerCase().includes(q))
                            || (g.seat_code && g.seat_code.toLowerCase().includes(q))
                            || (g.prodi && g.prodi.toLowerCase().includes(q))
                            || (g.faculty && g.faculty.toLowerCase().includes(q))
                            || (g.faculty_name && g.faculty_name.toLowerCase().includes(q));
                    });
                },

                get searchedSeatIds() {
                    if (this.searchedSeatIdClicked) {
                        return [this.searchedSeatIdClicked];
                    }
                    if (!this.searchQuery.trim()) {
                        return [];
                    }
                    return this.filteredGraduates.map(g => g.seat_id);
                },

                init() {
                    if (this.searchQuery.trim() !== '' && this.filteredGraduates.length > 0) {
                        const first = this.filteredGraduates[0];
                        this.focusSeat(first.seat_id, first);
                    }
                },

                selectSeat(seatId, data) {
                    if (!data) return;
                    if (this.selectedSeatId === seatId) {
                        this.selectedSeatId = null;
                        this.activeModalData = null;
                    } else {
                        this.selectedSeatId = seatId;
                        this.activeModalData = data;
                    }
                },

                focusSeat(seatId, data) {
                    if (!seatId) return;
                    this.selectedSeatId = seatId;
                    this.activeModalData = data;
                    this.searchedSeatIdClicked = seatId;

                    this.$nextTick(() => {
                        const el = document.getElementById(`seat-${seatId}`);
                        if (el) {
                            el.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center',
                                inline: 'center'
                            });
                        }
                    });
                },

                closeModal() {
                    this.activeModalData = null;
                    this.selectedSeatId = null;
                },

                clearSelection() {
                    this.selectedSeatId = null;
                    this.activeModalData = null;
                    this.searchQuery = '';
                    this.searchedSeatIdClicked = null;
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('denahContainer');
            const lorong = document.getElementById('lorongTengah');
            const zoomContent = document.getElementById('zoomContent');
            const zoomWrapper = document.getElementById('zoomWrapper');
            const zoomIndicator = document.getElementById('zoomIndicator');
            
            let currentScale = 1;
            const minScale = 0.3;
            const maxScale = 1.8;
            let holdInterval = null;

            function applyZoom() {
                if (!zoomContent || !zoomWrapper) return;

                zoomContent.style.transform = 'none';
                const naturalWidth = zoomContent.offsetWidth;
                const naturalHeight = zoomContent.offsetHeight;

                zoomContent.style.transform = `scale(${currentScale})`;
                zoomContent.style.transformOrigin = '0 0';

                const scaledWidth = naturalWidth * currentScale;
                const scaledHeight = naturalHeight * currentScale;

                zoomWrapper.style.width = scaledWidth + 'px';
                zoomWrapper.style.height = scaledHeight + 'px';

                if (zoomIndicator) {
                    zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
                }
            }

            function centerToLorong() {
                if (container && lorong && zoomContent) {
                    const containerWidth = container.clientWidth;
                    const lorongLeft = lorong.offsetLeft * currentScale;
                    const lorongWidth = lorong.offsetWidth * currentScale;
                    
                    const targetScroll = lorongLeft - (containerWidth / 2) + (lorongWidth / 2);
                    container.scrollLeft = Math.max(0, targetScroll);
                }
            }

            window.adjustZoom = function(amount) {
                const oldScrollLeft = container ? container.scrollLeft : 0;
                const containerWidth = container ? container.clientWidth : 0;
                const oldScaledWidth = zoomWrapper ? zoomWrapper.offsetWidth : 0;

                const ratio = oldScaledWidth > 0
                    ? (oldScrollLeft + containerWidth / 2) / oldScaledWidth
                    : 0.5;

                currentScale += amount;
                if (currentScale > maxScale) currentScale = maxScale;
                if (currentScale < minScale) currentScale = minScale;

                applyZoom();

                if (container && zoomWrapper) {
                    const newScaledWidth = zoomWrapper.offsetWidth;

                    if (newScaledWidth <= containerWidth) {
                        centerToLorong();
                    } else {
                        const desiredScroll = (ratio * newScaledWidth) - (containerWidth / 2);
                        const maxScroll = newScaledWidth - containerWidth;
                        container.scrollLeft = Math.max(0, Math.min(desiredScroll, maxScroll));
                    }
                }
            };

            window.resetZoom = function() {
                currentScale = 1;
                applyZoom();
                centerToLorong();
            };

            setTimeout(() => {
                applyZoom();
                centerToLorong();
            }, 100);

            window.addEventListener('resize', () => {
                applyZoom();
            });

            window.startZoomHold = function(amount) {
                window.adjustZoom(amount);
                holdInterval = setInterval(() => {
                    window.adjustZoom(amount);
                }, 100);
            };

            window.stopZoomHold = function() {
                if (holdInterval) {
                    clearInterval(holdInterval);
                    holdInterval = null;
                }
            };

            if (container) {
                container.addEventListener('wheel', function(e) {
                    if (e.ctrlKey) {
                        e.preventDefault();
                        const zoomAmount = e.deltaY < 0 ? 0.08 : -0.08;
                        window.adjustZoom(zoomAmount);
                    }
                }, { passive: false });
            }
        });
    </script>
</body>

</html>