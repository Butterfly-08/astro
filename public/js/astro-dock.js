(function () {
    'use strict';

    const dock = document.getElementById('astro-dock');
    if (!dock) return;

    const items = Array.from(dock.querySelectorAll('.astro-dock-item'));
    const radius = 120;
    const maxScale = 1.42;

    function resetItems() {
        items.forEach((item) => {
            item.style.removeProperty('--dock-scale');
            item.style.removeProperty('--dock-lift');
        });
    }

    dock.addEventListener('pointermove', (event) => {
        if (event.pointerType !== 'mouse') return;

        items.forEach((item) => {
            const bounds = item.getBoundingClientRect();
            const center = bounds.left + bounds.width / 2;
            const distance = Math.abs(event.clientX - center);
            const proximity = Math.max(0, 1 - distance / radius);
            const scale = 1 + (maxScale - 1) * proximity;

            item.style.setProperty('--dock-scale', scale.toFixed(3));
            item.style.setProperty('--dock-lift', `${(-12 * proximity).toFixed(1)}px`);
        });
    });

    dock.addEventListener('pointerleave', resetItems);
    dock.addEventListener('focusout', (event) => {
        if (!dock.contains(event.relatedTarget)) resetItems();
    });
})();
