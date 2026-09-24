/* ===== SENO MASCOT TOUR ENGINE (HIGH PERFORMANCE OPTIMIZED) ===== */
(function () {
    'use strict';

    var POSES = {
        wisuda:   (window.APP_BASE_URL || '') + '/images/SENO_POSE_1.png',
        wave:     (window.APP_BASE_URL || '') + '/images/SENO_POSE_3.png',
        shy:      (window.APP_BASE_URL || '') + '/images/SENO_POSE_2.png',
        surprise: (window.APP_BASE_URL || '') + '/images/SENO_POSE_4.png',
        normal:   (window.APP_BASE_URL || '') + '/images/SENO_POSE_1.png',
    };

    var steps = [
        { pose: 'wave', text: '🎉 Halo Wisudawan ITS! Selamat atas pencapaianmu yang luar biasa! 🎓', dur: 4000 },
        { pose: 'shy', text: 'Aku <b>SENO</b>, maskot ITS! Yuk kukenalkan cara pakai denah ini!!', dur: 3800 },
        { pose: 'wisuda', text: '🎛️ Gunakan <b>dropdown sesi</b> di atas untuk memilih jadwal wisudamu.', dur: 4000 },
        { pose: 'normal', text: '🔍 <b>Ketik nama/NRP</b> di kotak cari — kursimu akan <span style="color:#127BBE;font-weight:700">berkilau!</span> ✨', dur: 4200 },
        { pose: 'surprise', text: '🔎 Pakai tombol <b>− / +</b> atau <b>Ctrl+Scroll</b> untuk zoom denah.', dur: 3600 },
        { pose: 'normal', text: '🪑 Klik kursi mana saja untuk lihat <b>nama, NRP & prodi</b> wisudawan.', dur: 3800 },
        { pose: 'wave', text: '🎊 Selamat menikmati hari wisudamu! Kalau perlu bantuan, panggil aku lagi ya! 👋', dur: 0, isLast: true },
    ];

    var cur = 0;
    var autoMode = true;
    var autoTimer = null;
    var twRaf = null;
    var ENTRY_ANIMS = ['anim-right', 'anim-rise', 'anim-drop', 'anim-peek'];

    // Cache DOM Elements (Hanya sekali query agar super kencang)
    var dom = {};

    function initDomCache() {
        dom.mascot  = document.getElementById('seno-mascot');
        dom.bubble  = document.getElementById('seno-bubble');
        dom.img     = document.getElementById('seno-img');
        dom.text    = document.getElementById('seno-text');
        dom.nextBtn = document.getElementById('seno-next-btn');
        dom.skipBtn = document.getElementById('seno-skip-btn');
        dom.progWrap= document.getElementById('seno-progress-wrap');
        dom.progBar = document.getElementById('seno-progress-bar');
        dom.replay  = document.getElementById('seno-replay-btn');
    }

    // Preload semua gambar di background
    function preloadPoses() {
        Object.keys(POSES).forEach(function(key) {
            var img = new Image();
            img.src = POSES[key];
        });
    }

    function setPose(pose) {
        if (dom.img) dom.img.src = POSES[pose] || POSES.wisuda;
    }

    function clearAutoTimer() {
        if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
    }

    function clearTwRaf() {
        if (twRaf) { cancelAnimationFrame(twRaf); twRaf = null; }
    }

    // Typewriter via requestAnimationFrame (GPU-friendly, 60fps mulus)
    function typewrite(text, onDone) {
        clearTwRaf();
        if (!dom.text) return;

        var plain = text.replace(/<[^>]+>/g, '');
        var length = plain.length;
        var i = 0;
        var lastTime = performance.now();
        var speed = 20; // ms per karakter

        dom.text.textContent = '';

        function step(now) {
            if (now - lastTime >= speed) {
                i++;
                dom.text.textContent = plain.substring(0, i);
                lastTime = now;
            }

            if (i < length) {
                twRaf = requestAnimationFrame(step);
            } else {
                clearTwRaf();
                dom.text.innerHTML = text;
                if (onDone) onDone();
            }
        }

        twRaf = requestAnimationFrame(step);
    }

    function animateBubble() {
        if (!dom.bubble) return;
        dom.bubble.style.display = 'block';
        dom.bubble.classList.remove('seno-bounce-anim');
        // Trigger reflow ringan tanpa membaca offsetWidth
        requestAnimationFrame(function() {
            dom.bubble.classList.add('seno-bounce-anim');
        });
    }

    function startProgressBar(dur) {
        if (!dom.progWrap || !dom.progBar) return;
        if (dur > 0) {
            dom.progWrap.style.display = 'block';
            dom.progBar.classList.remove('running');
            dom.progBar.style.width = '0%';
            
            requestAnimationFrame(function() {
                dom.progBar.style.setProperty('--seno-dur', (dur / 1000) + 's');
                dom.progBar.classList.add('running');
            });
        } else {
            dom.progWrap.style.display = 'none';
        }
    }

    function stopProgressBar() {
        if (dom.progWrap) dom.progWrap.style.display = 'none';
        if (dom.progBar) {
            dom.progBar.classList.remove('running');
            dom.progBar.style.width = '0%';
        }
    }

    function showStep(idx) {
        var step = steps[idx];
        if (!step) return;

        clearAutoTimer();
        stopProgressBar();

        setPose(step.pose);
        animateBubble();

        if (step.isLast) {
            if (dom.nextBtn) { dom.nextBtn.textContent = 'Siap! 🎓'; dom.nextBtn.style.display = ''; }
            if (dom.skipBtn) dom.skipBtn.style.display = 'none';
            stopProgressBar();
            typewrite(step.text, null);
        } else if (autoMode) {
            if (dom.nextBtn) { dom.nextBtn.textContent = 'Lanjut →'; dom.nextBtn.style.display = ''; }
            if (dom.skipBtn) { dom.skipBtn.textContent = '⏩ Skip'; dom.skipBtn.style.display = ''; }
            typewrite(step.text, function() {
                startProgressBar(step.dur);
                autoTimer = setTimeout(advanceStep, step.dur);
            });
        } else {
            if (dom.nextBtn) { dom.nextBtn.textContent = 'Lanjut →'; dom.nextBtn.style.display = ''; }
            if (dom.skipBtn) { dom.skipBtn.textContent = '⏩ Skip'; dom.skipBtn.style.display = ''; }
            typewrite(step.text, null);
            stopProgressBar();
        }
    }

    function advanceStep() {
        if (twRaf) {
            clearTwRaf();
            var step = steps[cur];
            if (dom.text && step) dom.text.innerHTML = step.text;

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

    function isMobile() {
        return window.innerWidth < 768;
    }

    function exitSeno() {
        clearAutoTimer();
        clearTwRaf();
        stopProgressBar();
        if (!dom.mascot) return;

        setPose('wave');
        if (dom.bubble) dom.bubble.style.display = 'none';

        dom.mascot.classList.remove('anim-float');
        ENTRY_ANIMS.forEach(function(a) { dom.mascot.classList.remove(a); });
        dom.mascot.classList.add('anim-exit');

        setTimeout(function() {
            dom.mascot.classList.add('seno-hidden');
            dom.mascot.classList.remove('seno-visible', 'anim-exit');
            if (dom.replay) dom.replay.classList.add('seno-rb-visible');
        }, 550);
    }

    function startTour(manual) {
        autoMode = !manual;
        cur = 0;
        if (!dom.mascot) return;

        ENTRY_ANIMS.forEach(function(a) { dom.mascot.classList.remove(a); });
        dom.mascot.classList.remove('anim-exit', 'anim-float', 'seno-hidden');
        dom.mascot.style.right = '';
        dom.mascot.style.left  = '';
        dom.mascot.classList.add('seno-visible');

        if (dom.replay) dom.replay.classList.remove('seno-rb-visible');

        var pick = ENTRY_ANIMS[Math.floor(Math.random() * ENTRY_ANIMS.length)];
        dom.mascot.classList.add(pick);

        setTimeout(function() {
            ENTRY_ANIMS.forEach(function(a) { dom.mascot.classList.remove(a); });
            dom.mascot.classList.add('anim-float');
            showStep(0);
        }, 850);
    }

    window.SenoTour = {
        next: function() { advanceStep(); },
        skip: function() { exitSeno(); },
        replay: function() { startTour(true); },
    };

    document.addEventListener('DOMContentLoaded', function() {
        initDomCache();
        preloadPoses();

        if (dom.nextBtn) dom.nextBtn.addEventListener('click', SenoTour.next);
        if (dom.skipBtn) dom.skipBtn.addEventListener('click', SenoTour.skip);
        if (dom.replay) {
            dom.replay.addEventListener('click', function(e) {
                e.stopPropagation();
                if (isMobile()) {
                    // Mobile: Jika mascot disembunyikan / hanya replay, tampilkan mascot + bubble
                    if (dom.mascot && dom.mascot.classList.contains('seno-hidden')) {
                        startTour(true);
                    } else if (dom.bubble) {
                        // Toggle atau buka bubble
                        if (dom.bubble.style.display === 'none' || !dom.bubble.style.display) {
                            showStep(cur);
                        } else {
                            if (dom.bubble) dom.bubble.style.display = 'none';
                        }
                    }
                } else {
                    SenoTour.replay();
                }
            });
        }

        if (dom.img) {
            dom.img.addEventListener('click', function(e) {
                e.stopPropagation();
                if (isMobile()) {
                    // Mobile click on mascot image toggles bubble or advances step
                    if (dom.bubble && (dom.bubble.style.display === 'none' || !dom.bubble.style.display)) {
                        showStep(cur);
                    } else {
                        advanceStep();
                    }
                    return;
                }

                if (twRaf) {
                    clearTwRaf();
                    var step = steps[cur];
                    if (dom.text && step) dom.text.innerHTML = step.text;
                    if (autoMode && step && step.dur > 0 && !step.isLast) {
                        stopProgressBar();
                        startProgressBar(step.dur);
                        autoTimer = setTimeout(advanceStep, step.dur);
                    }
                }
            });
        }

        // INITIAL LOAD LOGIC ACCORDING TO SCREEN SIZE
        setTimeout(function() {
            if (isMobile()) {
                // Di Mobile: Sembunyikan bubble chat & mascot penuh, HANYA tampilkan tombol/ikon panggil SENO (seno-replay-btn)
                if (dom.mascot) {
                    dom.mascot.classList.add('seno-hidden');
                    dom.mascot.classList.remove('seno-visible');
                }
                if (dom.bubble) dom.bubble.style.display = 'none';
                if (dom.replay) dom.replay.classList.add('seno-rb-visible');
            } else {
                // Di Desktop (>= 768px): Tampilkan SENO + Bubble onboarding otomatis, lalu auto collapse / exit setelah 6 detik
                startTour(false);
                clearAutoTimer();
                autoTimer = setTimeout(function() {
                    exitSeno();
                }, 6000);
            }
        }, 1000);
    });
})();