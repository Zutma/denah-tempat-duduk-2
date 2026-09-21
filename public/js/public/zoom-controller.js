/* ===== HIGH-PERFORMANCE GPU ANIMATED ZOOM ENGINE ===== */
document.addEventListener("DOMContentLoaded", function() {
    const container = document.getElementById('denahContainer');
    const zoomContent = document.getElementById('zoomContent');
    const zoomIndicator = document.getElementById('zoomIndicator');
    const lorong = document.getElementById('lorongTengah');

    if (!container || !zoomContent) return;

    let targetScale = 1;
    let currentScale = 1;
    let baseScale = 1; // Skala CSS zoom dasar saat ini
    let minScaleLocked = 0.5;
    const maxScale = 2.5;
    let holdInterval = null;
    let isAnimating = false;

    let isMouseDown = false;
    let startX, startY, scrollLeft, scrollTop;

    // Hitung Skala Fit Layar
    function calculateFitScale() {
        zoomContent.style.zoom = '1';
        zoomContent.style.transform = 'none';
        const containerWidth = container.clientWidth - 40;
        const contentWidth = zoomContent.offsetWidth;

        if (contentWidth > 0 && containerWidth > 0) {
            const fitRatio = containerWidth / contentWidth;
            return Math.min(Math.max(fitRatio, 0.1), 1.0);
        }
        return 0.5;
    }

    // Loop Animasi GPU (Menggunakan CSS Transform untuk 60 FPS)
    function animateGPU() {
        currentScale += (targetScale - currentScale) * 0.25; // Lerp halus

        if (Math.abs(targetScale - currentScale) > 0.005) {
            // Gunakan transform: scale() selama animasi agar diproses GPU (Super Mulus)
            const relativeScale = currentScale / baseScale;
            zoomContent.style.transform = `scale(${relativeScale})`;
            zoomContent.style.transformOrigin = 'top center';

            if (zoomIndicator) {
                zoomIndicator.innerText = Math.round(currentScale * 100) + '%';
            }
            requestAnimationFrame(animateGPU);
        } else {
            // Animasi Selesai: Kunci nilai akhir ke CSS zoom agar scrollbar pas
            currentScale = targetScale;
            baseScale = currentScale;
            
            zoomContent.style.transform = 'none'; // Clear transform
            zoomContent.style.zoom = currentScale; // Apply zoom fisik sekali saja

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
            const containerWidth = container.clientWidth;
            const lorongLeft = lorong.offsetLeft * targetScale;
            const lorongWidth = lorong.offsetWidth * targetScale;
            
            const targetScroll = lorongLeft - (containerWidth / 2) + (lorongWidth / 2);
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

    // Initial Load
    setTimeout(() => {
        window.resetZoom();
    }, 150);

    window.addEventListener('resize', () => {
        window.resetZoom();
    });
});