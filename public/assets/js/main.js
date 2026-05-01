/* main.js — scripts globaux du site */

document.addEventListener('DOMContentLoaded', () => {
    // Marquer le lien actif dans la navigation
    const currentPath = window.location.pathname;
    document.querySelectorAll('.main-nav a').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
});
