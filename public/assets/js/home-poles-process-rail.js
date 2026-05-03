/**
 * Section « Un système complet » — panneau sombre : le rail vertical de la timeline
 * se remplit (0 → 100 %) selon la progression du scroll dans la colonne processus.
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-home-poles-process]');
    if (!root) return;

    function clamp01(t) {
        return Math.min(1, Math.max(0, t));
    }

    function getScrollProgress() {
        var rect = root.getBoundingClientRect();
        var vh = window.innerHeight || 1;
        var range = root.offsetHeight - vh * 0.5;
        if (range <= 0) return 1;
        var scrolled = -rect.top + vh * 0.1;
        var p = scrolled / range;
        if (typeof p !== 'number' || !isFinite(p)) return 0;
        return clamp01(p);
    }

    function update() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            root.style.setProperty('--poles-rail-p', '1');
            return;
        }
        root.style.setProperty('--poles-rail-p', getScrollProgress().toFixed(4));
    }

    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
})();
