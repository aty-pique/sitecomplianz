/**
 * Bouton retour en haut (bas droite). Le rail Hub / devis repose sur quote-modal.js pour la modale.
 */
(function () {
    const btn = document.getElementById('qa-back-top');
    if (!btn) return;

    function sync() {
        const show = window.scrollY > 320;
        btn.hidden = !show;
        btn.setAttribute('aria-hidden', show ? 'false' : 'true');
    }

    window.addEventListener(
        'scroll',
        function () {
            sync();
        },
        { passive: true }
    );
    sync();

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
