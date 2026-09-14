<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
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

        body {
            position: relative;
            background-color: #f8fafc;
            touch-action: manipulation;
        }

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
            background-size: 280px auto;
            opacity: 0.10;
            pointer-events: none;
            z-index: 1;
        }

        header, main, footer, div[x-data] {
            position: relative;
            z-index: 2;
        }

        .selected-seat-ring {
            box-shadow: 0 0 0 3px #127BBE, 0 4px 14px rgba(18, 123, 190, 0.4);
            transform: scale(1.1);
            position: relative;
            z-index: 20 !important;
        }

        @keyframes seatPulse {
            0% {
                box-shadow: 0 0 0 3px #127BBE, 0 0 8px rgba(18, 123, 190, 0.5);
                transform: scale(1.05);
            }
            50% {
                box-shadow: 0 0 0 4px #FDBB16, 0 0 18px rgba(253, 187, 22, 0.85);
                transform: scale(1.15);
            }
            100% {
                box-shadow: 0 0 0 3px #127BBE, 0 0 8px rgba(18, 123, 190, 0.5);
                transform: scale(1.05);
            }
        }

        .search-highlight-seat {
            animation: seatPulse 1.4s infinite ease-in-out;
            position: relative;
            z-index: 25 !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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

        /* ===== SENO MASCOT STYLES (v2) ===== */
        #seno-mascot {
            position: fixed;
            bottom: 0;
            right: clamp(12px, 3vw, 40px);
            width: clamp(100px, 15vw, 160px);
            z-index: 9999;
            pointer-events: none;
            will-change: transform;
        }
        #seno-mascot.seno-hidden { opacity: 0; pointer-events: none; }
        #seno-mascot.seno-visible { opacity: 1; pointer-events: auto; }

        @keyframes senoSlideInRight {
            from { transform: translateX(220px); }
            to   { transform: translateX(0); }
        }
        @keyframes senoRiseUp {
            from { transform: translateY(260px); }
            to   { transform: translateY(0); }
        }
        @keyframes senoDropDown {
            0%   { transform: translateY(-280px) rotate(-12deg); }
            65%  { transform: translateY(18px) rotate(4deg); }
            82%  { transform: translateY(-8px) rotate(-2deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
        @keyframes senoPeekRight {
            0%   { transform: translateX(150px); }
            28%  { transform: translateX(55px); }
            52%  { transform: translateX(55px); }
            100% { transform: translateX(0); }
        }
        @keyframes senoExitRight {
            from { transform: translateX(0); }
            to   { transform: translateX(230px); }
        }
        @keyframes senoFloat {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-7px); }
        }
        @keyframes senoBounceIn {
            0%   { transform: scale(0.65); opacity: 0; }
            60%  { transform: scale(1.08); opacity: 1; }
            80%  { transform: scale(0.96); }
            100% { transform: scale(1); }
        }
        @keyframes senoProgress {
            from { width: 0%; }
            to   { width: 100%; }
        }

        #seno-mascot.anim-right  { animation: senoSlideInRight 0.75s cubic-bezier(.34,1.56,.64,1) forwards; }
        #seno-mascot.anim-rise   { animation: senoRiseUp       0.7s  cubic-bezier(.34,1.56,.64,1) forwards; }
        #seno-mascot.anim-drop   { animation: senoDropDown     0.85s ease-out forwards; }
        #seno-mascot.anim-peek   { animation: senoPeekRight    1.1s  ease forwards; }
        #seno-mascot.anim-exit   { animation: senoExitRight    0.55s ease-in forwards; }
        #seno-mascot.anim-float  { animation: senoFloat        3s    ease-in-out infinite; }

        #seno-img {
            width: 100%;
            cursor: pointer;
            display: block;
            user-select: none;
            filter: drop-shadow(0 6px 20px rgba(18,123,190,.32));
            transition: transform .25s;
        }
        #seno-img:hover { transform: scale(1.04); }

        #seno-bubble {
            position: absolute;
            bottom: calc(100% - 8px);
            right: 0;
            width: clamp(200px, 45vw, 270px);
            background: #fff;
            border-radius: 16px 16px 4px 16px;
            box-shadow: 0 8px 28px rgba(35,63,124,.16), 0 0 0 1.5px rgba(18,123,190,.13);
            padding: 13px 15px 11px;
            font-family: 'Work Sans', sans-serif;
            animation: senoBounceIn .35s ease forwards;
            display: none;
        }
        #seno-bubble::after {
            content: '';
            position: absolute;
            bottom: -13px;
            right: 18px;
            border: 13px solid transparent;
            border-top-color: #fff;
            border-right: 0;
            filter: drop-shadow(0 2px 3px rgba(35,63,124,.1));
        }

        .seno-text {
            font-size: clamp(11px, 1.5vw, 13px);
            line-height: 1.55;
            color: #1e293b;
            min-height: 36px;
        }

        #seno-progress-wrap {
            height: 3px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }
        #seno-progress-bar {
            height: 100%;
            background: linear-gradient(90deg,#127BBE,#FDBB16);
            border-radius: 4px;
            width: 0%;
        }
        #seno-progress-bar.running {
            animation: senoProgress var(--seno-dur, 4s) linear forwards;
        }

        .seno-btn-row {
            display: flex;
            gap: 6px;
            margin-top: 9px;
            align-items: center;
        }
        .seno-btn {
            flex: 1;
            background: linear-gradient(135deg,#127BBE,#233F7C);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 6px 10px;
            font-size: clamp(10px,1.4vw,12px);
            font-weight: 600;
            cursor: pointer;
            font-family: 'Work Sans', sans-serif;
            transition: opacity .18s, transform .14s;
            white-space: nowrap;
        }
        .seno-btn:hover { opacity: .86; transform: scale(1.03); }
        .seno-btn:active { transform: scale(.97); }
        .seno-btn.seno-skip {
            background: #f1f5f9;
            color: #64748b;
        }
        .seno-btn.seno-skip:hover { background: #e2e8f0; }

        #seno-replay-btn {
            position: fixed;
            bottom: clamp(16px,2.5vw,24px);
            right: clamp(14px,2.5vw,24px);
            z-index: 9998;
            background: linear-gradient(135deg,#233F7C,#127BBE);
            border: none;
            border-radius: 50%;
            width: clamp(42px,6vw,52px);
            height: clamp(42px,6vw,52px);
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(35,63,124,.32);
            display: none;
            align-items: center;
            justify-content: center;
            transition: transform .2s, box-shadow .2s;
            padding: 4px;
        }
        #seno-replay-btn:hover { transform: scale(1.12); box-shadow: 0 6px 22px rgba(35,63,124,.42); }
        #seno-replay-btn img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
        #seno-replay-btn.seno-rb-visible { display: flex; }

        @media (max-width: 480px) {
            #seno-mascot {
                width: clamp(80px, 22vw, 110px);
                right: 8px;
            }
            #seno-bubble {
                width: min(88vw, 260px);
                right: -8px;
            }
        }
        @media (max-width: 768px) {
            #seno-bubble { width: min(70vw, 260px); }
        }
    </style>
