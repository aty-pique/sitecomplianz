/**
 * Wiki : menu latéral mobile, copie de code, TOC actif, feedback, filtre liste hub.
 */
(function () {
    const shell = document.querySelector('.wiki-doc-shell');
    const menuBtn = document.querySelector('.wiki-doc-bar__menu');

    function setSidebarOpen(open) {
        if (!shell) return;
        shell.classList.toggle('wiki-doc-shell--sidebar-open', open);
        if (menuBtn) menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    document.querySelectorAll('[data-wiki-sidebar-toggle]').forEach(function (el) {
        el.addEventListener('click', function () {
            if (!shell) return;
            setSidebarOpen(!shell.classList.contains('wiki-doc-shell--sidebar-open'));
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && shell && shell.classList.contains('wiki-doc-shell--sidebar-open')) {
            setSidebarOpen(false);
        }
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-wiki-copy]');
        if (!btn) return;
        var block = btn.closest('.wiki-code-block');
        var code = block ? block.querySelector('pre code') : null;
        if (!code) return;
        var text = code.textContent || '';
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                var prev = btn.textContent;
                btn.textContent = 'Copié !';
                setTimeout(function () {
                    btn.textContent = prev;
                }, 1600);
            });
        }
    });

    /* TOC — lien actif au scroll (premier titre intersectant le haut de la zone de lecture) */
    var prose = document.querySelector('.wiki-prose');
    var toc = document.querySelector('[data-wiki-toc]');
    if (prose && toc && 'IntersectionObserver' in window) {
        var headings = prose.querySelectorAll('h2[id], h3[id]');
        var links = toc.querySelectorAll('a[href^="#"]');
        if (headings.length && links.length) {
            var currentId = '';
            var obs = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (en) {
                        if (en.isIntersecting && en.target.id) {
                            currentId = en.target.id;
                        }
                    });
                    if (!currentId) return;
                    links.forEach(function (a) {
                        a.classList.toggle('is-active', a.getAttribute('href') === '#' + currentId);
                    });
                },
                { rootMargin: '-90px 0px -50% 0px', threshold: 0 }
            );
            headings.forEach(function (h) {
                obs.observe(h);
            });
        }
    }

    document.querySelectorAll('[data-wiki-feedback]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var msg = document.getElementById('wiki-feedback-msg');
            if (!msg) return;
            msg.hidden = false;
            msg.textContent =
                chip.getAttribute('data-wiki-feedback') === 'yes'
                    ? 'Merci ! Vos retours nourrissent les prochaines itérations de cette page.'
                    : 'Merci pour l’honnêteté — nous allons clarifier ou compléter ce chapitre.';
        });
    });

    /* Hub — filtre live sur la liste « toutes les pages » */
    var hubInput = document.getElementById('wiki-hub-q');
    var allList = document.getElementById('wiki-hub-all-pages');
    if (hubInput && allList) {
        function filterHub() {
            var q = hubInput.value.trim().toLowerCase();
            allList.querySelectorAll('li[data-wiki-search-text]').forEach(function (li) {
                var hay = (li.getAttribute('data-wiki-search-text') || '').toLowerCase();
                li.style.display = !q || hay.indexOf(q) !== -1 ? '' : 'none';
            });
        }
        hubInput.addEventListener('input', filterHub);
        filterHub();
    }
})();
