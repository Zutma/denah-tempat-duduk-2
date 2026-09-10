<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Kursi Wisuda - Interaktif</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Ring Highlight saat kursi dipilih */
        .selected-seat-ring {
            outline: 4px solid #0284c7;
            outline-offset: 2px;
            transform: scale(1.1);
        }

        /* Pulse & Glow Animation saat hasil pencarian */
        @keyframes seatPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.8), 0 0 15px rgba(2, 132, 199, 0.6);
                transform: scale(1.12);
            }
            50% {
                box-shadow: 0 0 0 14px rgba(2, 132, 199, 0), 0 0 25px rgba(2, 132, 199, 0.9);
                transform: scale(1.22);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(2, 132, 199, 0), 0 0 15px rgba(2, 132, 199, 0.6);
                transform: scale(1.12);
            }
        }

        .search-highlight-seat {
            animation: seatPulse 1.4s infinite ease-in-out;
            z-index: 25 !important;
            outline: 4px solid #0284c7 !important;
            outline-offset: 3px;
        }

        /* Custom Scrollbar Tipis */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans min-h-screen w-full flex flex-col items-center py-8" x-data="seatMapApp()">

    <div class="w-full px-4 md:px-8 text-center flex-grow flex flex-col">

        <!-- Header Title -->
        <h1 class="text-xl md:text-2xl font-bold text-slate-800 mb-6">
            <?php if ($activeSession): ?>
                <?= htmlspecialchars($activeSession['event_name']) ?> - Sesi <?= htmlspecialchars($activeSession['session']) ?>
            <?php else: ?>
                Denah Wisuda
            <?php endif; ?>
        </h1>

        <!-- Dropdown Pilihan Sesi / Event Wisuda -->
        <?php if (!empty($publishedSessions)): ?>
            <div class="mb-6 max-w-md mx-auto w-full">
                <form method="GET" id="sessionForm">
                    <select name="session_id" id="session_id" onchange="document.getElementById('sessionForm').submit()"
                        class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl shadow-sm text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="" <?= !$activeSession ? 'selected' : '' ?>>
                            -- Pilih Acara Wisuda --
                        </option>
                        <?php foreach ($publishedSessions as $session): ?>
                            <option value="<?= $session['id'] ?>"
                                <?= $activeSession && $activeSession['id'] == $session['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($session['event_name'] ?? 'Event') ?> — Sesi <?= htmlspecialchars($session['session']) ?>
                                (<?= date('d M Y', strtotime($session['date'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php endif; ?>

        <?php
    $seatMapInfo = [];
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

    $allGraduatesList = [];
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
?>

        <!-- SEARCH BAR & CARD HASIL PENCARIAN (LIVE INSTANT SEARCH) -->
        <?php if ($activeSession): ?>
            <div class="mb-8 max-w-lg mx-auto w-full">
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input="searchedSeatIdClicked = null"
                        placeholder="🔍 Ketik Nama, NRP, atau No. Kursi..."
                        class="w-full px-5 py-3 border border-slate-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm text-center font-medium bg-white">
                </div>

                <div x-show="searchQuery.trim() !== ''" x-cloak class="mt-3">
                    <div class="flex items-center justify-between text-xs text-slate-500 px-3">
                        <span>Ditemukan <strong class="text-slate-800" x-text="filteredGraduates.length"></strong> hasil
                            untuk "<strong x-text="searchQuery"></strong>"</span>
                        <button type="button" @click="clearSelection()" class="text-sky-600 font-semibold hover:underline cursor-pointer">Reset</button>
                    </div>

                    <!-- Card List Hasil Pencarian Live -->
                    <template x-if="filteredGraduates.length > 0">
                        <div class="mt-3 bg-white border border-slate-200 rounded-2xl shadow-sm p-2 text-left max-h-56 overflow-y-auto custom-scrollbar divide-y divide-slate-100">
                            <template x-for="g in filteredGraduates" :key="g.seat_id">
                                <button type="button"
                                    @click="focusSeat(g.seat_id, g)"
                                    class="w-full p-2.5 hover:bg-sky-50/80 rounded-xl transition-colors flex items-center justify-between group cursor-pointer focus:outline-none">
                                    <div>
                                        <p class="text-xs font-bold text-slate-800 group-hover:text-sky-700" x-text="g.name"></p>
                                        <p class="text-[11px] text-slate-500">
                                            NRP: <span x-text="g.nrp"></span> • <span x-text="g.faculty"></span> • <span x-text="g.prodi"></span>
                                        </p>
                                    </div>
                                    <span class="px-2.5 py-1 bg-sky-100 text-sky-700 text-xs font-bold rounded-lg group-hover:bg-sky-500 group-hover:text-white transition-colors whitespace-nowrap"
                                          x-text="'Kursi ' + g.seat_code">
                                    </span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="filteredGraduates.length === 0">
                        <div class="mt-3 bg-white border border-slate-200 rounded-2xl shadow-sm p-4 text-center text-xs text-slate-500">
                            Tidak ada wisudawan atau kursi yang cocok dengan "<span x-text="searchQuery" class="font-bold"></span>".
                        </div>
                    </template>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tampilan Jika Sesi Kosong / Belum Ada Event -->
        <?php if (!$activeSession || isset($message)): ?>
            <div
                class="bg-amber-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-xl max-w-md mx-auto my-12 text-sm">
                <?= htmlspecialchars($message ?? 'Belum ada data sesi wisuda yang tersedia.') ?>
            </div>
        <?php else: ?>
            <!-- PANGGUNG UTAMA -->
            <div
                class="bg-slate-800 text-white font-bold tracking-widest py-4 rounded-lg mb-6 shadow-md w-full max-w-6xl mx-auto text-xs md:text-sm">
                PANGGUNG UTAMA / REKTORAT
            </div>

            <!-- CONTAINER UTAMA DENAH -->
            <div class="w-full max-w-6xl mx-auto relative">
                
                <!-- TOMBOL KONTROL ZOOM (HOLD TO ZOOM & RESET) -->
                <div class="flex justify-end mb-3">
                    <div class="inline-flex items-center bg-white border border-slate-200 shadow-sm rounded-xl overflow-hidden select-none">
                        <button type="button" 
                            onmousedown="window.startZoomHold(-0.05)" 
                            onmouseup="window.stopZoomHold()" 
                            onmouseleave="window.stopZoomHold()"
                            ontouchstart="window.startZoomHold(-0.05)" 
                            ontouchend="window.stopZoomHold()"
                            class="px-3.5 py-1.5 text-slate-700 hover:bg-slate-100 font-bold text-sm transition focus:outline-none" title="Tahan untuk Zoom Out">-</button>
                        
                        <span id="zoomIndicator" class="px-3 py-1.5 text-xs font-bold text-slate-600 border-x border-slate-200 min-w-[50px] text-center">100%</span>
                        
                        <button type="button" 
                            onmousedown="window.startZoomHold(0.05)" 
                            onmouseup="window.stopZoomHold()" 
                            onmouseleave="window.stopZoomHold()"
                            ontouchstart="window.startZoomHold(0.05)" 
                            ontouchend="window.stopZoomHold()"
                            class="px-3.5 py-1.5 text-slate-700 hover:bg-slate-100 font-bold text-sm transition focus:outline-none" title="Tahan untuk Zoom In">+</button>
                        
                        <button type="button" onclick="window.resetZoom()" class="px-3.5 py-1.5 text-xs font-semibold text-sky-600 hover:bg-sky-50 border-l border-slate-200 transition focus:outline-none">Reset</button>
                    </div>
                </div>

                <!-- AREA SCROLL HORIZONTAL -->
                <div id="denahContainer"
                    class="w-full overflow-x-auto pb-16 pt-2 cursor-grab active:cursor-grabbing scroll-smooth custom-scrollbar">
                    
                    <!-- KONTEN KURSI (URUTAN: SAYAP KIRI -> LORONG -> SAYAP KANAN) -->
                    <div id="zoomContent" class="flex flex-nowrap justify-center mx-auto min-w-max px-4 gap-6 md:gap-10 transition-transform duration-75 origin-top">

                        <!-- 1. SAYAP KIRI -->
                        <div class="flex flex-col gap-6 items-end">
                            <?php if (empty($leftRows)): ?>
                                <p class="text-slate-400 text-sm italic">Belum ada data kursi sayap kiri.</p>
                            <?php else: ?>
                                <?php foreach ($leftRows as $row): ?>
                                    <div
                                        class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-sm border border-slate-200">
                                        <span class="font-bold text-slate-400 w-6 text-sm text-center"><?= htmlspecialchars($row['row']) ?></span>
                                        <div class="flex flex-row flex-nowrap gap-3">
                                            <?php foreach ($row['seats'] as $seat): ?>
                                                <?php
                                                    $hasGraduate = !empty($seat['graduate_name']);
                                                    $facultyColor = $hasGraduate && !empty($seat['faculty_color'])
                                                        ? $seat['faculty_color']
                                                        : '#cbd5e1';

                                                    $graduateData = $hasGraduate
                                                        ? [
                                                            'seat_id' => $seat['id'],
                                                            'seat_code' => $seatMapInfo[$seat['id']]['code'] ?? '-',
                                                            'name' => $seat['graduate_name'],
                                                            'nrp' => $seat['nrp'],
                                                            'prodi' => $seat['prodi_name'] ?? '-',
                                                            'faculty' => !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-'),
                                                            'faculty_name' => $seat['faculty_name'] ?? '-',
                                                            'color' => $facultyColor,
                                                        ]
                                                        : null;
                                                ?>

                                                <div class="flex flex-col items-center">
                                                    <button type="button" id="seat-<?= $seat['id'] ?>"
                                                        @click="selectSeat(<?= $seat['id'] ?>, <?= $graduateData ? htmlspecialchars(json_encode($graduateData), ENT_QUOTES, 'UTF-8') : 'null' ?>)"
                                                        :class="{ 'search-highlight-seat': searchedSeatIds.includes(<?= $seat['id'] ?>), 'selected-seat-ring': selectedSeatId === <?= $seat['id'] ?> && !searchedSeatIds.includes(<?= $seat['id'] ?>) }"
                                                        class="w-12 h-12 md:w-14 md:h-14 rounded-lg flex items-center justify-center font-bold text-xs md:text-sm shadow transition-all duration-150 hover:scale-105 cursor-pointer focus:outline-none"
                                                        style="background-color: <?= $hasGraduate ? htmlspecialchars($facultyColor) : '#e2e8f0' ?>; color: <?= $hasGraduate ? '#ffffff' : '#64748b' ?>;">
                                                        <?= htmlspecialchars($seatMapInfo[$seat['id']]['code'] ?? '-') ?>
                                                    </button>

                                                    <?php if ($hasGraduate): ?>
                                                        <?php
                                                            $facCode = !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-');
                                                            $prodiName = $seat['prodi_name'] ?? '-';
                                                        ?>
                                                        <div class="mt-1 flex flex-col items-center leading-tight w-14 md:w-16 cursor-default" title="<?= htmlspecialchars($facCode . ' - ' . $prodiName) ?>">
                                                            <span class="text-[10px] font-bold text-slate-800 truncate w-full text-center">
                                                                <?= htmlspecialchars($facCode) ?>
                                                            </span>
                                                            <span class="text-[9px] font-medium text-slate-500 truncate w-full text-center">
                                                                <?= htmlspecialchars($prodiName) ?>
                                                            </span>
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

                        <!-- 2. LORONG TENGAH (PAS DI TENGAH) -->
                        <div id="lorongTengah" class="flex items-stretch mx-2">
                            <div
                                class="w-12 md:w-16 border-x-2 border-dashed border-slate-400 opacity-60 relative flex items-center justify-center">
                                <span class="absolute -rotate-90 text-slate-400 font-bold tracking-[0.3em] text-xs whitespace-nowrap">
                                    LORONG
                                </span>
                            </div>
                        </div>

                        <!-- 3. SAYAP KANAN -->
                        <div class="flex flex-col gap-6 items-start">
                            <?php if (empty($rightRows)): ?>
                                <p class="text-slate-400 text-sm italic">Belum ada data kursi sayap kanan.</p>
                            <?php else: ?>
                                <?php foreach ($rightRows as $row): ?>
                                    <div
                                        class="flex items-center gap-3 bg-white p-3 rounded-xl shadow-sm border border-slate-200">
                                        <div class="flex flex-row flex-nowrap gap-3">
                                            <?php foreach ($row['seats'] as $seat): ?>
                                                <?php
                                                    $hasGraduate = !empty($seat['graduate_name']);
                                                    $facultyColor = $hasGraduate && !empty($seat['faculty_color'])
                                                        ? $seat['faculty_color']
                                                        : '#cbd5e1';

                                                    $graduateData = $hasGraduate
                                                        ? [
                                                            'seat_id' => $seat['id'],
                                                            'seat_code' => $seatMapInfo[$seat['id']]['code'] ?? '-',
                                                            'name' => $seat['graduate_name'],
                                                            'nrp' => $seat['nrp'],
                                                            'prodi' => $seat['prodi_name'] ?? '-',
                                                            'faculty' => !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-'),
                                                            'faculty_name' => $seat['faculty_name'] ?? '-',
                                                            'color' => $facultyColor,
                                                        ]
                                                        : null;
                                                ?>

                                                <div class="flex flex-col items-center">
                                                    <button type="button" id="seat-<?= $seat['id'] ?>"
                                                        @click="selectSeat(<?= $seat['id'] ?>, <?= $graduateData ? htmlspecialchars(json_encode($graduateData), ENT_QUOTES, 'UTF-8') : 'null' ?>)"
                                                        :class="{ 'search-highlight-seat': searchedSeatIds.includes(<?= $seat['id'] ?>), 'selected-seat-ring': selectedSeatId === <?= $seat['id'] ?> && !searchedSeatIds.includes(<?= $seat['id'] ?>) }"
                                                        class="w-12 h-12 md:w-14 md:h-14 rounded-lg flex items-center justify-center font-bold text-xs md:text-sm shadow transition-all duration-150 hover:scale-105 cursor-pointer focus:outline-none"
                                                        style="background-color: <?= $hasGraduate ? htmlspecialchars($facultyColor) : '#e2e8f0' ?>; color: <?= $hasGraduate ? '#ffffff' : '#64748b' ?>;">
                                                        <?= htmlspecialchars($seatMapInfo[$seat['id']]['code'] ?? '-') ?>
                                                    </button>

                                                    <?php if ($hasGraduate): ?>
                                                        <?php
                                                            $facCode = !empty($seat['faculty_code']) ? $seat['faculty_code'] : ($seat['faculty_name'] ?? '-');
                                                            $prodiName = $seat['prodi_name'] ?? '-';
                                                        ?>
                                                        <div class="mt-1 flex flex-col items-center leading-tight w-14 md:w-16 cursor-default" title="<?= htmlspecialchars($facCode . ' - ' . $prodiName) ?>">
                                                            <span class="text-[10px] font-bold text-slate-800 truncate w-full text-center">
                                                                <?= htmlspecialchars($facCode) ?>
                                                            </span>
                                                            <span class="text-[9px] font-medium text-slate-500 truncate w-full text-center">
                                                                <?= htmlspecialchars($prodiName) ?>
                                                            </span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-[10px] mt-1 text-slate-400 font-medium text-center">-</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <span class="font-bold text-slate-400 w-6 text-sm text-center"><?= htmlspecialchars($row['row']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>

            <!-- MODAL DETAIL KURSI -->
            <div x-show="activeModalData" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
                @click.self="closeModal()">
                <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full p-6 text-left transform transition-all"
                    @keydown.escape.window="closeModal()">

                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full"
                                :style="`background-color: ${activeModalData?.color || '#cbd5e1'}`"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Detail Tempat
                                Duduk</span>
                        </div>
                        <button @click="closeModal()"
                            class="text-slate-400 hover:text-slate-600 font-bold text-sm cursor-pointer focus:outline-none">✕</button>
                    </div>

                    <template x-if="activeModalData">
                        <div class="space-y-3">
                            <div>
                                <span
                                    class="inline-block px-2.5 py-1 bg-sky-100 text-sky-800 font-extrabold text-sm rounded-md mb-2">
                                    Kursi <span x-text="activeModalData.seat_code"></span>
                                </span>
                                <h4 class="text-base font-bold text-slate-800" x-text="activeModalData.name"></h4>
                                <p class="text-xs text-slate-500" x-text="`NRP: ${activeModalData.nrp}`"></p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 space-y-1 text-xs text-slate-600">
                                <p><span class="font-semibold text-slate-700">Program Studi:</span> <span
                                        x-text="activeModalData.prodi"></span></p>
                                <p><span class="font-semibold text-slate-700">Fakultas:</span> <span
                                        x-text="activeModalData.faculty_name && activeModalData.faculty_name !== '-' ? activeModalData.faculty_name : activeModalData.faculty"></span></p>
                            </div>
                        </div>
                    </template>

                    <div class="mt-5 pt-3 border-t border-slate-100 text-right">
                        <button @click="closeModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer focus:outline-none">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

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

        // ==========================================
        // AUTO-CENTER TEPAT DI LORONG & HOLD-TO-ZOOM
        // ==========================================
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('denahContainer');
            const lorong = document.getElementById('lorongTengah');
            
            // Auto-center presisi langsung mengarahkan pandangan pas di tengah "LORONG"
            function centerToLorong() {
                if (container && lorong) {
                    const containerWidth = container.clientWidth;
                    const lorongLeft = lorong.offsetLeft;
                    const lorongWidth = lorong.offsetWidth;
                    
                    // Hitung posisi scroll agar garis lorong tepat berada di tengah layar
                    container.scrollLeft = lorongLeft - (containerWidth / 2) + (lorongWidth / 2);
                }
            }

            setTimeout(centerToLorong, 50); // Eksekusi setelah elemen selesai dirender

            let currentScale = 1;
            const minScale = 0.4;
            const maxScale = 1.8;
            let holdInterval = null;

            window.adjustZoom = function(amount) {
                currentScale += amount;
                if (currentScale > maxScale) currentScale = maxScale;
                if (currentScale < minScale) currentScale = minScale;

                applyZoom();
            };

            window.resetZoom = function() {
                currentScale = 1;
                applyZoom();
                centerToLorong();
            };

            function applyZoom() {
                const zoomContent = document.getElementById('zoomContent');
                const zoomIndicator = document.getElementById('zoomIndicator');
                
                if (zoomContent) {
                    zoomContent.style.transform = `scale(${currentScale})`;
                }
                if (zoomIndicator) {
                    zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
                }
            }

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

            // Dukungan Ctrl + Scroll Mouse
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