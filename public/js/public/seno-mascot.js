/* ===== JS MASKOT SENO TOUR (v2) ===== */
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