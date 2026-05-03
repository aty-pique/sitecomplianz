/**
 * Section « Comment on transforme votre entreprise » : la ligne verte de la timeline
 * se remplit (0 → 100 %) selon la progression du scroll dans la section.
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-home-method]');
    if (!root) return;

    function clamp01(t) {
        return Math.min(1, Math.max(0, t));
    }

    function getScrollProgress() {
        var rect = root.getBoundingClientRect();
        var vh = window.innerHeight || 1;
        var total = root.offsetHeight - vh;
        if (total <= 0) return 1;
        var scrolled = -rect.top;
        var p = scrolled / total;
        if (typeof p !== 'number' || !isFinite(p)) return 0;
        return clamp01(p);
    }

    function update() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            root.style.setProperty('--method-tl', '1');
            return;
        }
        root.style.setProperty('--method-tl', getScrollProgress().toFixed(4));
    }

    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
})();
