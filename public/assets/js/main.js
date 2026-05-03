/* main.js — scripts globaux du site */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Liens actifs nav (exact ou préfixe pour /blog/…) ────────────────────── */
    const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
    document.querySelectorAll('.header-nav a').forEach(link => {
        const raw = link.getAttribute('href');
        if (!raw || raw === '#') return;
        const href = raw.replace(/\/$/, '') || '/';
        let active = false;
        if (href === '/') {
            active = currentPath === '/' || currentPath === '';
        } else if (currentPath === href) {
            active = true;
        } else if (currentPath.startsWith(href + '/')) {
            active = true;
        }
        if (active) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });

    /* ── Méga Menu ───────────────────────────── */
    const bar       = document.getElementById('megamenu-bar');
    const panels    = document.getElementById('megamenu-panels');
    const tabs      = document.querySelectorAll('.mgm-tab');
    const allPanels = document.querySelectorAll('.mgm-panel');
    const allCards  = document.querySelectorAll('.mgm-card');

    if (bar && panels) {
        let closeTimer = null;

        function openTab(index) {
            clearTimeout(closeTimer);
            tabs.forEach(t => t.classList.remove('active'));
            allPanels.forEach(p => p.classList.remove('active'));
            tabs[index]?.classList.add('active');
            const panel = document.querySelector(`.mgm-panel[data-panel="${index}"]`);
            if (panel) {
                panel.classList.add('active');
                /* Afficher les packs par défaut (aucune carte activée) */
                deactivateCards(panel);
            }
            panels.classList.add('open');
        }

        function deactivateCards(panel) {
            panel.querySelectorAll('.mgm-card').forEach(c => c.classList.remove('active'));
            panel.querySelectorAll('.mgm-sub').forEach(s => s.classList.remove('active'));
            const packs = panel.querySelector('.mgm-packs-default');
            if (packs) packs.classList.remove('hidden');
            const right = panel.querySelector('.mgm-panel-right');
            if (right) right.classList.remove('mgm-panel-right--detail');
        }

        function closeMegamenu() {
            closeTimer = setTimeout(() => {
                tabs.forEach(t => t.classList.remove('active'));
                allPanels.forEach(p => p.classList.remove('active'));
                panels.classList.remove('open');
            }, 120);
        }

        function activateCard(card) {
            const panel = card.closest('.mgm-panel');
            if (!panel) return;
            panel.querySelectorAll('.mgm-card').forEach(c => c.classList.remove('active'));
            panel.querySelectorAll('.mgm-sub').forEach(s => s.classList.remove('active'));
            card.classList.add('active');
            const sub = panel.querySelector(`.mgm-sub[data-sub="${card.dataset.card}"]`);
            const right = panel.querySelector('.mgm-panel-right');
            if (sub) {
                sub.classList.add('active');
                if (right) right.classList.add('mgm-panel-right--detail');
            } else if (right) {
                right.classList.remove('mgm-panel-right--detail');
            }
            const packs = panel.querySelector('.mgm-packs-default');
            if (packs) {
                /* Masquer les packs seulement s’il existe des pages niveau 3 pour cette carte */
                if (sub) packs.classList.add('hidden');
                else packs.classList.remove('hidden');
            }
        }

        tabs.forEach((tab, i) => tab.addEventListener('mouseenter', () => openTab(i)));
        panels.addEventListener('mouseenter', () => clearTimeout(closeTimer));
        bar.addEventListener('mouseleave', closeMegamenu);
        allCards.forEach(card => card.addEventListener('mouseenter', () => activateCard(card)));

        /*
         * Réafficher les packs seulement quand la souris quitte tout le bloc deux colonnes
         * (pas quand on passe de la grille des cartes vers la colonne droite niveau 3).
         */
        document.querySelectorAll('.mgm-panel-layout').forEach(layout => {
            layout.addEventListener('mouseleave', (e) => {
                const next = e.relatedTarget;
                if (next instanceof Node && layout.contains(next)) return;
                const panel = layout.closest('.mgm-panel');
                if (panel) deactivateCards(panel);
            });
        });

        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => {
                if (tab.classList.contains('active') && panels.classList.contains('open')) {
                    clearTimeout(closeTimer);
                    tabs.forEach(t => t.classList.remove('active'));
                    allPanels.forEach(p => p.classList.remove('active'));
                    panels.classList.remove('open');
                } else {
                    openTab(i);
                }
            });
        });

        document.addEventListener('click', e => {
            if (!bar.contains(e.target)) {
                tabs.forEach(t => t.classList.remove('active'));
                allPanels.forEach(p => p.classList.remove('active'));
                panels.classList.remove('open');
            }
        });

        /* ── Recherche dans les panneaux ─────────── */

        /**
         * Construit l'index de recherche d'un panneau.
         * Retourne un tableau d'objets { label, href, category }.
         * - category = texte de la carte pilier parente
         * - Pour les cartes elles-mêmes, category = nom du pôle
         */
        function buildIndex(panel) {
            const items = [];

            panel.querySelectorAll('.mgm-card').forEach(card => {
                const cardLabel = card.querySelector('.mgm-card-text strong')?.textContent.trim() ?? '';
                const cardDesc  = card.querySelector('.mgm-card-text span')?.textContent.trim() ?? '';
                const cardHref  = card.getAttribute('href') ?? '#';

                // La carte elle-même
                items.push({ label: cardLabel, desc: cardDesc, href: cardHref, category: cardLabel });

                // Les sous-liens rattachés à cette carte
                const subKey = card.dataset.card;
                const sub = panel.querySelector(`.mgm-sub[data-sub="${subKey}"]`);
                if (sub) {
                    sub.querySelectorAll('li a').forEach(link => {
                        items.push({
                            label:    link.textContent.trim(),
                            desc:     '',
                            href:     link.getAttribute('href') ?? '#',
                            category: cardLabel,
                        });
                    });
                }
            });

            return items;
        }

        /**
         * Échappe les caractères spéciaux pour RegExp.
         */
        function escapeRE(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        /**
         * Met en emphase les occurrences du terme dans un texte.
         */
        function highlight(text, term) {
            if (!term) return escapeHTML(text);
            const re = new RegExp(`(${escapeRE(term)})`, 'gi');
            return escapeHTML(text).replace(re, '<mark>$1</mark>');
        }

        function escapeHTML(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        /**
         * Filtre l'index et affiche les résultats, ou rétablit la vue normale.
         */
        function applySearch(panel, query, index) {
            const cardsGrid  = panel.querySelector('.mgm-cards-grid');
            const panelRight = panel.querySelector('.mgm-panel-right');
            const resultsBox = panel.querySelector('.mgm-search-results');

            const q = query.trim().toLowerCase();

            if (!q) {
                // Vue normale
                if (cardsGrid)  cardsGrid.removeAttribute('hidden');
                if (panelRight) panelRight.removeAttribute('hidden');
                resultsBox.setAttribute('hidden', '');
                resultsBox.innerHTML = '';
                // Réactiver la première carte
                const firstCard = panel.querySelector('.mgm-card');
                if (firstCard) activateCard(firstCard);
                return;
            }

            // Mode recherche : masquer la vue normale
            if (cardsGrid)  cardsGrid.setAttribute('hidden', '');
            if (panelRight) panelRight.setAttribute('hidden', '');
            resultsBox.removeAttribute('hidden');

            const matches = index.filter(item =>
                item.label.toLowerCase().includes(q) ||
                item.desc.toLowerCase().includes(q)
            );

            if (matches.length === 0) {
                resultsBox.innerHTML = `
                    <p class="mgm-sr-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Aucun service trouvé pour « ${escapeHTML(q)} »
                    </p>`;
                return;
            }

            const ul = document.createElement('ul');
            ul.className = 'mgm-sr-list';

            matches.forEach(item => {
                const li = document.createElement('li');
                li.className = 'mgm-sr-item';
                li.innerHTML = `
                    <a href="${escapeHTML(item.href)}">
                        <span class="mgm-sr-cat">${escapeHTML(item.category)}</span>
                        <span class="mgm-sr-label">${highlight(item.label, q)}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="14" height="14" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>`;
                ul.appendChild(li);
            });

            resultsBox.innerHTML = '';
            resultsBox.appendChild(ul);
        }

        /* Initialiser la recherche sur chaque panneau */
        allPanels.forEach(panel => {
            const input = panel.querySelector('.mgm-search');
            if (!input) return;

            const index = buildIndex(panel);

            input.addEventListener('input', () => applySearch(panel, input.value, index));

            /* Empêcher la fermeture du menu quand on clique dans le champ */
            input.addEventListener('click', e => e.stopPropagation());

            /* Réinitialiser quand le panneau se ferme */
            const observer = new MutationObserver(() => {
                if (!panel.classList.contains('active') && input.value) {
                    input.value = '';
                    applySearch(panel, '', index);
                }
            });
            observer.observe(panel, { attributes: true, attributeFilter: ['class'] });
        });
    }

    /* ── Recherche globale dans le header ───── */
    const searchForm   = document.querySelector('.header-search__form');
    const searchToggle = document.querySelector('.header-search__toggle');
    const searchInput  = document.getElementById('header-search-input');

    if (searchForm && searchToggle && searchInput) {
        /* Ouverture au clic sur l'icône loupe */
        searchToggle.addEventListener('click', () => {
            const isOpen = searchForm.classList.toggle('is-open');
            searchToggle.setAttribute('aria-expanded', String(isOpen));
            if (isOpen) {
                searchInput.focus();
            } else {
                searchInput.value = '';
            }
        });

        /* Fermeture si clic en dehors */
        document.addEventListener('click', (e) => {
            if (!searchForm.contains(e.target)) {
                searchForm.classList.remove('is-open');
                searchToggle.setAttribute('aria-expanded', 'false');
                searchInput.value = '';
            }
        });

        /* Fermeture avec Échap */
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchForm.classList.remove('is-open');
                searchToggle.setAttribute('aria-expanded', 'false');
                searchInput.value = '';
                searchToggle.focus();
            }
        });
    }

});
