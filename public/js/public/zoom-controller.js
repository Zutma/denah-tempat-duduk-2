// pengatur zoom denah (in/out/reset & mouse wheel)
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

    // terapin skala ke elemen wrapper
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

    // posisikan scroll pas di tengah lorong
    function centerToLorong() {
        if (container && lorong && zoomContent) {
            const containerWidth = container.clientWidth;
            const lorongLeft = lorong.offsetLeft * currentScale;
            const lorongWidth = lorong.offsetWidth * currentScale;
            
            const targetScroll = lorongLeft - (containerWidth / 2) + (lorongWidth / 2);
            container.scrollLeft = Math.max(0, targetScroll);
        }
    }

    // atur perbesaran/pengecilan denah
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

    // kembalikan zoom ke 100%
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

    // tahan tombol zoom in/out
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

    // zoom pakai scroll + ctrl
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