</head>

<body class="font-sans antialiased text-slate-800 min-h-screen w-full flex flex-col overflow-x-hidden" x-data="seatMapApp()">

    <!-- HEADER RESPONSIF v3 -->
    <header class="w-full bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-[98vw] mx-auto px-3 md:px-4 py-2 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2 md:gap-3">
            
            <div class="flex items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-2.5">
                    <img src="/images/logo.png" alt="ITS Logo" class="w-8 h-8 md:w-9 md:h-9 object-contain">
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div class="flex flex-col justify-center leading-none">
                        <p class="font-friz text-[9px] md:text-[11px] font-bold uppercase tracking-wider text-its-blue-light mb-0.5">
                            SISTEM INFORMASI WISUDA
                        </p>
                        <h1 class="font-friz text-xs md:text-lg font-bold uppercase text-its-blue tracking-tight">
                            DENAH TEMPAT DUDUK
                        </h1>
                    </div>
                </div>

                <!-- Dropdown Mobile (v3) -->
                <div class="md:hidden w-44">
                    <form method="GET" id="sessionFormMobile" class="m-0">
                        <select name="session_id" 
                            onchange="if(this.value === '') { window.location.href = window.location.pathname; } else { this.form.submit(); }"
                            class="w-full px-2 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-[11px] font-bold text-its-blue focus:outline-none truncate">
                            <option value="" <?= empty($activeSession) ? 'selected' : '' ?>>-- Pilih Acara --</option>
                            <?php if (!empty($publishedSessions)): ?>
                                <?php foreach ($publishedSessions as $session): ?>
                                    <option value="<?= $session['id'] ?>" <?= (!empty($activeSession) && $activeSession['id'] == $session['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($session['event_name'] ?? 'Event') ?> — Sesi <?= htmlspecialchars($session['session']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Dropdown Desktop (v3) -->
            <div class="hidden md:block shrink-0 w-64 md:w-72">
                <form method="GET" id="sessionForm" class="m-0">
                    <select name="session_id" id="session_id" 
                        onchange="if(this.value === '') { window.location.href = window.location.pathname; } else { document.getElementById('sessionForm').submit(); }"
                        class="w-full px-3.5 py-1.5 bg-slate-50 border border-slate-300 focus:border-its-blue-light focus:bg-white rounded-lg text-xs font-bold text-its-blue focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 transition-all cursor-pointer shadow-2xs">
                        <option value="" <?= empty($activeSession) ? 'selected' : '' ?>>-- Pilih Acara Wisuda --</option>
                        <?php if (!empty($publishedSessions)): ?>
                            <?php foreach ($publishedSessions as $session): ?>
                                <option value="<?= $session['id'] ?>" <?= (!empty($activeSession) && $activeSession['id'] == $session['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($session['event_name'] ?? 'Event') ?> — Sesi <?= htmlspecialchars($session['session']) ?> (<?= date('d M Y', strtotime($session['date'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto flex-1 max-w-none md:max-w-md">
                <div class="relative flex-1 min-w-0">
                    <div class="relative flex items-center">
                        <span class="absolute left-2.5 text-slate-400 text-xs">🔍</span>
                        <input type="text" 
                            x-model="searchQuery" 
                            @input="searchedSeatIdClicked = null"
                            <?= empty($activeSession) ? 'disabled' : '' ?>
                            placeholder="<?= empty($activeSession) ? 'Pilih acara dulu...' : 'Cari nama, NRP, kursi...' ?>"
                            class="w-full pl-7 pr-2 py-1.5 border border-slate-300 rounded-lg text-xs font-medium bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-its-blue-sky/50 focus:border-its-blue-light transition-all shadow-2xs disabled:bg-slate-100">
                    </div>

                    <?php if (!empty($activeSession)): ?>
                        <div x-show="searchQuery.trim() !== ''" x-cloak 
                             class="absolute top-full left-0 right-0 mt-1.5 bg-white border border-its-blue-sky/40 rounded-xl shadow-xl z-50 p-2 text-left max-h-64 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                            <div class="flex items-center justify-between text-[10px] text-slate-500 px-1 pb-1">
                                <span>Ditemukan <strong class="text-its-blue font-bold" x-text="filteredGraduates.length"></strong> wisudawan</span>
                                <button type="button" @click="clearSelection()" class="text-its-blue-light font-bold hover:underline cursor-pointer">Reset</button>
                            </div>

                            <template x-if="filteredGraduates.length > 0">
                                <div class="divide-y divide-slate-100">
                                    <template x-for="g in filteredGraduates" :key="g.seat_id">
                                        <button type="button" @click="focusSeat(g.seat_id, g)"
                                            class="w-full p-1.5 hover:bg-its-blue/5 rounded-lg flex items-center justify-between group text-left">
                                            <div class="pr-2 truncate">
                                                <p class="text-xs font-bold text-slate-800 group-hover:text-its-blue-light truncate" x-text="g.name"></p>
                                                <p class="text-[10px] text-slate-500 truncate">NRP: <span x-text="g.nrp"></span> • <span x-text="g.faculty"></span></p>
                                            </div>
                                            <span class="px-2 py-0.5 bg-its-yellow/20 text-its-blue border border-its-yellow/60 text-[10px] font-extrabold rounded-md whitespace-nowrap" x-text="'Kursi ' + g.seat_code"></span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-0.5 shrink-0 bg-slate-100 p-1 rounded-lg border border-slate-200 text-xs shadow-2xs <?= empty($activeSession) ? 'opacity-50 pointer-events-none' : '' ?>">
                    <button type="button" onmousedown="window.startZoomHold(-0.05)" onmouseup="window.stopZoomHold()" onmouseleave="window.stopZoomHold()" ontouchstart="window.startZoomHold(-0.05)" ontouchend="window.stopZoomHold()" class="w-6 h-6 bg-white hover:bg-its-blue hover:text-white border border-slate-200 rounded font-bold text-slate-700 flex items-center justify-center">-</button>
                    <span id="zoomIndicator" class="w-10 text-center font-extrabold text-its-blue text-[11px]">100%</span>
                    <button type="button" onmousedown="window.startZoomHold(0.05)" onmouseup="window.stopZoomHold()" onmouseleave="window.stopZoomHold()" ontouchstart="window.startZoomHold(0.05)" ontouchend="window.stopZoomHold()" class="w-6 h-6 bg-white hover:bg-its-blue hover:text-white border border-slate-200 rounded font-bold text-slate-700 flex items-center justify-center">+</button>
                    <button type="button" onclick="window.resetZoom()" class="px-1.5 text-its-blue-light font-bold text-[11px] hover:underline">Reset</button>
                </div>
            </div>

        </div>
    </header>

    <!-- MAIN AREA -->
    <main class="w-full flex-grow px-2 md:px-6 pt-3 pb-8 flex flex-col items-center justify-start">

        <?php if (empty($activeSession) || isset($message)): ?>
            <div class="bg-white/95 backdrop-blur-md border border-its-blue-sky/40 text-slate-700 p-6 rounded-3xl max-w-sm mx-auto my-auto text-center shadow-xl">
                <div class="relative w-20 h-20 mx-auto mb-3 flex items-center justify-center bg-its-blue/5 rounded-full border border-its-blue-sky/30">
                    <img src="/images/logo.png" alt="ITS Logo" class="w-12 h-12 object-contain">
                </div>
                <h3 class="font-extrabold text-its-blue text-base mb-1">Acara Wisuda Belum Dipilih</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    <?= htmlspecialchars($message ?? 'Silakan pilih acara wisuda pada dropdown toolbar di atas.') ?>
                </p>
            </div>
        <?php else: ?>
            <div class="bg-its-blue text-white font-bold tracking-widest py-2 px-4 rounded-xl mb-3 shadow-xs w-full text-[11px] md:text-xs text-center border-b-4 border-its-yellow shrink-0">
                PANGGUNG UTAMA / REKTORAT
            </div>

            <div class="w-full relative flex-grow flex flex-col items-center overflow-hidden">
                <div id="denahContainer" class="w-full overflow-auto pb-8 pt-2 cursor-grab active:cursor-grabbing custom-scrollbar touch-pan-x touch-pan-y">
                    <div id="zoomWrapper" class="inline-block relative transition-all duration-75">
                        <div id="zoomContent" class="flex flex-nowrap p-2 md:p-4 gap-4 md:gap-10 transition-transform duration-75 origin-top-left">

                            <!-- SAYAP KIRI -->
                            <div class="flex flex-col gap-3 md:gap-5 items-end shrink-0">
                                <?php if (!empty($leftRows)): ?>
                                    <?php foreach ($leftRows as $row): ?>
                                        <div class="flex items-center gap-1.5 md:gap-3 bg-white p-2 md:p-3 rounded-xl shadow-2xs border border-slate-200/80 whitespace-nowrap">
                                            <span class="font-extrabold text-its-blue bg-its-blue/10 border border-its-blue-sky/30 px-1.5 py-0.5 rounded text-[10px] md:text-xs w-6 md:w-7 text-center"><?= htmlspecialchars($row['row']) ?></span>
                                            <div class="flex flex-row flex-nowrap gap-1.5 md:gap-2.5">
                                                <?php foreach ($row['seats'] as $seat): ?>
                                                    <?php
                                                        $hasGraduate = !empty($seat['graduate_name']);
                                                        $facultyColor = $hasGraduate && !empty($seat['faculty_color']) ? $seat['faculty_color'] : '#cbd5e1';
                                                        $graduateData = $hasGraduate ? [
                                                            'seat_id'      => $seat['id'],
                                                            'seat_code'    => $seatMapInfo[$seat['id']]['code'] ?? '-',
                                                            'name'         => $seat['graduate_name'],
                                                            'nrp'          => $seat['nrp'],
                                                            'prodi'        => $seat['prodi_name'] ?? '-',
                                                            'faculty'      => !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-'),
                                                            'faculty_name' => $seat['faculty_name'] ?? '-',
                                                            'color'        => $facultyColor,
                                                        ] : null;
                                                    ?>
                                                    <div class="flex flex-col items-center relative" :class="{ 'z-30': searchedSeatIds.includes(<?= $seat['id'] ?>) || selectedSeatId === <?= $seat['id'] ?> }">
                                                        <button type="button" id="seat-<?= $seat['id'] ?>"
                                                            @click="selectSeat(<?= $seat['id'] ?>, <?= $graduateData ? htmlspecialchars(json_encode($graduateData), ENT_QUOTES, 'UTF-8') : 'null' ?>)"
                                                            :class="{ 'search-highlight-seat': searchedSeatIds.includes(<?= $seat['id'] ?>), 'selected-seat-ring': selectedSeatId === <?= $seat['id'] ?> && !searchedSeatIds.includes(<?= $seat['id'] ?>) }"
                                                            class="w-10 h-10 md:w-13 md:h-13 rounded-lg flex items-center justify-center font-extrabold text-[11px] md:text-xs shadow-2xs transition-all duration-150 hover:scale-105 cursor-pointer focus:outline-none"
                                                            style="background-color: <?= $hasGraduate ? htmlspecialchars($facultyColor) : '#f1f5f9' ?>; color: <?= $hasGraduate ? '#ffffff' : '#64748b' ?>; border: <?= $hasGraduate ? 'none' : '1px solid #cbd5e1' ?>;">
                                                            <?= htmlspecialchars($seatMapInfo[$seat['id']]['code'] ?? '-') ?>
                                                        </button>
                                                        <?php if ($hasGraduate): ?>
                                                            <div class="mt-1 flex flex-col items-center leading-tight w-10 md:w-13 cursor-default">
                                                                <span class="text-[9px] font-bold text-slate-800 truncate w-full text-center"><?= htmlspecialchars(!empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-')) ?></span>
                                                                <span class="text-[8px] font-medium text-slate-500 truncate w-full text-center"><?= htmlspecialchars($seat['prodi_name'] ?? '-') ?></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-[9px] mt-1 text-slate-400 font-medium text-center">-</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- LORONG TENGAH -->
                            <div id="lorongTengah" class="flex items-stretch mx-1 md:mx-2 shrink-0">
                                <div class="w-8 md:w-14 border-x-2 border-dashed border-slate-300 relative flex items-center justify-center">
                                    <span class="absolute -rotate-90 text-slate-400 font-bold tracking-[0.25em] text-[10px] md:text-xs whitespace-nowrap">LORONG</span>
                                </div>
                            </div>

                            <!-- SAYAP KANAN -->
                            <div class="flex flex-col gap-3 md:gap-5 items-start shrink-0">
                                <?php if (!empty($rightRows)): ?>
                                    <?php foreach ($rightRows as $row): ?>
                                        <div class="flex items-center gap-1.5 md:gap-3 bg-white p-2 md:p-3 rounded-xl shadow-2xs border border-slate-200/80 whitespace-nowrap">
                                            <div class="flex flex-row flex-nowrap gap-1.5 md:gap-2.5">
                                                <?php foreach ($row['seats'] as $seat): ?>
                                                    <?php
                                                        $hasGraduate = !empty($seat['graduate_name']);
                                                        $facultyColor = $hasGraduate && !empty($seat['faculty_color']) ? $seat['faculty_color'] : '#cbd5e1';
                                                        $graduateData = $hasGraduate ? [
                                                            'seat_id'      => $seat['id'],
                                                            'seat_code'    => $seatMapInfo[$seat['id']]['code'] ?? '-',
                                                            'name'         => $seat['graduate_name'],
                                                            'nrp'          => $seat['nrp'],
                                                            'prodi'        => $seat['prodi_name'] ?? '-',
                                                            'faculty'      => !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-'),
                                                            'faculty_name' => $seat['faculty_name'] ?? '-',
                                                            'color'        => $facultyColor,
                                                        ] : null;
                                                    ?>
                                                    <div class="flex flex-col items-center relative" :class="{ 'z-30': searchedSeatIds.includes(<?= $seat['id'] ?>) || selectedSeatId === <?= $seat['id'] ?> }">
                                                        <button type="button" id="seat-<?= $seat['id'] ?>"
                                                            @click="selectSeat(<?= $seat['id'] ?>, <?= $graduateData ? htmlspecialchars(json_encode($graduateData), ENT_QUOTES, 'UTF-8') : 'null' ?>)"
                                                            :class="{ 'search-highlight-seat': searchedSeatIds.includes(<?= $seat['id'] ?>), 'selected-seat-ring': selectedSeatId === <?= $seat['id'] ?> && !searchedSeatIds.includes(<?= $seat['id'] ?>) }"
                                                            class="w-10 h-10 md:w-13 md:h-13 rounded-lg flex items-center justify-center font-extrabold text-[11px] md:text-xs shadow-2xs transition-all duration-150 hover:scale-105 cursor-pointer focus:outline-none"
                                                            style="background-color: <?= $hasGraduate ? htmlspecialchars($facultyColor) : '#f1f5f9' ?>; color: <?= $hasGraduate ? '#ffffff' : '#64748b' ?>; border: <?= $hasGraduate ? 'none' : '1px solid #cbd5e1' ?>;">
                                                            <?= htmlspecialchars($seatMapInfo[$seat['id']]['code'] ?? '-') ?>
                                                        </button>
                                                        <?php if ($hasGraduate): ?>
                                                            <div class="mt-1 flex flex-col items-center leading-tight w-10 md:w-13 cursor-default">
                                                                <span class="text-[9px] font-bold text-slate-800 truncate w-full text-center"><?= htmlspecialchars(!empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-')) ?></span>
                                                                <span class="text-[8px] font-medium text-slate-500 truncate w-full text-center"><?= htmlspecialchars($seat['prodi_name'] ?? '-') ?></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-[9px] mt-1 text-slate-400 font-medium text-center">-</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <span class="font-extrabold text-its-blue bg-its-blue/10 border border-its-blue-sky/30 px-1.5 py-0.5 rounded text-[10px] md:text-xs w-6 md:w-7 text-center"><?= htmlspecialchars($row['row']) ?></span>
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
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
                @click.self="closeModal()">
                <div class="bg-white rounded-2xl shadow-2xl border border-its-blue-sky/30 max-w-xs md:max-w-sm w-full p-5 text-left relative"
                    @keydown.escape.window="closeModal()">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full border border-white" :style="`background-color: ${activeModalData?.color || '#cbd5e1'}`"></span>
                            <span class="text-[11px] font-extrabold uppercase text-its-blue-light">Detail Tempat Duduk</span>
                        </div>
                        <button @click="closeModal()" class="text-slate-400 hover:text-its-blue font-bold text-sm">✕</button>
                    </div>
                    <template x-if="activeModalData">
                        <div class="space-y-2 text-xs">
                            <span class="inline-block px-2 py-0.5 bg-its-yellow/20 text-its-blue border border-its-yellow/60 font-extrabold rounded mb-1">
                                Kursi <span x-text="activeModalData.seat_code"></span>
                            </span>
                            <h4 class="text-sm font-extrabold text-slate-800" x-text="activeModalData.name"></h4>
                            <p class="font-semibold text-its-blue-light" x-text="`NRP: ${activeModalData.nrp}`"></p>
                            <div class="pt-2 border-t border-slate-100 text-slate-600">
                                <p><span class="font-bold">Prodi:</span> <span x-text="activeModalData.prodi"></span></p>
                                <p><span class="font-bold">Fakultas:</span> <span x-text="activeModalData.faculty_name"></span></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- SENO MASCOT HTML STRUCTURE (v2) -->
    <?php if (!empty($activeSession) && !isset($message)): ?>
    <div id="seno-mascot" class="seno-hidden">
        <div id="seno-bubble">
            <img src="/images/NAMA_SENO.png" alt="SENO" style="height: 22px; width: auto; display: block; margin-bottom: 8px; object-fit: contain;" />
            <div class="seno-text" id="seno-text"></div>
            <div id="seno-progress-wrap"><div id="seno-progress-bar"></div></div>
            <div class="seno-btn-row" id="seno-btns">
                <button class="seno-btn seno-skip" id="seno-skip-btn">⏩ Skip</button>
                <button class="seno-btn" id="seno-next-btn">Lanjut →</button>
            </div>
        </div>
        <img id="seno-img" src="/images/SENO_POSE_1.png" alt="SENO" />
    </div>

    <button id="seno-replay-btn" title="Panggil SENO lagi 🎓">
        <img src="/images/SENO_POSE_4.png" alt="Panggil SENO" />
    </button>
    <?php endif; ?>

    <!-- JS CONTROLLER & LOGIKA ZOOM PRESISI (v2) -->
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
                            || (g.seat_code && g.seat_code.toLowerCase().includes(q));
                    });
                },

                get searchedSeatIds() {
                    if (this.searchedSeatIdClicked) return [this.searchedSeatIdClicked];
                    if (!this.searchQuery.trim()) return [];
                    return this.filteredGraduates.map(g => g.seat_id);
                },

                selectSeat(seatId, data) {
                    if (!data) return;
                    this.selectedSeatId = (this.selectedSeatId === seatId) ? null : seatId;
                    this.activeModalData = this.selectedSeatId ? data : null;
                },

                focusSeat(seatId, data) {
                    if (!seatId) return;
                    this.selectedSeatId = seatId;
                    this.activeModalData = data;
                    this.searchedSeatIdClicked = seatId;
                    this.$nextTick(() => {
                        const el = document.getElementById(`seat-${seatId}`);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
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

        /* ===== LOGIKA ZOOM PRESISI V2 ===== */
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

    <!-- JS MASKOT SENO TOUR (v2) -->
    <?php if (!empty($activeSession) && !isset($message)): ?>
    <script>
    (function () {
        'use strict';
        var POSES = {
            wisuda:   '/images/SENO_POSE_1.png',
            wave:     '/images/SENO_POSE_3.png',
            shy:      '/images/SENO_POSE_2.png',
            surprise: '/images/SENO_POSE_4.png',
            normal:   '/images/SENO_POSE_1.png',
        };

        var steps = [
            {
                pose: 'wave',
                text: '🎉 Halo Wisudawan ITS! Selamat atas pencapaianmu yang luar biasa! 🎓',
                dur: 4000,
            },
            {
                pose: 'shy',
                text: 'Aku <b>SENO</b>, maskot ITS! Yuk kukenalkan cara pakai denah ini!!',
                dur: 3800,
            },
            {
                pose: 'wisuda',
                text: '🎛️ Gunakan <b>dropdown sesi</b> di atas untuk memilih jadwal wisudamu.',
                dur: 4000,
            },
            {
                pose: 'normal',
                text: '🔍 <b>Ketik nama/NRP</b> di kotak cari — kursimu akan <span style="color:#127BBE;font-weight:700">berkilau!</span> ✨',
                dur: 4200,
            },
            {
                pose: 'surprise',
                text: '🔎 Pakai tombol <b>− / +</b> atau <b>Ctrl+Scroll</b> untuk zoom denah.',
                dur: 3600,
            },
            {
                pose: 'normal',
                text: '🪑 Klik kursi mana saja untuk lihat <b>nama, NRP & prodi</b> wisudawan.',
                dur: 3800,
            },
            {
                pose: 'wave',
                text: '🎊 Selamat menikmati hari wisudamu! Kalau perlu bantuan, panggil aku lagi ya! 👋',
                dur: 0,
                isLast: true,
            },
        ];

        var cur = 0;
        var autoMode = true;
        var autoTimer = null;
        var twTimer = null;
        var ENTRY_ANIMS = ['anim-right', 'anim-rise', 'anim-drop', 'anim-peek'];

        function $id(id) { return document.getElementById(id); }
        var elMascot    = function() { return $id('seno-mascot'); };
        var elBubble    = function() { return $id('seno-bubble'); };
        var elImg       = function() { return $id('seno-img'); };
        var elText      = function() { return $id('seno-text'); };
        var elNextBtn   = function() { return $id('seno-next-btn'); };
        var elSkipBtn   = function() { return $id('seno-skip-btn'); };
        var elProgWrap  = function() { return $id('seno-progress-wrap'); };
        var elProgBar   = function() { return $id('seno-progress-bar'); };
        var elReplay    = function() { return $id('seno-replay-btn'); };

        function setPose(pose) {
            var img = elImg();
            if (img) img.src = POSES[pose] || POSES.wisuda;
        }

        function clearAutoTimer() {
            if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
        }
        function clearTwTimer() {
            if (twTimer) { clearInterval(twTimer); twTimer = null; }
        }

        function typewrite(text, onDone) {
            clearTwTimer();
            var el = elText();
            if (!el) return;
            var plain = text.replace(/<[^>]+>/g, '');
            var i = 0;
            el.innerHTML = '';
            twTimer = setInterval(function () {
                i++;
                el.textContent = plain.substring(0, i);
                if (i >= plain.length) {
                    clearTwTimer();
                    el.innerHTML = text;
                    if (onDone) onDone();
                }
            }, 20);
        }

        function animateBubble() {
            var b = elBubble();
            if (!b) return;
            b.style.animation = 'none';
            void b.offsetWidth;
            b.style.animation = 'senoBounceIn .35s ease forwards';
            b.style.display = 'block';
        }

        function startProgressBar(dur) {
            var wrap = elProgWrap();
            var bar  = elProgBar();
            if (!wrap || !bar) return;
            if (dur > 0) {
                wrap.style.display = 'block';
                bar.style.animation = 'none';
                bar.style.width = '0%';
                void bar.offsetWidth;
                bar.style.setProperty('--seno-dur', (dur / 1000) + 's');
                bar.classList.add('running');
            } else {
                wrap.style.display = 'none';
            }
        }

        function stopProgressBar() {
            var wrap = elProgWrap();
            var bar  = elProgBar();
            if (wrap) wrap.style.display = 'none';
            if (bar) {
                bar.classList.remove('running');
                bar.style.width = '0%';
                bar.style.animation = 'none';
            }
        }

        function showStep(idx) {
            var step = steps[idx];
            if (!step) return;

            clearAutoTimer();
            stopProgressBar();

            setPose(step.pose);
            animateBubble();

            var nextBtn = elNextBtn();
            var skipBtn = elSkipBtn();

            if (step.isLast) {
                if (nextBtn) { nextBtn.textContent = 'Siap! 🎓'; nextBtn.style.display = ''; }
                if (skipBtn) skipBtn.style.display = 'none';
                stopProgressBar();
                typewrite(step.text, null);
            } else if (autoMode) {
                if (nextBtn) { nextBtn.textContent = 'Lanjut →'; nextBtn.style.display = ''; }
                if (skipBtn) { skipBtn.textContent = '⏩ Skip'; skipBtn.style.display = ''; }
                typewrite(step.text, function() {
                    startProgressBar(step.dur);
                    autoTimer = setTimeout(function() {
                        advanceStep();
                    }, step.dur);
                });
            } else {
                if (nextBtn) { nextBtn.textContent = 'Lanjut →'; nextBtn.style.display = ''; }
                if (skipBtn) { skipBtn.textContent = '⏩ Skip'; skipBtn.style.display = ''; }
                typewrite(step.text, null);
                stopProgressBar();
            }
        }

        function advanceStep() {
            if (twTimer) {
                clearTwTimer();
                var step = steps[cur];
                var el = elText();
                if (el && step) el.innerHTML = step.text;

                if (autoMode && step && step.dur > 0 && !step.isLast) {
                    stopProgressBar();
                    startProgressBar(step.dur);
                    autoTimer = setTimeout(advanceStep, step.dur);
                }
                return;
            }
            clearAutoTimer();
            if (cur >= steps.length - 1) {
                exitSeno();
                return;
            }
            cur++;
            showStep(cur);
        }

        function exitSeno() {
            clearAutoTimer();
            clearTwTimer();
            stopProgressBar();
            var mascot = elMascot();
            if (!mascot) return;
            setPose('wave');
            elBubble() && (elBubble().style.display = 'none');
            mascot.classList.remove('anim-float');
            ENTRY_ANIMS.forEach(function(a) { mascot.classList.remove(a); });
            mascot.classList.add('anim-exit');
            setTimeout(function() {
                mascot.classList.add('seno-hidden');
                mascot.classList.remove('seno-visible', 'anim-exit');
                var rb = elReplay();
                if (rb) rb.classList.add('seno-rb-visible');
            }, 600);
        }

        function startTour(manual) {
            autoMode = !manual;
            cur = 0;
            var mascot = elMascot();
            if (!mascot) return;

            ENTRY_ANIMS.forEach(function(a) { mascot.classList.remove(a); });
            mascot.classList.remove('anim-exit', 'anim-float', 'seno-hidden');
            mascot.style.right = '';
            mascot.style.left  = '';
            mascot.classList.add('seno-visible');

            var rb = elReplay();
            if (rb) rb.classList.remove('seno-rb-visible');

            var pick = ENTRY_ANIMS[Math.floor(Math.random() * ENTRY_ANIMS.length)];
            mascot.classList.add(pick);

            setTimeout(function() {
                ENTRY_ANIMS.forEach(function(a) { mascot.classList.remove(a); });
                mascot.classList.add('anim-float');
                showStep(0);
            }, 950);
        }

        window.SenoTour = {
            next: function() { advanceStep(); },
            skip: function() { exitSeno(); },
            replay: function() { startTour(true); },
        };

        document.addEventListener('DOMContentLoaded', function() {
            var nb = elNextBtn();
            var sb = elSkipBtn();
            var rb = elReplay();
            if (nb) nb.addEventListener('click', function() { SenoTour.next(); });
            if (sb) sb.addEventListener('click', function() { SenoTour.skip(); });
            if (rb) rb.addEventListener('click', function() { SenoTour.replay(); });

            var img = elImg();
            if (img) img.addEventListener('click', function() {
                if (twTimer) {
                    clearTwTimer();
                    var step = steps[cur];
                    var tel = elText();
                    if (tel && step) tel.innerHTML = step.text;
                    if (autoMode && step && step.dur > 0 && !step.isLast) {
                        stopProgressBar();
                        startProgressBar(step.dur);
                        autoTimer = setTimeout(advanceStep, step.dur);
                    }
                }
            });

            setTimeout(function() {
                startTour(false);
            }, 900);
        });
    })();
    </script>
    <?php endif; ?>
</body>
</html>