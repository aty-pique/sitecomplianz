/**
 * Section « Comment on transforme votre entreprise » : la ligne verte de la timeline
 * se remplit (0 → 100 %) selon la progression du scroll dans la section.
 */
(function () {
    'use strict';

    var section = document.querySelector('[data-home-method]');
    if (!section) return;

    /** Zone dont le scroll pilote la jauge (timeline + cartes, pas tout le bandeau titre). */
    var track = section.querySelector('.home-method__diagram') || section;

    function clamp01(t) {
        return Math.min(1, Math.max(0, t));
    }

    function getScrollProgress() {
        var rect = track.getBoundingClientRect();
        var vh = window.innerHeight || 1;
        var range = track.offsetHeight - vh * 0.55;
        if (range <= 0) return 1;
        var scrolled = -rect.top + vh * 0.12;
        var p = scrolled / range;
        if (typeof p !== 'number' || !isFinite(p)) return 0;
        return clamp01(p);
    }

    function update() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            section.style.setProperty('--method-tl', '1');
            return;
        }
        section.style.setProperty('--method-tl', getScrollProgress().toFixed(4));
    }

    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
})();
