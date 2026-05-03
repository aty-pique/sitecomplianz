/**
 * Modale pré-devis : ouverture (header, CTA, liens /contact hors nav « Contact ») + wizard multi-étapes.
 */
(function () {
    const modal = document.getElementById('quote-modal');
    const form = document.getElementById('quote-wizard');

    function shouldOpenFromContactLink(anchor) {
        if (!anchor || anchor.getAttribute('href') !== '/contact') return false;
        if (anchor.closest('.header-nav')) return false;
        if (anchor.closest('.site-footer')) return false;
        if (anchor.hasAttribute('data-no-quote-modal')) return false;
        return true;
    }

    function openModal() {
        if (!modal) return;
        const rdvModal = document.getElementById('rdv-modal');
        if (rdvModal && !rdvModal.hidden) {
            rdvModal.hidden = true;
            document.body.classList.remove('rdv-modal-open');
        }
        modal.hidden = false;
        document.body.classList.add('quote-modal-open');
        if (typeof resetWizard === 'function') resetWizard();
        const closeBtn = modal.querySelector('.quote-modal__close');
        if (closeBtn) closeBtn.focus();
    }

    function closeModal() {
        if (!modal) return;
        modal.hidden = true;
        document.body.classList.remove('quote-modal-open');
    }

    document.addEventListener('click', function (e) {
        const direct = e.target.closest('[data-open-quote-modal]');
        if (direct) {
            e.preventDefault();
            openModal();
            return;
        }
        const a = e.target.closest('a[href="/contact"]');
        if (a && shouldOpenFromContactLink(a)) {
            e.preventDefault();
            openModal();
        }
    });

    if (modal) {
        modal.querySelectorAll('[data-quote-modal-close]').forEach(function (el) {
            el.addEventListener('click', function () {
                closeModal();
            });
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && !modal.hidden) {
            closeModal();
        }
    });

    /* —— Wizard (identique à l’ancien contact-quote.js) —— */
    var resetWizard;

    if (!form) return;

    const panes = form.querySelectorAll('.quote-wizard__pane');
    const indicators = form.querySelectorAll('[data-step-indicator]');
    const btnPrev = document.getElementById('quote-prev');
    const btnNext = document.getElementById('quote-next');
    const btnSubmit = document.getElementById('quote-submit');
    const btnNoRdv = document.getElementById('quote-submit-no-rdv');
    const deadlineSel = document.getElementById('quote-has-deadline');
    const deadlineWrap = document.getElementById('quote-deadline-detail-wrap');
    const noPolesMsg = document.getElementById('quote-no-poles-msg');

    const qRdvGrid = document.getElementById('quote-rdv-cal-grid');
    const qRdvTitle = document.getElementById('quote-rdv-cal-title');
    const qRdvSlots = document.getElementById('quote-rdv-slots');
    const qRdvSlotsHint = document.getElementById('quote-rdv-slots-hint');
    const qRdvDate = document.getElementById('quote-rdv-date');
    const qRdvSlot = document.getElementById('quote-rdv-slot');
    const qRdvSkip = document.getElementById('quote-rdv-skip');
    const qRdvValMsg = document.getElementById('quote-rdv-validation-msg');

    const RDV_MONTHS = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
    ];

    var qRdvViewYear;
    var qRdvViewMonth;
    var qRdvSelectedIso = '';
    var qRdvSelectedSlot = '';

    const total = 6;
    var step = 1;

    const elEstLoading = document.getElementById('quote-estimate-loading');
    const elEstContent = document.getElementById('quote-estimate-content');
    const elEstError = document.getElementById('quote-estimate-error');

    function formatMoney(n) {
        var x = typeof n === 'number' ? n : parseFloat(n);
        if (isNaN(x)) x = 0;
        return x.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    function clientBenefitsForLine(ln) {
        if (ln && ln.client_benefits && ln.client_benefits.length) return ln.client_benefits;
        return [
            'Un chargé d’affaires valide le périmètre avec vous avant tout engagement.',
            'Une proposition claire, chiffrée et priorisée.',
            'Un seul interlocuteur pour faire le lien avec les équipes techniques.',
        ];
    }

    function clientDetailParamsForLine(ln) {
        if (ln && ln.client_detail_params && ln.client_detail_params.length) return ln.client_detail_params;
        var det = ln.detail_params;
        if (!det || !det.length) return [];
        var out = [];
        for (var j = 0; j < det.length; j++) {
            var p = String(det[j].param || '');
            if (/Hypothèses|grille|scénario|intersection/i.test(p)) continue;
            out.push(det[j]);
        }
        return out;
    }

    function renderEstimate(est) {
        if (!elEstContent) return;
        var lines = (est && est.lines) ? est.lines : [];
        var totalHt = (est && typeof est.total_ht === 'number') ? est.total_ht : 0;
        var notes = (est && est.notes) ? est.notes : [];
        var html = '';
        html += '<div class="quote-estimate__layout">';

        html += '<div class="quote-estimate__hero">';
        html += '<p class="quote-estimate__hero-label">Total indicatif HT</p>';
        html += '<p class="quote-estimate__hero-amount">' + formatMoney(totalHt) + '</p>';
        html += '<p class="quote-estimate__hero-hint">Montant non contractuel — affiné après échange.</p>';
        html += '</div>';

        html += '<ul class="quote-estimate__trust" aria-label="Nos engagements">';
        html += '<li class="quote-estimate__trust-item"><span class="quote-estimate__trust-title">Réactivité</span><span class="quote-estimate__trust-text">Retour sous 48 h ouvrées en moyenne.</span></li>';
        html += '<li class="quote-estimate__trust-item"><span class="quote-estimate__trust-title">Transparence</span><span class="quote-estimate__trust-text">Pas de surprise : le devis suit cette base.</span></li>';
        html += '<li class="quote-estimate__trust-item"><span class="quote-estimate__trust-title">Expertise</span><span class="quote-estimate__trust-text">Un cadrage métier + technique avec nos consultants.</span></li>';
        html += '</ul>';

        if (notes.length) {
            var hasOnlyHints = lines.length > 0;
            for (var ni = 0; ni < notes.length; ni++) {
                var t = String(notes[ni] || '');
                if (/Excel|cellules calculées/i.test(t)) continue;
                if (lines.length === 0 && /Aucune ligne automatique/i.test(t)) continue;
                if (hasOnlyHints && /Aucune ligne automatique/i.test(t)) continue;
                html += '<p class="quote-estimate__note">' + escapeHtml(t) + '</p>';
            }
        }

        if (lines.length) {
            html += '<div class="quote-estimate__cards">';
            for (var i = 0; i < lines.length; i++) {
                var ln = lines[i] || {};
                var amt = typeof ln.amount_eur === 'number' ? ln.amount_eur : parseFloat(ln.amount_eur) || 0;
                var duration = ln.client_duration_hint ? String(ln.client_duration_hint) : '';
                var benefits = clientBenefitsForLine(ln);
                var ctx = clientDetailParamsForLine(ln);

                html += '<article class="quote-estimate-card">';
                html += '<div class="quote-estimate-card__head">';
                html += '<h4 class="quote-estimate-card__title">' + escapeHtml(String(ln.label || 'Ligne')) + '</h4>';
                html += '<p class="quote-estimate-card__price">' + formatMoney(amt) + ' <span>HT</span></p>';
                html += '</div>';
                if (duration) {
                    html += '<div class="quote-estimate-card__time">';
                    html += '<span class="quote-estimate-card__time-label">Délai indicatif</span>';
                    html += '<p class="quote-estimate-card__time-text">' + escapeHtml(duration) + '</p>';
                    html += '</div>';
                }
                html += '<div class="quote-estimate-card__benefits-wrap">';
                html += '<span class="quote-estimate-card__benefits-label">Ce que vous en retirez</span>';
                html += '<ul class="quote-estimate-card__benefits">';
                for (var b = 0; b < benefits.length; b++) {
                    html += '<li>' + escapeHtml(String(benefits[b])) + '</li>';
                }
                html += '</ul></div>';
                if (ctx.length) {
                    html += '<ul class="quote-estimate-card__context">';
                    for (var c = 0; c < ctx.length; c++) {
                        html += '<li><span class="quote-estimate-card__ctx-k">' +
                            escapeHtml(String(ctx[c].param || '')) + '</span> ' +
                            escapeHtml(String(ctx[c].value || '')) + '</li>';
                    }
                    html += '</ul>';
                }
                html += '</article>';
            }
            html += '</div>';
        } else {
            html += '<div class="quote-estimate__empty-wrap">';
            html += '<p class="quote-estimate__empty">Nous n’avons pas pu déduire un montant automatique à partir de cette sélection.</p>';
            html += '<p class="quote-estimate__empty-hint">Pas d’inquiétude : envoyez votre demande — nous chiffrons manuellement et vous recontactons.</p>';
            html += '</div>';
        }

        html += '</div>';
        elEstContent.innerHTML = html;
    }

    function escapeHtml(s) {
        return s.replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch] || ch;
        });
    }

    function loadEstimateIfNeeded() {
        if (!elEstLoading || !elEstContent || !elEstError) return Promise.resolve();
        elEstLoading.hidden = false;
        elEstContent.hidden = true;
        elEstError.hidden = true;
        elEstError.textContent = '';
        return fetch('/contact/quote/estimate', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (data) {
                if (!data || !data.ok) throw new Error('bad');
                renderEstimate(data.estimation);
                elEstLoading.hidden = true;
                elEstContent.hidden = false;
            })
            .catch(function () {
                elEstLoading.hidden = true;
                elEstError.textContent =
                    'Le calcul automatique est momentanément indisponible. Vous pouvez envoyer la demande : nous chiffrerons manuellement à partir de la grille.';
                elEstError.hidden = false;
                elEstContent.innerHTML = '';
                elEstContent.hidden = false;
            });
    }

    function isVisible(el) {
        if (!el) return false;
        if (el.hasAttribute('hidden')) return false;
        var h = el.closest('[hidden]');
        return !h;
    }

    function pad2(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function qRdvToIso(y, m, d) {
        return y + '-' + pad2(m + 1) + '-' + pad2(d);
    }

    function qRdvStartOfToday() {
        var t = new Date();
        t.setHours(0, 0, 0, 0);
        return t;
    }

    function syncQuoteRdvHidden() {
        if (qRdvDate) qRdvDate.value = qRdvSelectedIso || '';
        if (qRdvSlot) qRdvSlot.value = qRdvSelectedSlot || '';
    }

    function renderQuoteRdvCalendar() {
        if (!qRdvGrid || !qRdvTitle) return;
        if (typeof qRdvViewYear !== 'number') {
            var now = new Date();
            qRdvViewYear = now.getFullYear();
            qRdvViewMonth = now.getMonth();
        }
        qRdvTitle.textContent = RDV_MONTHS[qRdvViewMonth] + ' ' + qRdvViewYear;

        var first = new Date(qRdvViewYear, qRdvViewMonth, 1);
        var startWeekday = (first.getDay() + 6) % 7;
        var daysInMonth = new Date(qRdvViewYear, qRdvViewMonth + 1, 0).getDate();
        var today = qRdvStartOfToday();
        var frag = document.createDocumentFragment();
        var prevMonthDays = new Date(qRdvViewYear, qRdvViewMonth, 0).getDate();

        var i;
        for (i = 0; i < startWeekday; i++) {
            var pd = prevMonthDays - startWeekday + i + 1;
            var c0 = document.createElement('button');
            c0.type = 'button';
            c0.className = 'quote-rdv-cal__day quote-rdv-cal__day--muted';
            c0.textContent = String(pd);
            c0.disabled = true;
            frag.appendChild(c0);
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'quote-rdv-cal__day';
            cell.textContent = String(d);
            var cur = new Date(qRdvViewYear, qRdvViewMonth, d);
            cur.setHours(0, 0, 0, 0);
            if (cur < today) {
                cell.classList.add('quote-rdv-cal__day--disabled');
                cell.disabled = true;
            } else {
                var iso = qRdvToIso(qRdvViewYear, qRdvViewMonth, d);
                if (iso === qRdvSelectedIso) cell.classList.add('is-selected');
                cell.addEventListener('click', (function (isoVal, dayNum) {
                    return function () {
                        if (qRdvSkip) qRdvSkip.value = '0';
                        qRdvSelectedIso = isoVal;
                        qRdvSelectedSlot = '';
                        form.querySelectorAll('.quote-rdv-slot-btn').forEach(function (b) {
                            b.classList.remove('is-selected');
                        });
                        if (qRdvSlots) qRdvSlots.hidden = false;
                        if (qRdvSlotsHint) {
                            qRdvSlotsHint.textContent =
                                'Date : ' + dayNum + ' ' + RDV_MONTHS[qRdvViewMonth].toLowerCase() + ' ' + qRdvViewYear + ' — choisissez un créneau.';
                        }
                        if (qRdvValMsg) qRdvValMsg.hidden = true;
                        renderQuoteRdvCalendar();
                        syncQuoteRdvHidden();
                    };
                })(iso, d));
            }
            frag.appendChild(cell);
        }

        var totalCells = startWeekday + daysInMonth;
        var trailing = (7 - (totalCells % 7)) % 7;
        for (i = 1; i <= trailing; i++) {
            var c1 = document.createElement('button');
            c1.type = 'button';
            c1.className = 'quote-rdv-cal__day quote-rdv-cal__day--muted';
            c1.textContent = String(i);
            c1.disabled = true;
            frag.appendChild(c1);
        }

        qRdvGrid.innerHTML = '';
        qRdvGrid.appendChild(frag);
        if (qRdvSlots) {
            qRdvSlots.hidden = !qRdvSelectedIso;
        }
    }

    function resetQuoteRdvUi() {
        qRdvSelectedIso = '';
        qRdvSelectedSlot = '';
        var now = new Date();
        qRdvViewYear = now.getFullYear();
        qRdvViewMonth = now.getMonth();
        if (qRdvSkip) qRdvSkip.value = '0';
        if (qRdvDate) qRdvDate.value = '';
        if (qRdvSlot) qRdvSlot.value = '';
        if (qRdvSlots) qRdvSlots.hidden = true;
        if (qRdvValMsg) qRdvValMsg.hidden = true;
        form.querySelectorAll('.quote-rdv-slot-btn').forEach(function (b) {
            b.classList.remove('is-selected');
        });
        renderQuoteRdvCalendar();
    }

    function validateStep(s) {
        var pane = form.querySelector('.quote-wizard__pane[data-step="' + s + '"]');
        if (!pane) return true;
        var req = pane.querySelectorAll('[required]');
        for (var i = 0; i < req.length; i++) {
            var el = req[i];
            if (!isVisible(el)) continue;
            if (el.type === 'checkbox') {
                if (!el.checked) {
                    el.focus();
                    return false;
                }
            } else if (!String(el.value || '').trim()) {
                el.focus();
                return false;
            }
        }
        if (s === 5 && qRdvSkip && qRdvSkip.value !== '1') {
            if (!qRdvSelectedIso || !qRdvSelectedSlot) {
                if (qRdvValMsg) qRdvValMsg.hidden = false;
                var sec = document.getElementById('quote-rdv-section');
                if (sec) sec.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return false;
            }
        }
        if (s === 5 && qRdvValMsg) qRdvValMsg.hidden = true;
        if (s === 6) {
            var priv = form.querySelector('input[name="quote[privacy_ok]"]');
            if (priv && !priv.checked) {
                priv.focus();
                return false;
            }
        }
        return true;
    }

    function validateAllSteps() {
        for (var s = 1; s <= total; s++) {
            if (!validateStep(s)) {
                showStep(s);
                return false;
            }
        }
        return true;
    }

    function syncPoleSections() {
        var checks = form.querySelectorAll('[data-pole-checkbox]');
        var any = false;
        checks.forEach(function (ch) {
            var id = ch.getAttribute('data-pole-checkbox');
            var sec = form.querySelector('[data-pole-section="' + id + '"]');
            if (sec) {
                var on = ch.checked;
                sec.hidden = !on;
                if (on) any = true;
            }
        });
        if (noPolesMsg) noPolesMsg.hidden = any;
    }

    /** Types de projet p1 cochés → panneaux « Questionnaire besoins » (routage). */
    function selectedP1ProjectTypes() {
        var out = [];
        form.querySelectorAll('input[name="quote[p1][project_types][]"]:checked').forEach(function (cb) {
            out.push(cb.value);
        });
        return out;
    }

    function syncP1Submodules() {
        var sel = selectedP1ProjectTypes();
        var intro = document.getElementById('quote-p1-submodules-intro');
        if (intro) intro.hidden = sel.length === 0;
        form.querySelectorAll('[data-p1-for]').forEach(function (box) {
            var v = box.getAttribute('data-p1-for') || '';
            box.hidden = sel.indexOf(v) === -1;
        });
    }

    function selectedP2Levers() {
        var out = [];
        form.querySelectorAll('input[name="quote[p2][levers][]"]:checked').forEach(function (cb) {
            out.push(cb.value);
        });
        return out;
    }

    function syncP2LeverPanels() {
        var sel = selectedP2Levers();
        var intro = document.getElementById('quote-p2-lever-panels-intro');
        if (intro) intro.hidden = sel.length === 0;
        form.querySelectorAll('[data-p2-for-lever]').forEach(function (box) {
            var v = box.getAttribute('data-p2-for-lever') || '';
            box.hidden = sel.indexOf(v) === -1;
        });
    }

    function showStep(n) {
        step = Math.max(1, Math.min(total, n));
        panes.forEach(function (p) {
            var ps = parseInt(p.getAttribute('data-step'), 10);
            var active = ps === step;
            p.hidden = !active;
            p.classList.toggle('is-active', active);
        });
        indicators.forEach(function (ind) {
            var s = parseInt(ind.getAttribute('data-step-indicator'), 10);
            ind.classList.toggle('is-active', s === step);
        });
        if (btnPrev) btnPrev.disabled = step === 1;
        if (btnNext) btnNext.hidden = step === total;
        if (btnSubmit) btnSubmit.hidden = step !== total;
        if (btnNoRdv) btnNoRdv.hidden = step !== 5;
        if (step === 4) {
            syncPoleSections();
            syncP1Submodules();
            syncP2LeverPanels();
        }
        if (step === 5 && qRdvGrid) {
            renderQuoteRdvCalendar();
            syncQuoteRdvHidden();
        }
        if (deadlineSel && deadlineWrap) {
            deadlineWrap.hidden = deadlineSel.value !== 'oui';
        }
    }

    resetWizard = function () {
        form.reset();
        resetQuoteRdvUi();
        showStep(1);
        syncPoleSections();
        syncP1Submodules();
        syncP2LeverPanels();
        form.querySelectorAll('.quote-pole-card').forEach(function (label) {
            var input = label.querySelector('input[type="checkbox"]');
            if (input) label.classList.toggle('is-selected', input.checked);
        });
        if (deadlineWrap) deadlineWrap.hidden = true;
    };

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            if (!validateStep(step)) return;
            if (step === 5) {
                showStep(6);
                loadEstimateIfNeeded();
                return;
            }
            if (step < total) showStep(step + 1);
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            if (step > 1) showStep(step - 1);
        });
    }

    form.querySelectorAll('[data-pole-checkbox]').forEach(function (ch) {
        ch.addEventListener('change', syncPoleSections);
    });

    form.querySelectorAll('input[name="quote[p1][project_types][]"]').forEach(function (cb) {
        cb.addEventListener('change', syncP1Submodules);
    });
    form.querySelectorAll('input[name="quote[p2][levers][]"]').forEach(function (cb) {
        cb.addEventListener('change', syncP2LeverPanels);
    });

    form.querySelectorAll('.quote-pole-card').forEach(function (label) {
        var input = label.querySelector('input[type="checkbox"]');
        if (!input) return;
        function upd() {
            label.classList.toggle('is-selected', input.checked);
        }
        input.addEventListener('change', upd);
        upd();
    });

    if (deadlineSel) {
        deadlineSel.addEventListener('change', function () {
            if (deadlineWrap) deadlineWrap.hidden = deadlineSel.value !== 'oui';
        });
    }

    var qRdvPrevM = document.getElementById('quote-rdv-prev-month');
    if (qRdvPrevM) {
        qRdvPrevM.addEventListener('click', function () {
            qRdvViewMonth--;
            if (qRdvViewMonth < 0) {
                qRdvViewMonth = 11;
                qRdvViewYear--;
            }
            renderQuoteRdvCalendar();
        });
    }

    var qRdvNextM = document.getElementById('quote-rdv-next-month');
    if (qRdvNextM) {
        qRdvNextM.addEventListener('click', function () {
            qRdvViewMonth++;
            if (qRdvViewMonth > 11) {
                qRdvViewMonth = 0;
                qRdvViewYear++;
            }
            renderQuoteRdvCalendar();
        });
    }

    form.querySelectorAll('.quote-rdv-slot-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!qRdvSelectedIso) return;
            if (qRdvSkip) qRdvSkip.value = '0';
            qRdvSelectedSlot = btn.getAttribute('data-quote-rdv-slot') || '';
            form.querySelectorAll('.quote-rdv-slot-btn').forEach(function (b) {
                b.classList.remove('is-selected');
            });
            btn.classList.add('is-selected');
            if (qRdvValMsg) qRdvValMsg.hidden = true;
            syncQuoteRdvHidden();
        });
    });

    if (btnNoRdv) {
        btnNoRdv.addEventListener('click', function () {
            qRdvSelectedIso = '';
            qRdvSelectedSlot = '';
            if (qRdvDate) qRdvDate.value = '';
            if (qRdvSlot) qRdvSlot.value = '';
            if (qRdvSkip) qRdvSkip.value = '1';
            if (qRdvSlots) qRdvSlots.hidden = true;
            form.querySelectorAll('.quote-rdv-slot-btn').forEach(function (b) {
                b.classList.remove('is-selected');
            });
            if (qRdvValMsg) qRdvValMsg.hidden = true;
            renderQuoteRdvCalendar();
            if (!validateStep(5)) return;
            showStep(6);
            loadEstimateIfNeeded();
        });
    }

    form.addEventListener('submit', function (e) {
        var sub = e.submitter;
        if (sub && sub.id === 'quote-submit' && qRdvSkip) {
            qRdvSkip.value = '0';
        }
        if (!validateAllSteps()) {
            e.preventDefault();
        }
    });

    showStep(1);

    if (window.location.hash === '#devis') {
        openModal();
    }
})();
