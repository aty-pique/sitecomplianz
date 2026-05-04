/* main.js — scripts globaux du site */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Hauteur du header sticky → --site-sticky-top (.home-cs, ancres #differentiation) ─ */
    const siteHeader = document.querySelector('.site-header');
    if (siteHeader) {
        const setSiteStickyTop = () => {
            const h = siteHeader.getBoundingClientRect().height;
            if (h > 0) {
                document.documentElement.style.setProperty('--site-sticky-top', `${Math.ceil(h)}px`);
            }
        };
        setSiteStickyTop();
        if (typeof ResizeObserver !== 'undefined') {
            new ResizeObserver(setSiteStickyTop).observe(siteHeader);
        } else {
            window.addEventListener('resize', setSiteStickyTop);
        }
        window.addEventListener('load', setSiteStickyTop);
    }

    /* ── Liens actifs nav (exact ou préfixe pour /blog/…) ────────────────────── */
    const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
    document.querySelectorAll('.header-nav a, .megamenu-mobile-navlinks a').forEach(link => {
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
        const mqMgmMobile = window.matchMedia('(max-width: 768px)');
        const isMobileMegamenu = () => mqMgmMobile.matches;

        const burger = document.getElementById('header-burger');
        const mobileCloseBtn = document.getElementById('megamenu-mobile-close');
        const mobileBackBtn = document.getElementById('mgm-mobile-back');

        /** Remet tous les panneaux dans #megamenu-panels (ordre data-panel) — requis desktop & après mobile */
        function restorePanelsToContainer() {
            if (!panels) return;
            const list = Array.from(document.querySelectorAll('.mgm-panel')).sort(
                (a, b) => Number(a.dataset.panel) - Number(b.dataset.panel)
            );
            list.forEach(p => panels.appendChild(p));
        }

        /** Mobile : panneau du pôle directement sous l’onglet cliqué (dernier enfant du li) */
        function placeMobilePanel(index) {
            const tabLi = tabs[index];
            const panel = document.querySelector(`.mgm-panel[data-panel="${index}"]`);
            if (!tabLi || !panel) return;
            tabLi.appendChild(panel);
        }

        function closeMobileDrawer() {
            restorePanelsToContainer();
            document.body.classList.remove('megamenu-mobile-open', 'megamenu-mobile-drilled');
            document.body.style.overflow = '';
            if (burger) {
                burger.setAttribute('aria-expanded', 'false');
            }
            clearTimeout(closeTimer);
            tabs.forEach(t => t.classList.remove('active'));
            allPanels.forEach(p => {
                p.classList.remove('active');
                deactivateCards(p);
            });
            panels.classList.remove('open');
        }

        function collapseMobilePolePanel() {
            restorePanelsToContainer();
            document.body.classList.remove('megamenu-mobile-drilled');
            tabs.forEach(t => t.classList.remove('active'));
            allPanels.forEach(p => {
                p.classList.remove('active');
                deactivateCards(p);
            });
            panels.classList.remove('open');
        }

        function openTab(index) {
            clearTimeout(closeTimer);
            restorePanelsToContainer();
            tabs.forEach(t => t.classList.remove('active'));
            allPanels.forEach(p => p.classList.remove('active'));
            tabs[index]?.classList.add('active');
            const panel = document.querySelector(`.mgm-panel[data-panel="${index}"]`);
            if (panel) {
                panel.classList.add('active');
                deactivateCards(panel);
            }
            panels.classList.add('open');
            if (isMobileMegamenu()) {
                document.body.classList.add('megamenu-mobile-drilled');
                placeMobilePanel(index);
                if (panel) {
                    panel.querySelectorAll('.mgm-sub .mgm-sub-title').forEach(t => {
                        t.setAttribute('role', 'button');
                    });
                }
            }
        }

        function deactivateCards(panel) {
            panel.querySelectorAll('.mgm-card').forEach(c => c.classList.remove('active'));
            panel.querySelectorAll('.mgm-sub').forEach(s => {
                s.classList.remove('active', 'mgm-sub--expanded');
                const tt = s.querySelector('.mgm-sub-title');
                if (tt) tt.setAttribute('aria-expanded', 'false');
            });
            const packs = panel.querySelector('.mgm-packs-default');
            if (packs) packs.classList.remove('hidden');
            const right = panel.querySelector('.mgm-panel-right');
            if (right) right.classList.remove('mgm-panel-right--detail');
        }

        function closeMegamenu() {
            if (isMobileMegamenu()) return;
            closeTimer = setTimeout(() => {
                tabs.forEach(t => t.classList.remove('active'));
                allPanels.forEach(p => p.classList.remove('active'));
                panels.classList.remove('open');
            }, 120);
        }

        function activateCard(card) {
            if (isMobileMegamenu()) return;
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

        tabs.forEach((tab, i) => {
            tab.addEventListener('mouseenter', () => {
                if (!isMobileMegamenu()) openTab(i);
            });
            const link = tab.querySelector('.mgm-tab-link');
            if (link) {
                link.addEventListener('click', (e) => {
                    if (!isMobileMegamenu()) return;
                    e.preventDefault();
                    e.stopPropagation();
                    if (!document.body.classList.contains('megamenu-mobile-open')) {
                        document.body.classList.add('megamenu-mobile-open');
                        document.body.style.overflow = 'hidden';
                        if (burger) burger.setAttribute('aria-expanded', 'true');
                    }
                    openTab(i);
                });
            }
        });

        panels.addEventListener('mouseenter', () => clearTimeout(closeTimer));
        bar.addEventListener('mouseleave', () => {
            if (!isMobileMegamenu()) closeMegamenu();
        });
        allCards.forEach(card => card.addEventListener('mouseenter', () => activateCard(card)));

        /*
         * Réafficher les packs seulement quand la souris quitte tout le bloc deux colonnes
         * (pas quand on passe de la grille des cartes vers la colonne droite niveau 3).
         */
        document.querySelectorAll('.mgm-panel-layout').forEach(layout => {
            layout.addEventListener('mouseleave', (e) => {
                if (isMobileMegamenu()) return;
                const next = e.relatedTarget;
                if (next instanceof Node && layout.contains(next)) return;
                const panel = layout.closest('.mgm-panel');
                if (panel) deactivateCards(panel);
            });
        });

        if (burger) {
            burger.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!isMobileMegamenu()) return;
                if (document.body.classList.contains('megamenu-mobile-open')) {
                    closeMobileDrawer();
                } else {
                    document.body.classList.add('megamenu-mobile-open');
                    document.body.style.overflow = 'hidden';
                    burger.setAttribute('aria-expanded', 'true');
                    collapseMobilePolePanel();
                }
            });
        }

        if (mobileCloseBtn) {
            mobileCloseBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                closeMobileDrawer();
            });
        }

        if (mobileBackBtn) {
            mobileBackBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!isMobileMegamenu()) return;
                collapseMobilePolePanel();
            });
        }

        /** Mobile : sous-sections en accordéon (un bloc ouvert à la fois) */
        bar.addEventListener('click', e => {
            if (!isMobileMegamenu() || !document.body.classList.contains('megamenu-mobile-open')) return;
            const title = e.target.closest('.mgm-sub-title');
            if (!title || !bar.contains(title)) return;
            const sub = title.closest('.mgm-sub');
            if (!sub || !sub.closest('.mgm-panel.active')) return;
            e.preventDefault();
            e.stopPropagation();
            const wasOpen = sub.classList.contains('mgm-sub--expanded');
            const right = sub.closest('.mgm-panel-right');
            if (right) {
                right.querySelectorAll('.mgm-sub').forEach(s => {
                    s.classList.remove('mgm-sub--expanded');
                    const tt = s.querySelector('.mgm-sub-title');
                    if (tt) tt.setAttribute('aria-expanded', 'false');
                });
            }
            if (!wasOpen) {
                sub.classList.add('mgm-sub--expanded');
                title.setAttribute('aria-expanded', 'true');
            }
        });

        const onMqMgmMobileChange = () => {
            if (!isMobileMegamenu()) {
                restorePanelsToContainer();
                closeMobileDrawer();
            }
        };
        if (typeof mqMgmMobile.addEventListener === 'function') {
            mqMgmMobile.addEventListener('change', onMqMgmMobileChange);
        } else if (typeof mqMgmMobile.addListener === 'function') {
            mqMgmMobile.addListener(onMqMgmMobileChange);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.body.classList.contains('megamenu-mobile-open')) {
                closeMobileDrawer();
            }
        });

        document.addEventListener('click', e => {
            if (isMobileMegamenu() && document.body.classList.contains('megamenu-mobile-open')) {
                if (bar.contains(e.target) || (burger && burger.contains(e.target))) {
                    return;
                }
                closeMobileDrawer();
                return;
            }
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
                const firstCard = panel.querySelector('.mgm-card');
                if (firstCard) {
                    if (isMobileMegamenu()) {
                        deactivateCards(panel);
                    } else {
                        activateCard(firstCard);
                    }
                }
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

    /* ── Hub pôle : constat (Aujourd’hui / Demain) — apparition au scroll en cascade + survol (CSS) ─ */
    (function initPoleIntroSplitReveal() {
        const wrap = document.querySelector('.pole-lp-band--split .pole-lp-intro-split');
        if (!wrap) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (typeof IntersectionObserver === 'undefined') return;

        const finishReveal = () => {
            wrap.classList.remove('pole-lp-intro-split--reveal-prep');
            wrap.classList.add('pole-lp-intro-split--revealed');
        };

        const vh = window.innerHeight || document.documentElement.clientHeight;
        const rect = wrap.getBoundingClientRect();
        const visibleNow = rect.top < vh * 0.88 && rect.bottom > vh * 0.1;

        if (visibleNow) return;

        wrap.classList.add('pole-lp-intro-split--reveal-prep');

        const io = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    finishReveal();
                    observer.disconnect();
                });
            },
            { threshold: 0.08, rootMargin: '0px 0px -6% 0px' }
        );

        io.observe(wrap);
    })();

    /* ── Hub pôle : carrousel « Domaines d’expertise » (défilement auto + boucle, barre de scroll masquée en CSS) ─ */
    (function initPoleDomainsCarousel() {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        document.querySelectorAll('[data-pole-domains-carousel]').forEach((root) => {
            const track = root.querySelector('[data-pole-domains-track]');
            const prevBtn = root.querySelector('[data-pole-domains-prev]');
            const nextBtn = root.querySelector('[data-pole-domains-next]');
            if (!track || !prevBtn || !nextBtn) return;

            const getStep = () => {
                const slide = track.querySelector(':scope > *');
                if (!slide) return 300;
                const cs = window.getComputedStyle(track);
                const gap = parseFloat(cs.columnGap || cs.gap) || 16;
                return slide.getBoundingClientRect().width + gap;
            };

            const maxScroll = () => track.scrollWidth - track.clientWidth;

            const scrollBehavior = () => (reduceMotion ? 'auto' : 'smooth');

            const goNext = () => {
                const m = maxScroll();
                if (m <= 4) return;
                if (track.scrollLeft >= m - 4) {
                    track.scrollTo({ left: 0, behavior: scrollBehavior() });
                } else {
                    track.scrollBy({ left: getStep(), behavior: scrollBehavior() });
                }
            };

            const goPrev = () => {
                const m = maxScroll();
                if (m <= 4) return;
                if (track.scrollLeft <= 4) {
                    track.scrollTo({ left: m, behavior: scrollBehavior() });
                } else {
                    track.scrollBy({ left: -getStep(), behavior: scrollBehavior() });
                }
            };

            const syncDisabled = () => {
                const m = maxScroll();
                const lock = m <= 4;
                prevBtn.disabled = lock;
                nextBtn.disabled = lock;
            };

            let autoTimer = null;
            const clearAuto = () => {
                if (autoTimer !== null) {
                    window.clearInterval(autoTimer);
                    autoTimer = null;
                }
            };

            const startAuto = () => {
                clearAuto();
                if (reduceMotion || maxScroll() <= 4) return;
                autoTimer = window.setInterval(goNext, 6000);
            };

            const bumpAuto = () => {
                clearAuto();
                startAuto();
            };

            prevBtn.addEventListener('click', () => {
                goPrev();
                bumpAuto();
            });
            nextBtn.addEventListener('click', () => {
                goNext();
                bumpAuto();
            });

            track.addEventListener('scroll', syncDisabled, { passive: true });
            window.addEventListener(
                'resize',
                () => {
                    syncDisabled();
                    bumpAuto();
                },
                { passive: true }
            );

            track.addEventListener('mouseenter', clearAuto);
            track.addEventListener('mouseleave', startAuto);

            track.addEventListener(
                'pointerdown',
                (e) => {
                    if (track.contains(e.target)) clearAuto();
                },
                { passive: true }
            );
            track.addEventListener(
                'pointerup',
                () => {
                    window.setTimeout(startAuto, 3200);
                },
                { passive: true }
            );

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) clearAuto();
                else startAuto();
            });

            syncDisabled();
            startAuto();
        });
    })();

});
