<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — Denah Tempat Duduk Wisuda ITS</title>
    <link rel="stylesheet" href="<?= url('css/tailwind-built.css') ?>">
    <!-- Ganti ke versi tetap (mis. ?v=1) saat production agar cache browser terpakai -->
    <link rel="stylesheet" href="<?= url('css/public-style.css?v=' . time()) ?>">

    <style>
        /* ============================================================
           1. LAYOUT HALAMAN (jarak, lebar, susunan)
           ============================================================ */
        .about-main {
            width: 100%; max-width: 56rem; margin: 0 auto; padding: 32px 20px 48px;
            flex: 1 1 auto; display: flex; flex-direction: column; align-items: center;
            justify-content: flex-start; box-sizing: border-box; position: relative;
            z-index: 10;
        }
        .about-head { text-align: center; margin-bottom: 28px; }
        .about-badge { margin-bottom: 12px; }
        .about-sub { max-width: 32rem; margin: 10px auto 0; font-size: .8rem; line-height: 1.6; color: #64748b; }
        body { overflow-x: clip; }   /* cegah scroll ke samping tanpa memotong icon */

        /* ============================================================
           2. DEVELOPER (kartu, animasi kartu, garis warna, kilau)
           ============================================================ */
        .dev-grid { display: grid; grid-template-columns: 1fr; gap: 16px; width: 100%; }
        .dev-card {
            position: relative; z-index: 10; overflow: hidden; background: #fff;
            border: 1px solid rgba(226, 232, 240, .9); border-radius: 24px;
            padding: 32px 22px 26px; box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
            text-align: center; display: flex; flex-direction: column; align-items: center;
            transition: scale .3s ease; cursor: pointer; -webkit-tap-highlight-color: transparent;
            user-select: none; will-change: transform;
            animation: cardIn .8s cubic-bezier(.2, .8, .2, 1) both, idle 6s ease-in-out 1s infinite;
        }
        .dev-card.dev-1 { animation-delay: .15s, 1s; }
        .dev-card.dev-2 { animation-delay: .35s, -2s; }
        .dev-card:hover, .dev-card:active { scale: 1.03; }

        .dev-card::after {
            content: ""; position: absolute; top: 0; left: 0; width: 40%; height: 100%;
            pointer-events: none; transform: translateX(-160%) skewX(-20deg);
            will-change: transform; animation: shine 6s ease-in-out 2s infinite;
        }
        .dev-card.dev-1::after { background: linear-gradient(100deg, transparent, rgba(14, 165, 233, .12), transparent); }
        .dev-card.dev-2::after { background: linear-gradient(100deg, transparent, rgba(245, 158, 11, .14), transparent); }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(36px) scale(.94); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes idle {
            0%, 100% { translate: 0 0; rotate: -.7deg; }
            50%      { translate: 0 -6px; rotate: .7deg; }
        }
        @keyframes shine {
            0%, 60% { transform: translateX(-160%) skewX(-20deg); }
            100%    { transform: translateX(420%) skewX(-20deg); }
        }
        @keyframes stripeSlide { to { transform: translateX(-50%); } }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dev-card::before {
            content: ""; position: absolute; top: 0; left: 0; width: 200%; height: 5px;
            will-change: transform; animation: stripeSlide 6s linear infinite;
        }
        .dev-card.dev-1::before { background: linear-gradient(90deg, #1e3a5f, #0ea5e9, #1e3a5f, #0ea5e9, #1e3a5f); }
        .dev-card.dev-2::before { background: linear-gradient(90deg, #facc15, #f59e0b, #facc15, #f59e0b, #facc15); }

        .about-badge { animation: fadeUp .6s ease-out both; }
        .about-head h2 { animation: fadeUp .7s ease-out .1s both; }
        .about-sub { animation: fadeUp .7s ease-out .2s both; }

        @media (prefers-reduced-motion: reduce) {
            .dev-card, .dev-card::before, .dev-card::after,
            .about-badge, .about-head h2, .about-sub { animation: none !important; }
        }
        .dev-name { margin: 0; font-size: 1.4rem; font-weight: 800; color: #0f172a; letter-spacing: -.01em; }
        .dev-role { margin: 6px 0 0; font-size: .75rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .dev-quote {
            width: 100%; box-sizing: border-box; margin-top: 18px; padding: 14px 16px;
            display: flex; align-items: center; justify-content: center;
            overflow-wrap: anywhere;   /* kata yang sangat panjang tetap turun baris, tidak keluar kartu */
            background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px;
            font-size: .8rem; font-style: italic; line-height: 1.6; color: #475569;
        }
        .about-foot { margin-top: 8px; text-align: center; font-size: .75rem; font-weight: 500; color: #94a3b8; }

        @media (min-width: 640px) {
            .about-main { padding: 48px 24px 56px; }
            .about-head { margin-bottom: 20px; }
            .dev-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
        }

        /* ============================================================
           3. SOSMED ORBIT (icon berputar + kotak pilihan profil)
           ============================================================ */
        /* panggung orbit: ruang atas-bawah supaya icon tidak menabrak teks */
        
        .orbit-stage { position: relative; width: 100%; padding: 68px 0; }
        .orbit-layer { position: absolute; inset: 0; pointer-events: none; }
        .orbit-item {
            position: absolute; left: 50%; top: 50%; width: 44px; height: 44px;
            margin: -22px 0 0 -22px; border-radius: 50%; background: #fff;
            box-shadow: 0 6px 16px rgba(15, 23, 42, .18); display: flex;
            align-items: center; justify-content: center; opacity: 0;
            will-change: transform, opacity; pointer-events: auto; text-decoration: none;
            border: 0; padding: 0; cursor: pointer; -webkit-tap-highlight-color: transparent;
        }
        .orbit-item .oi { display: flex; transition: transform .2s ease; }
        .orbit-item:hover .oi, .orbit-item:focus-visible .oi { transform: scale(1.25); }
        .orbit-item:focus-visible { outline: 2px solid #0ea5e9; outline-offset: 2px; }
        #orbitPick { position: fixed; z-index: 10000; display: none; min-width: 160px; padding: 8px; background: #0f172a; border-radius: 14px; box-shadow: 0 12px 32px rgba(0,0,0,.35); font: 600 12px sans-serif; }
        #orbitPick .t { padding: 2px 8px 8px; color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: .06em; }
        #orbitPick a { display: block; padding: 9px 10px; border-radius: 9px; color: #f1f5f9; text-decoration: none; }
        #orbitPick a:hover, #orbitPick a:focus-visible { background: #1e3a5f; }
        .orbit-item svg { width: 24px; height: 24px; display: block; }

        /* ============================================================
           4. MUSIK (pemutar musik melayang — easter egg)
           ============================================================ */
        #musicWidget {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex;
            flex-direction: column; align-items: flex-end; gap: 8px; font-family: sans-serif;
        }
        #toggleBtn {
            background: linear-gradient(135deg, #1e3a5f, #0ea5e9); color: #fff; border: none;
            border-radius: 999px; padding: 10px 20px; font-size: 13px; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 20px rgba(0, 0, 0, .3);
            display: flex; align-items: center; gap: 7px;
        }
        #playerCard { display: none; width: 280px; background: #0f172a; border-radius: 16px; overflow: hidden; box-shadow: 0 12px 40px rgba(0, 0, 0, .5); }
        #ytPlayerWrap { width: 100%; aspect-ratio: 16 / 9; background: #000; }
        #ytPlayerWrap iframe { width: 100%; height: 100%; display: block; }
        .mp-controls { padding: 10px 14px 6px; display: flex; align-items: center; gap: 8px; }
        .mp-btn { background: #1e293b; color: #94a3b8; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 15px; cursor: pointer; flex-shrink: 0; }
        #playPauseBtn { background: #0ea5e9; color: #fff; border: none; border-radius: 50%; width: 36px; height: 36px; font-size: 16px; cursor: pointer; flex-shrink: 0; }
        .mp-info { flex: 1; overflow: hidden; min-width: 0; }
        #nowPlayingTitle { color: #f1f5f9; font-size: 11px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mp-sub { color: #475569; font-size: 10px; margin-top: 1px; }
        .mp-list-wrap { border-top: 1px solid #1e293b; max-height: 120px; overflow-y: auto; }
        .mp-list-title { padding: 6px 14px 4px; color: #475569; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; }
        #playlistUL { list-style: none; margin: 0; padding: 0 0 8px; }
        .mp-item { padding: 7px 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; background: transparent; }
        .mp-item:hover { background: #1e293b; }
        .mp-item.active, .mp-item.active:hover { background: #1e3a5f; }
        .mp-num { color: #475569; font-size: 10px; min-width: 16px; }
        .mp-name { color: #cbd5e1; font-size: 11px; font-weight: 600; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mp-item.active .mp-num { color: #38bdf8; }
        .mp-item.active .mp-name { color: #f8fafc; }

        body.player-open .about-main { padding-bottom: 470px; }
        @media (min-width: 900px) and (min-height: 760px) {
            body.player-open .about-main { padding-bottom: 56px; }
        }
        @media (max-width: 480px) {
            #musicWidget { left: 12px; right: 12px; bottom: 12px; }
            #playerCard { width: 100%; }
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 min-h-screen w-full flex flex-col justify-between relative bg-slate-50">

    <!-- ============================================================
         1. HEADER / NAVIGASI
         ============================================================ -->
    <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="<?= url('images/LOGO.png') ?>" alt="ITS Logo" class="w-9 h-9 object-contain">
                <div class="h-7 w-px bg-slate-200"></div>
                <div class="flex flex-col leading-none">
                    <span class="font-friz text-[10px] font-bold uppercase tracking-wider text-its-blue-light mb-0.5">SISTEM INFORMASI WISUDA</span>
                    <h1 class="font-friz text-base font-bold uppercase text-its-blue tracking-tight">TIM DEVELOPER</h1>
                </div>
            </div>
            <a href="<?= url('') ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-its-blue hover:bg-its-blue-light text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                ← Kembali ke Denah
            </a>
        </div>
    </header>

    <!-- ============================================================
         2. ISI HALAMAN (judul, developer, sosmed, footer)
         ============================================================ -->
    <main class="about-main">

        <div class="about-head">
            <span class="about-badge inline-block px-3.5 py-1 bg-its-yellow/20 text-its-blue border border-its-yellow/60 text-xs font-extrabold rounded-full uppercase tracking-widest shadow-2xs">
                Project Credits
            </span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Behind The Scene</h2>
            <p class="about-sub">
                Aplikasi Denah Tempat Duduk Wisuda ITS dikembangkan secara kolaboratif oleh 2 developer hebat di bawah ini:
            </p>
        </div>

        <!-- Panggung: kartu developer di tengah, icon sosmed berputar mengelilinginya -->
        <div class="orbit-stage" id="orbitStage">
            <!-- [SOSMED] lapisan icon orbit (tombol dibuat otomatis oleh script sosmed) -->
            <div class="orbit-layer" id="orbitLayer" aria-hidden="true"></div>

            <!-- [DEVELOPER] dua kartu developer -->

            <div class="dev-grid">
                <!-- DEVELOPER 1 -->
                <div class="dev-card dev-1">
                    <h3 class="dev-name">MALIK</h3>
                    <p class="dev-role text-its-blue-light">(Malik)</p>
                    <div class="dev-quote">"MARI KITA BERMAIN"</div>
                </div>

                <!-- DEVELOPER 2 — SILAKAN EDIT NAMA, PERAN, DAN QUOTE -->
                <div class="dev-card dev-2">
                    <h3 class="dev-name">Rizviqar Fajar Aslam Utama</h3>
                    <p class="dev-role text-amber-600">(ASLAM)</p>
                    <div class="dev-quote">"[Isikan quote / pesan dari Developer 2]"</div>
                </div>
            </div>
        </div>

        <div class="about-foot">
            © <?= date('Y') ?> Direktorat Pendidikan Sarjana dan Pascasarjana • Crafted with ❤️
        </div>
    </main>

    <!-- ============================================================
         3. MUSIK — pemutar melayang (easter egg)
         ============================================================ -->
    <div id="musicWidget">
        <button id="toggleBtn" type="button" onclick="toggleMusicPlayer()" aria-label="Buka atau tutup pemutar musik">
            🎵 <span id="toggleLabel">Putar Musik</span>
        </button>
        <div id="playerCard">
            <div id="ytPlayerWrap"></div>
            <div class="mp-controls">
                <button type="button" class="mp-btn" onclick="prevSong()" title="Sebelumnya" aria-label="Lagu sebelumnya">⏮</button>
                <button type="button" id="playPauseBtn" onclick="togglePlayPause()" title="Play/Pause" aria-label="Putar atau jeda">▶</button>
                <button type="button" class="mp-btn" onclick="nextSong()" title="Berikutnya" aria-label="Lagu berikutnya">⏭</button>
                <div class="mp-info">
                    <div id="nowPlayingTitle">-</div>
                    <div class="mp-sub">Now Playing</div>
                </div>
            </div>
            <div class="mp-list-wrap">
                <div class="mp-list-title">Daftar Lagu</div>
                <ul id="playlistUL"></ul>
            </div>
        </div>
    </div>

    <!-- ============================================================
         4. SCRIPT DEVELOPER — quote bergantian + kartu bertukar posisi
         ============================================================ -->
    <script>
    // ================================================================
    // 💬 QUOTE BERGANTIAN (setiap developer boleh punya banyak quote)
    // ================================================================
    (function () {
        // ---------- ISI QUOTE DI SINI ----------
        // Tulis teksnya saja, tanda kutip " " ditambahkan otomatis.
        // Tambah quote baru = tambah satu baris teks + koma di dalam [ ].
        const QUOTE_INTERVAL = 8;   // detik per quote (mis. 6 atau 10)
        const QUOTES = {
            'dev-1': [                                   // Developer 1 (Malik)
                'WELCOME GAIS, SENANG MELIHAT KALIAN BISA MASUK SINI🫣',
                'ASIKINN AJA, dunia bukan tentang ngejar karier 😵‍💫',
                'BTW SELAMAT JUGA YA BUAT PARA WISUDAA 👨‍🎓👩‍🎓🙌',
                'Dia lebih baik daripada kamu. Baguslah gw mau ngoding dulu, uda DEADLINE soalnya'
            ],
            'dev-2': [                                   // Developer 2 ()
                '[Isikan quote / pesan dari Developer 2]',
                // 'Quote kedua Developer 2'
            ]
        };
        // ---------------------------------------

        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        Object.keys(QUOTES).forEach((cls, n) => {
            const box  = document.querySelector('.' + cls + ' .dev-quote');
            const list = QUOTES[cls].filter(q => q && q.trim());
            if (!box || !list.length) return;

            const show = i => { box.textContent = '"' + list[i] + '"'; };
            show(0);
            if (list.length < 2) return;                 // 1 quote saja: tidak perlu bergantian

            // Kunci tinggi kotak = quote terpanjang, supaya kartu tidak "loncat"
            function fit() {
                const keep = box.textContent;
                box.style.minHeight = '';
                let max = 0;
                list.forEach((_, i) => { show(i); max = Math.max(max, box.offsetHeight); });
                box.textContent = keep;
                box.style.minHeight = max + 'px';
            }
            fit();
            if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
            let t;
            window.addEventListener('resize', () => { clearTimeout(t); t = setTimeout(fit, 200); });

            // Ganti quote: memudar keluar, ganti teks, memudar masuk (hanya opacity + transform)
            let i = 0;
            function next() {
                if (!document.hidden) {
                    i = (i + 1) % list.length;
                    if (reduce || !box.animate) {
                        show(i);
                    } else {
                        const out = box.animate(
                            [{ opacity: 1, transform: 'translateY(0)' }, { opacity: 0, transform: 'translateY(-8px)' }],
                            { duration: 350, easing: 'ease-in', fill: 'forwards' });
                        out.onfinish = () => {
                            show(i);
                            out.cancel();
                            box.animate(
                                [{ opacity: 0, transform: 'translateY(8px)' }, { opacity: 1, transform: 'translateY(0)' }],
                                { duration: 450, easing: 'ease-out' });
                        };
                    }
                }
                setTimeout(next, QUOTE_INTERVAL * 1000);
            }
            // kartu ke-2 bergeser setengah interval supaya tidak berganti bersamaan
            setTimeout(next, QUOTE_INTERVAL * 1000 + n * QUOTE_INTERVAL * 500);
        });
    })();

    // ================================================================
    // 🔀 KARTU BERTUKAR POSISI
    // ================================================================
    (function () {
        const grid = document.querySelector('.dev-grid');
        if (!grid || !grid.animate) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const DURATION = 1100;   // lama animasi tukar (ms)
        const INTERVAL = 4500;   // jeda antar tukar (ms)
        let swapping = false;

        function swapCards() {
            if (swapping || grid.children.length < 2) return;
            swapping = true;
            const cards  = Array.from(grid.children);
            const before = cards.map(c => c.getBoundingClientRect());
            grid.insertBefore(grid.lastElementChild, grid.firstElementChild);

            let done = 0;
            const finish = () => { if (++done === cards.length) swapping = false; };

            cards.forEach((card, i) => {
                const after    = card.getBoundingClientRect();
                const dx       = before[i].left - after.left;
                const dy       = before[i].top  - after.top;
                const vertical = Math.abs(dy) >= Math.abs(dx);
                const dir      = i === 0 ? 1 : -1;
                const side     = vertical ? 14 : 28;
                const mx       = dx / 2 + (vertical ? dir * side : 0);
                const my       = dy / 2 + (vertical ? 0 : dir * side);

                card.style.zIndex = i === 0 ? 12 : 11;
                const anim = card.animate([
                    { transform: `translate(${dx}px, ${dy}px) rotate(0deg) scale(1)` },
                    { transform: `translate(${mx}px, ${my}px) rotate(${dir * 5}deg) scale(${i === 0 ? 1.05 : 0.95})`, offset: 0.5 },
                    { transform: 'translate(0px, 0px) rotate(0deg) scale(1)' }
                ], { duration: DURATION, easing: 'cubic-bezier(.55, 0, .25, 1)' });

                const end = () => { card.style.zIndex = ''; finish(); };
                anim.onfinish = end;
                anim.oncancel = end;
            });
        }

        function loop() {
            if (!document.hidden) swapCards();
            setTimeout(loop, INTERVAL);
        }
        setTimeout(loop, 3500);
        grid.addEventListener('click', swapCards);
    })();
    </script>

    <!-- ============================================================
         5. SCRIPT SOSMED — icon mengorbit, bisa diklik ke profil / beranda platform
         ============================================================ -->
    <script>
    (function () {
        const stage = document.getElementById('orbitStage');
        const layer = document.getElementById('orbitLayer');
        const grid  = document.querySelector('.dev-grid');
        if (!stage || !layer || !grid) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        // ================================================================
        // A. PENGATURAN ANIMASI (satuan detik)
        // ================================================================
        const ORBIT_TIME = 28;   // lama icon tampil berputar sebelum kembali ke tengah
        const EMERGE     = 1.6;  // lama keluar dari tengah
        const RETURN     = 1.6;  // lama kembali ke tengah
        const HIDDEN     = 3;    // lama sembunyi
        const LAP        = 28;   // lama 1 putaran penuh (kecil = lebih cepat)

        // ================================================================
        // 🔗 B. ISI SOSIAL MEDIA DI SINI (OPSIONAL)
        // Isi di dalam tanda kutip pada SOCIALS di bawah. Boleh dikosongkan ('').
        //
        //   github     → username           contoh: 'malik123'
        //   instagram  → username (tanpa @) contoh: 'malik.dev'
        //   tiktok     → username (tanpa @) contoh: 'malikdev'
        //   youtube    → handle channel     contoh: '@malikdev'
        //   spotify    → ID profil Spotify  contoh: '31abcdefgh'
        //   discord    → ID pengguna (angka) contoh: '123456789012345678'
        //   Semua platform juga boleh diisi LINK LENGKAP (berawalan https://)
        //
        // Aturan saat icon diklik:
        //   • Kosong semua        → membuka beranda platform (mis. youtube.com)
        //   • Hanya 1 yang isi    → langsung ke profil developer itu
        //   • Keduanya mengisi    → muncul pilihan nama developer dulu
        // Di HP: kalau aplikasinya terpasang, link terbuka di aplikasi;
        // kalau tidak, terbuka di situs web platform.
        // ================================================================
        const SOCIALS = {
            //            Developer 1 (Malik)   Developer 2
            github:    { malik: '', aslam: 'Zutma' },
            instagram: { malik: '', aslam: '' },
            tiktok:    { malik: '', aslam: '' },
            youtube:   { malik: '', aslam: '' },
            spotify:   { malik: '', aslam: '' },
            discord:   { malik: '', aslam: '' }
        };
        // ================================================================
        // C. DATA PLATFORM — bagian ini tidak perlu diubah
        // ================================================================
        const DEVS = [
            { id: 'malik', label: (document.querySelector('.dev-1 .dev-name') || {}).textContent || 'Developer 1' },
            { id: 'aslam',  label: (document.querySelector('.dev-2 .dev-name') || {}).textContent || 'Developer 2' }
        ];
        const at = v => v.replace(/^@/, '');
        const PLATFORMS = {
            github:    { home: 'https://github.com/',           user: v => 'https://github.com/' + at(v) },
            instagram: { home: 'https://www.instagram.com/',    user: v => 'https://www.instagram.com/' + at(v) },
            tiktok:    { home: 'https://www.tiktok.com/',       user: v => 'https://www.tiktok.com/@' + at(v) },
            youtube:   { home: 'https://www.youtube.com/',      user: v => 'https://www.youtube.com/@' + at(v) },
            spotify:   { home: 'https://open.spotify.com/',     user: v => 'https://open.spotify.com/user/' + v },
            discord:   { home: 'https://discord.com/',          user: v => 'https://discord.com/users/' + v }
        };
        function targetsFor(key) {
            const P = PLATFORMS[key], S = SOCIALS[key] || {}, list = [];
            DEVS.forEach(d => {
                const raw = String(S[d.id] || '').trim();
                if (!raw) return;
                if (/^https?:\/\//i.test(raw)) list.push({ label: d.label, url: raw });   // link lengkap (hanya http/https)
                else list.push({ label: d.label, url: P.user(raw) });
            });
            return list.length ? list : [{ label: null, url: P.home }];
        }

        // ================================================================
        // D. DAFTAR ICON (digambar dengan SVG, tanpa file gambar)
        // ================================================================
        const ITEMS = [
            { name: 'GitHub', svg: '<svg viewBox="0 0 16 16"><path fill="#181717" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>' },
            { name: 'Discord', svg: '<svg viewBox="0 0 24 24"><path fill="#5865F2" d="M5.5 6.2C7 5.2 8.6 4.6 9.7 4.5l.5 1a13 13 0 0 1 3.6 0l.5-1c1.100.1 2.700.7 4.200 1.700 1.600 2.600 2.300 5.400 2.100 8.600-1.300 1-2.600 1.500-3.700 1.800l-.9-1.500c.5-.2 1-.4 1.400-.7l-.4-.3c-2.700 1.200-5.700 1.200-8.400 0l-.4.300c.4.300.9.500 1.400.7l-.9 1.500c-1.100-.3-2.400-.8-3.700-1.800-.2-3.200.5-6 2.100-8.600z"/><circle cx="9.200" cy="11.800" r="1.500" fill="#fff"/><circle cx="14.800" cy="11.800" r="1.500" fill="#fff"/></svg>' },
            { name: 'YouTube', svg: '<svg viewBox="0 0 24 24"><rect x="1.500" y="4.500" width="21" height="15" rx="4.500" fill="#FF0000"/><path fill="#fff" d="M10 8.800v6.400l5.500-3.200z"/></svg>' },
            { name: 'Spotify', svg: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10.500" fill="#1DB954"/><g fill="none" stroke="#fff" stroke-linecap="round"><path stroke-width="2" d="M6.500 9.200c3.700-1.100 7.800-.8 11 1"/><path stroke-width="1.700" d="M7.200 12.500c3.100-.9 6.500-.6 9.200 1"/><path stroke-width="1.400" d="M8 15.500c2.500-.7 5.100-.4 7.300.8"/></g></svg>' },
            { name: 'Instagram', svg: '<svg viewBox="0 0 24 24"><defs><linearGradient id="igG" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#FEDA75"/><stop offset=".5" stop-color="#E1306C"/><stop offset="1" stop-color="#833AB4"/></linearGradient></defs><rect x="2.500" y="2.500" width="19" height="19" rx="5.500" fill="none" stroke="url(#igG)" stroke-width="2.200"/><circle cx="12" cy="12" r="4.300" fill="none" stroke="url(#igG)" stroke-width="2.200"/><circle cx="17.300" cy="6.700" r="1.300" fill="#E1306C"/></svg>' },
            { name: 'TikTok', svg: '<svg viewBox="0 0 24 24"><g fill="none" stroke-width="2.600" stroke-linecap="round" stroke-linejoin="round"><path stroke="#25F4EE" d="M13.500 3.500v10.800a3.600 3.600 0 1 1-3.600-3.600"/><path stroke="#FE2C55" d="M14.500 4.500v10.800a3.600 3.600 0 1 1-3.600-3.600"/><path stroke="#111" d="M14 4v10.800a3.600 3.600 0 1 1-3.600-3.600M14 4c.3 2.500 1.900 4.200 4.500 4.500"/></g></svg>' }
        ];

        // ================================================================
        // E. TOMBOL ICON + KOTAK PILIHAN PROFIL
        // ================================================================
        const pick = document.createElement('div');
        pick.id = 'orbitPick';
        document.body.appendChild(pick);
        let hover = false, pickOpen = false;

        function closePick() { pick.style.display = 'none'; pickOpen = false; }
        function openPick(btn, targets, name) {
            pick.innerHTML = '';
            const t = document.createElement('div');
            t.className = 't';
            t.textContent = name + ' — pilih profil';
            pick.appendChild(t);
            targets.forEach(x => {
                const a = document.createElement('a');
                a.href = x.url; a.target = '_blank'; a.rel = 'noopener noreferrer';
                a.textContent = x.label;
                a.addEventListener('click', closePick);
                pick.appendChild(a);
            });
            pick.style.display = 'block';
            const r = btn.getBoundingClientRect(), pw = pick.offsetWidth, ph = pick.offsetHeight;
            let left = Math.max(8, Math.min(window.innerWidth - pw - 8, r.left + r.width / 2 - pw / 2));
            let top  = r.bottom + 8;
            if (top + ph > window.innerHeight - 8) top = Math.max(8, r.top - ph - 8);
            pick.style.left = left + 'px';
            pick.style.top  = top + 'px';
            pickOpen = true;
        }
        document.addEventListener('pointerdown', e => {
            if (pickOpen && !pick.contains(e.target) && !e.target.closest('.orbit-item')) closePick();
        });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePick(); });

        const els = ITEMS.map(it => {
            const key = it.name.toLowerCase();
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'orbit-item';
            el.setAttribute('aria-label', 'Buka ' + it.name);
            el.innerHTML = '<span class="oi">' + it.svg + '</span>';
            el.addEventListener('pointerenter', () => { hover = true; });
            el.addEventListener('pointerleave', () => { hover = false; });
            el.addEventListener('click', () => {
                const targets = targetsFor(key);
                if (targets.length === 1) { closePick(); window.open(targets[0].url, '_blank', 'noopener,noreferrer'); }
                else if (pickOpen) closePick();
                else openPick(el, targets, it.name);
            });
            layer.appendChild(el);
            return el;
        });

        let rx = 0, ry = 0;
        function measure() {
            const w = grid.offsetWidth, h = grid.offsetHeight;
            rx = Math.min(w / 2 + 44, window.innerWidth / 2 - 30);   // selalu muat di layar
            ry = h / 2 + 40;
        }
        measure();
        window.addEventListener('resize', measure);
        if (window.ResizeObserver) new ResizeObserver(measure).observe(grid);

        // ================================================================
        // F. ANIMASI ORBIT (muncul → berputar → kembali → sembunyi)
        // ================================================================
        const easeOut = t => 1 - Math.pow(1 - t, 3);
        const easeIn  = t => t * t * t;
        const CYCLE   = EMERGE + ORBIT_TIME + RETURN + HIDDEN;
        let last = performance.now(), vt = 0;
        const TAU     = Math.PI * 2;

        function frame(now) {
            const dt = now - last; last = now;
            if (!hover && !pickOpen) vt += Math.min(dt, 100) / 1000;   // jeda saat disorot / dipilih
            const t = vt;
            const c = t % CYCLE;

            let r, vis;                                   // r = jarak dari tengah (0..1)
            if (c < EMERGE)                       { r = easeOut(c / EMERGE); vis = r; }
            else if (c < EMERGE + ORBIT_TIME)     { r = 1; vis = 1; }
            else if (c < EMERGE + ORBIT_TIME + RETURN) {
                const k = (c - EMERGE - ORBIT_TIME) / RETURN;
                r = 1 - easeIn(k); vis = r;
            } else                                { r = 0; vis = 0; }

            const spin = (t / LAP) * TAU;
            els.forEach((el, i) => {
                if (vis <= 0.001) { el.style.opacity = 0; el.style.visibility = 'hidden'; return; }
                el.style.visibility = 'visible';
                const a     = spin + (i / els.length) * TAU;
                const depth = (Math.sin(a) + 1) / 2;      // 0 = belakang (atas), 1 = depan (bawah)
                const s     = (0.7 + 0.4 * depth) * (0.4 + 0.6 * r);
                const x     = Math.cos(a) * rx * r;
                const y     = Math.sin(a) * ry * r;
                el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0) scale(${s.toFixed(3)})`;
                el.style.opacity   = ((0.5 + 0.5 * depth) * vis).toFixed(3);
                el.style.zIndex    = depth > 0.5 ? 30 : 5;   // depan: di atas kartu, belakang: di bawah kartu
            });
            requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    })();
    </script>

    <!-- ============================================================
         6. SCRIPT MUSIK — daftar lagu + pemutar YouTube (YouTube baru diunduh saat tombol musik pertama ditekan)
         ============================================================ -->
    <script>
    // ================================================================
    // 🎵 DAFTAR LAGU (isi di sini)
    // Satu lagu = satu baris:  { url: "LINK YOUTUBE", title: "Judul Lagu" },
    // ================================================================
    const playlist = [
        { url: "https://youtu.be/pyGU-UudvrM?si=RaxskFSA869lyHuI", title: "She & Him - I Thought I Saw Your Face Today" },
        { url: "https://youtu.be/cswfR85D7jM?si=FY3psGrc0k1wrqrc", title: "Ravyn Lenae - Love Me Not" },
        { url: "https://youtu.be/0Yi1ttjXQ6c?si=MitJSmjJtY8WkNFH", title: "The Greatest Techno Song That's Ever Lived" },
    ];
    // ================================================================

    // --- Ambil ID video dari berbagai format link YouTube ---
    function getVideoId(url) {
        if (!url) return '';
        const patterns = [
            /youtu\.be\/([A-Za-z0-9_-]{11})/,
            /[?&]v=([A-Za-z0-9_-]{11})/,
            /embed\/([A-Za-z0-9_-]{11})/
        ];
        for (const p of patterns) {
            const m = url.match(p);
            if (m) return m[1];
        }
        return /^[A-Za-z0-9_-]{11}$/.test(url.trim()) ? url.trim() : '';
    }

    // --- Pemutar YouTube ---
    let currentIndex = 0, ytPlayer = null, playerReady = false, isPlaying = false, playerOpen = false;

    function onYouTubeIframeAPIReady() {
        ytPlayer = new YT.Player('ytPlayerWrap', {
            width: '280', height: '158',
            videoId: getVideoId(playlist[0]?.url),
            playerVars: { autoplay: 0, controls: 1, rel: 0, modestbranding: 1 },
            events: {
                onReady: () => { playerReady = true; if (playerOpen) ytPlayer.playVideo(); },
                onStateChange: (e) => {
                    if (e.data === YT.PlayerState.ENDED) nextSong();
                    isPlaying = (e.data === YT.PlayerState.PLAYING);
                    document.getElementById('playPauseBtn').textContent = isPlaying ? '⏸' : '▶';
                }
            }
        });
    }

    // --- Tampilan daftar lagu ---
    function buildPlaylistUI() {
        const ul = document.getElementById('playlistUL');
        ul.innerHTML = '';
        playlist.forEach((song, i) => {
            const li = document.createElement('li');
            li.id = 'song-' + i;
            li.className = 'mp-item';
            const num = document.createElement('span');
            num.className = 'mp-num';
            num.textContent = (i + 1) + '.';
            const name = document.createElement('span');
            name.className = 'mp-name';
            name.textContent = song.title;
            li.append(num, name);
            li.addEventListener('click', () => playSong(i));
            ul.appendChild(li);
        });
        highlightSong(currentIndex);
    }

    function highlightSong(idx) {
        playlist.forEach((_, i) => {
            const li = document.getElementById('song-' + i);
            if (li) li.classList.toggle('active', i === idx);
        });
        document.getElementById('nowPlayingTitle').textContent = playlist[idx]?.title ?? '-';
    }

    function playSong(idx) {
        currentIndex = idx;
        highlightSong(idx);
        if (!playerReady || !ytPlayer) return;
        const vid = getVideoId(playlist[idx]?.url);
        if (vid) ytPlayer.loadVideoById(vid);
    }

    // --- Kontrol putar / jeda / ganti lagu ---
    function togglePlayPause() {
        if (!playerReady || !ytPlayer) return;
        isPlaying ? ytPlayer.pauseVideo() : ytPlayer.playVideo();
    }
    function nextSong() { if (playlist.length) playSong((currentIndex + 1) % playlist.length); }
    function prevSong() { if (playlist.length) playSong((currentIndex - 1 + playlist.length) % playlist.length); }

    // --- Muat YouTube API hanya saat tombol musik pertama kali ditekan (hemat data pengunjung) ---
    let apiRequested = false;
    function loadYouTubeApi() {
        apiRequested = true;
        const tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(tag);
    }

    // --- Buka / tutup kartu pemutar ---
    function toggleMusicPlayer() {
        playerOpen = !playerOpen;
        document.getElementById('playerCard').style.display = playerOpen ? 'block' : 'none';
        document.getElementById('toggleLabel').textContent = playerOpen ? 'Tutup Musik' : 'Putar Musik';
        document.body.classList.toggle('player-open', playerOpen);
        if (playerOpen) {
            if (!apiRequested) loadYouTubeApi();                       // pertama kali: unduh YouTube dulu
            else if (playerReady && !isPlaying) ytPlayer.playVideo();
        }
    }

    buildPlaylistUI();
    </script>

</body>
</html>