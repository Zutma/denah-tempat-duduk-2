/* ===== HIGH-PERFORMANCE GPU ANIMATED ZOOM ENGINE (ZOOM TO CENTER) ===== */
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('denahContainer');
    const zoomContent = document.getElementById('zoomContent');
    const zoomIndicator = document.getElementById('zoomIndicator');
    const lorong = document.getElementById('lorongTengah');

    if (!container || !zoomContent) return;

    let targetScale = 1;
    let currentScale = 1;
    let baseScale = 1;
    let minScaleLocked = 0.5;
    const maxScale = 2.5;
    let holdInterval = null;
    let isAnimating = false;

    let isMouseDown = false;
    let startX, startY, scrollLeft, scrollTop;

    // Hitung Skala Fit Layar (Memperhitungkan Lebar DAN Tinggi)
    function calculateFitScale() {
        zoomContent.style.zoom = '1';
        zoomContent.style.transform = 'none';

        const containerWidth = container.clientWidth - 40;
        const containerHeight = container.clientHeight - 40;

        const contentWidth = zoomContent.offsetWidth;
        const contentHeight = zoomContent.offsetHeight;

        // PENGAMAN: Jika DOM belum selesai render atau ukuran 0, pakai skala default aman
        if (!contentWidth || !contentHeight || containerWidth <= 0 || containerHeight <= 0) {
            return 0.4; 
        }

        const fitWidthRatio = containerWidth / contentWidth;
        const fitHeightRatio = containerHeight / contentHeight;

        const bestFitRatio = Math.min(fitWidthRatio, fitHeightRatio);
        return Math.min(Math.max(bestFitRatio, 0.15), 1.0);
    }

    // Fungsi Utama: Menjaga Titik Fokus Tampilan Saat Zoom Berubah
    function updateScrollToCenter(oldScale, newScale) {
        if (!container || oldScale === newScale) return;

        const centerX = container.scrollLeft + (container.clientWidth / 2);
        const centerY = container.scrollTop + (container.clientHeight / 2);

        const ratioX = centerX / oldScale;
        const ratioY = centerY / newScale;

        const newCenterX = ratioX * newScale;
        const newCenterY = ratioY * newScale;

        container.scrollLeft = newCenterX - (container.clientWidth / 2);
        container.scrollTop = newCenterY - (container.clientHeight / 2);
    }

    // Loop Animasi GPU
    function animateGPU() {
        const oldScale = currentScale;
        currentScale += (targetScale - currentScale) * 0.25;

        if (Math.abs(targetScale - currentScale) > 0.005) {
            const relativeScale = currentScale / baseScale;
            zoomContent.style.transform = `scale(${relativeScale})`;
            zoomContent.style.transformOrigin = 'top center';

            updateScrollToCenter(oldScale, currentScale);

            if (zoomIndicator) {
                zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
            }
            requestAnimationFrame(animateGPU);
        } else {
            const finalOldScale = currentScale;
            currentScale = targetScale;
            baseScale = currentScale;
            
            zoomContent.style.transform = 'none';
            zoomContent.style.zoom = currentScale;

            updateScrollToCenter(finalOldScale, currentScale);

            if (zoomIndicator) {
                zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
            }
            isAnimating = false;
        }
    }

    function triggerSmoothZoom() {
        if (!isAnimating) {
            isAnimating = true;
            requestAnimationFrame(animateGPU);
        }
    }

    function centerToLorong() {
        if (container && lorong) {
            // Gunakan skala target yang sedang diproses
            const activeScale = targetScale || currentScale || 1;
            const containerWidth = container.clientWidth;
            const lorongLeft = lorong.offsetLeft * activeScale;
            const lorongWidth = lorong.offsetWidth * activeScale;
            
            const targetScroll = lorongLeft - (containerWidth / 2) + (lorongWidth / 2);
            
            // Terapkan scroll langsung (instant) tanpa jeda animasi browser
            container.scrollLeft = Math.max(0, targetScroll);
        }
    }

    window.adjustZoom = function(amount) {
        targetScale += amount;
        
        if (targetScale < minScaleLocked) targetScale = minScaleLocked;
        if (targetScale > maxScale) targetScale = maxScale;

        triggerSmoothZoom();
    };

    window.resetZoom = function() {
        minScaleLocked = calculateFitScale();
        targetScale = minScaleLocked;
        triggerSmoothZoom();
        centerToLorong();
    };

    window.startZoomHold = function(amount) {
        window.adjustZoom(amount);
        holdInterval = setInterval(() => {
            window.adjustZoom(amount);
        }, 60);
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
            const zoomAmount = e.deltaY < 0 ? 0.12 : -0.12;
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

    // ===== TOUCH PINCH & DRAG MULTI-TOUCH FIX =====
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
            const factor = (dist - touchStartDist) * 0.003; // Sensitivitas pinch
            window.adjustZoom(factor);
            touchStartDist = dist;
        }
    }, { passive: false });

    // ===== INIT & RESIZE OBSERVER (TANPA RESET OTOMATIS SAAT ZOOM) =====
    let lastContainerWidth = 0;
    let lastContainerHeight = 0;

    function safeInitZoom() {
        if (!container) return;
        const currentW = container.clientWidth;
        const currentH = container.clientHeight;

        if (currentW > 0 && currentH > 0 && (Math.abs(currentW - lastContainerWidth) > 10 || Math.abs(currentH - lastContainerHeight) > 10)) {
            lastContainerWidth = currentW;
            lastContainerHeight = currentH;
            
            // 1. Hitung skala fit
            minScaleLocked = calculateFitScale();
            targetScale = minScaleLocked;
            currentScale = minScaleLocked;
            baseScale = minScaleLocked;

            // 2. Terpakan zoom fisik instan tanpa animasi GPU di awal
            zoomContent.style.transform = 'none';
            zoomContent.style.zoom = currentScale;
            if (zoomIndicator) zoomIndicator.innerText = Math.round(currentScale * 100) + '%';

            // 3. Kunci posisi lorong ke tengah SECARA INSTAN
            centerToLorong();
        }
    }

    // Hanya dengarkan event Resize resmi jendela browser, bukan per-frame observer
    let resizeDebounce = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeDebounce);
        resizeDebounce = setTimeout(safeInitZoom, 200);
    });

    // Inisialisasi Pertama Kali Saja
    window.addEventListener('load', () => {
        setTimeout(() => {
            if (container) {
                lastContainerWidth = container.clientWidth;
                lastContainerHeight = container.clientHeight;
                window.resetZoom();
            }
        }, 100);
    });

    setTimeout(() => {
        if (lastContainerWidth === 0 && container && container.clientWidth > 0) {
            lastContainerWidth = container.clientWidth;
            lastContainerHeight = container.clientHeight;
            window.resetZoom();
        }
    }, 350);
});