/**
 * Parcours client — remplissage progressif de la ligne ondulée (stroke-dashoffset).
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-home-journey]');
    if (!root) return;

    var path = root.querySelector('#hj-wave-path-fill');
    if (!path) return;

    function pathLen() {
        try {
            return path.getTotalLength();
        } catch (e) {
            return 1400;
        }
    }

    var L = pathLen();

    function clamp01(t) {
        return Math.min(1, Math.max(0, t));
    }

    function scrollProgress() {
        var rect = root.getBoundingClientRect();
        var vh = window.innerHeight || 1;
        var total = root.offsetHeight - vh;
        if (total <= 0) return 1;
        var p = -rect.top / total;
        if (typeof p !== 'number' || !isFinite(p)) return 0;
        return clamp01(p);
    }

    function apply() {
        L = pathLen();
        path.style.strokeDasharray = String(L);

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            path.style.strokeDashoffset = '0';
            root.style.setProperty('--journey-p', '1');
            return;
        }
        var p = scrollProgress();
        root.style.setProperty('--journey-p', p.toFixed(4));
        path.style.strokeDashoffset = String(L * (1 - p));
    }

    apply();
    window.addEventListener('scroll', apply, { passive: true });
    window.addEventListener('resize', apply);
})();
