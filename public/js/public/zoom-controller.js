/* ===== HYBRID ENGINE WITH STRICT MIN-BOUND LOCK ===== */
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('denahContainer');
    const zoomContent = document.getElementById('zoomContent');
    const zoomIndicator = document.getElementById('zoomIndicator');
    const lorong = document.getElementById('lorongTengah');

    if (!container || !zoomContent) return;

    let currentScale = 1;
    let minScaleLocked = 0.5; // Kunci batas minimal
    const maxScale = 2.5;
    let holdInterval = null;

    let isMouseDown = false;
    let startX, startY, scrollLeft, scrollTop;

    // Hitung Skala Fit Layar secara Presisi
    function calculateFitScale() {
        zoomContent.style.zoom = '1';
        const containerWidth = container.clientWidth - 40; // Kurangi padding 20px kiri-kanan
        const contentWidth = zoomContent.offsetWidth;

        if (contentWidth > 0 && containerWidth > 0) {
            const fitRatio = containerWidth / contentWidth;
            // Batas minimal KETAT: Denah TIDAK BISA mengecil melebihi ukuran pas layar
            return Math.min(Math.max(fitRatio, 0.1), 1.0);
        }
        return 0.5;
    }

    function applyZoom() {
        zoomContent.style.zoom = currentScale;

        if (zoomIndicator) {
            zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
        }
    }

    function centerToLorong() {
        if (container && lorong) {
            const containerWidth = container.clientWidth;
            const lorongLeft = lorong.offsetLeft * currentScale;
            const lorongWidth = lorong.offsetWidth * currentScale;
            
            const targetScroll = lorongLeft - (containerWidth / 2) + (lorongWidth / 2);
            container.scrollLeft = Math.max(0, targetScroll);
        }
    }

    window.adjustZoom = function(amount) {
        let targetScale = currentScale + amount;
        
        // KUNCI MUTLAK: Jika zoom out mencoba melewati minScaleLocked, paksa berhenti di minScaleLocked
        if (targetScale < minScaleLocked) targetScale = minScaleLocked;
        if (targetScale > maxScale) targetScale = maxScale;

        currentScale = targetScale;
        applyZoom();
    };

    window.resetZoom = function() {
        // Hitung ulang skala fit dan kunci minScaleLocked di nilai tersebut
        minScaleLocked = calculateFitScale();
        currentScale = minScaleLocked;
        applyZoom();
        centerToLorong();
    };

    window.startZoomHold = function(amount) {
        window.adjustZoom(amount);
        holdInterval = setInterval(() => {
            window.adjustZoom(amount);
        }, 80);
    };

    window.stopZoomHold = function() {
        if (holdInterval) {
            clearInterval(holdInterval);
            holdInterval = null;
        }
    };

    // 1. MOUSE WHEEL (CTRL + SCROLL)
    container.addEventListener('wheel', function(e) {
        if (e.ctrlKey) {
            e.preventDefault();
            const zoomAmount = e.deltaY < 0 ? 0.08 : -0.08;
            window.adjustZoom(zoomAmount);
        }
    }, { passive: false });

    // 2. DRAG TO SCROLL (DESKTOP)
    container.style.cursor = 'grab';

    container.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return;
        isMouseDown = true;
        container.style.cursor = 'grabbing';
        startX = e.pageX - container.offsetLeft;
        startY = e.pageY - container.offsetTop;
        scrollLeft = container.scrollLeft;
        scrollTop = container.scrollTop;
    });

    window.addEventListener('mouseup', () => {
        isMouseDown = false;
        if (container) container.style.cursor = 'grab';
    });

    container.addEventListener('mousemove', (e) => {
        if (!isMouseDown) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const y = e.pageY - container.offsetTop;
        const walkX = (x - startX) * 1.5;
        const walkY = (y - startY) * 1.5;
        container.scrollLeft = scrollLeft - walkX;
        container.scrollTop = scrollTop - walkY;
    });

    // 3. PINCH & TOUCH DRAG (MOBILE)
    let touchStartDist = 0;

    container.addEventListener('touchstart', (e) => {
        if (e.touches.length === 2) {
            touchStartDist = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
            );
        }
    }, { passive: true });

    container.addEventListener('touchmove', (e) => {
        if (e.touches.length === 2) {
            e.preventDefault();
            const dist = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
            );
            const factor = (dist - touchStartDist) * 0.005;
            window.adjustZoom(factor);
            touchStartDist = dist;
        }
    }, { passive: false });

    // Inisialisasi awal saat load
    setTimeout(() => {
        window.resetZoom();
    }, 150);

    window.addEventListener('resize', () => {
        window.resetZoom();
    });
});