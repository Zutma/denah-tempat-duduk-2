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
    </style>
</head>

<body class="font-sans antialiased text-slate-800 min-h-screen w-full flex flex-col overflow-x-hidden" x-data="seatMapApp()">

    <!-- HEADER RESPONSIF RAPI -->
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

    <!-- JS CONTROLLER & RESPONSIF SCALE LOGIC -->
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

        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('denahContainer');
            const lorong = document.getElementById('lorongTengah');
            const zoomContent = document.getElementById('zoomContent');
            const zoomWrapper = document.getElementById('zoomWrapper');
            const zoomIndicator = document.getElementById('zoomIndicator');
            
            let currentScale = 1;
            const minScale = 0.25;
            const maxScale = 1.8;
            let holdInterval = null;

            function calculateInitialScale() {
                if (!container || !zoomContent) return 1;
                const windowWidth = window.innerWidth;
                const contentWidth = zoomContent.scrollWidth || 1200;

                if (windowWidth < 640) {
                    return Math.max(0.30, Math.min(0.45, (windowWidth - 20) / contentWidth));
                } else if (windowWidth < 1024) {
                    return Math.max(0.60, Math.min(0.80, (windowWidth - 40) / contentWidth));
                }
                return 1;
            }

            function applyZoom() {
                if (!zoomContent || !zoomWrapper) return;
                zoomContent.style.transform = 'none';
                const naturalWidth = zoomContent.offsetWidth;
                const naturalHeight = zoomContent.offsetHeight;

                zoomContent.style.transform = `scale(${currentScale})`;
                zoomContent.style.transformOrigin = '0 0';

                zoomWrapper.style.width = (naturalWidth * currentScale) + 'px';
                zoomWrapper.style.height = (naturalHeight * currentScale) + 'px';

                if (zoomIndicator) zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
            }

            function centerToLorong() {
                if (container && lorong) {
                    const containerWidth = container.clientWidth;
                    const lorongLeft = lorong.offsetLeft * currentScale;
                    const lorongWidth = lorong.offsetWidth * currentScale;
                    container.scrollLeft = Math.max(0, lorongLeft - (containerWidth / 2) + (lorongWidth / 2));
                }
            }

            window.adjustZoom = function(amount) {
                currentScale += amount;
                if (currentScale > maxScale) currentScale = maxScale;
                if (currentScale < minScale) currentScale = minScale;
                applyZoom();
            };

            window.resetZoom = function() {
                currentScale = calculateInitialScale();
                applyZoom();
                centerToLorong();
            };

            setTimeout(() => {
                currentScale = calculateInitialScale();
                applyZoom();
                centerToLorong();
            }, 100);

            window.addEventListener('resize', () => {
                applyZoom();
            });

            window.startZoomHold = function(amount) {
                window.adjustZoom(amount);
                holdInterval = setInterval(() => window.adjustZoom(amount), 100);
            };

            window.stopZoomHold = function() {
                if (holdInterval) { clearInterval(holdInterval); holdInterval = null; }
            };

            let initialPinchDistance = null;
            if (container) {
                container.addEventListener('touchstart', (e) => {
                    if (e.touches.length === 2) {
                        initialPinchDistance = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                    }
                }, { passive: true });

                container.addEventListener('touchmove', (e) => {
                    if (e.touches.length === 2 && initialPinchDistance) {
                        const dist = Math.hypot(e.touches[0].pageX - e.touches[1].pageX, e.touches[0].pageY - e.touches[1].pageY);
                        const diff = dist - initialPinchDistance;
                        if (Math.abs(diff) > 10) {
                            window.adjustZoom(diff > 0 ? 0.02 : -0.02);
                            initialPinchDistance = dist;
                        }
                    }
                }, { passive: true });

                container.addEventListener('touchend', () => { initialPinchDistance = null; });
            }
        });
    </script>
</body>

</html>