/**
 * Modale « Prendre rendez-vous » : calendrier + envoi via /contact/message (ajax=1).
 */
(function () {
    const modal = document.getElementById('rdv-modal');
    if (!modal) return;

    const grid = document.getElementById('rdv-cal-grid');
    const titleEl = document.getElementById('rdv-cal-title');
    const slotsWrap = document.getElementById('rdv-slots');
    const slotsHint = document.getElementById('rdv-slots-hint');
    const form = document.getElementById('rdv-form');
    const msgField = document.getElementById('rdv-message-field');
    const submitBtn = document.getElementById('rdv-form-submit');
    const errEl = document.getElementById('rdv-form-error');
    const mainBlock = document.getElementById('rdv-modal-main');
    const successBlock = document.getElementById('rdv-modal-success');

    const MONTHS = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
    ];

    let viewYear;
    let viewMonth;
    let selectedIso = '';
    let selectedSlot = '';

    function pad(n) {
        return n < 10 ? '0' + n : String(n);
    }

    function toIso(y, m, d) {
        return y + '-' + pad(m + 1) + '-' + pad(d);
    }

    function startOfToday() {
        const t = new Date();
        t.setHours(0, 0, 0, 0);
        return t;
    }

    function openModal() {
        const quoteModal = document.getElementById('quote-modal');
        if (quoteModal && !quoteModal.hidden) {
            quoteModal.hidden = true;
            document.body.classList.remove('quote-modal-open');
        }
        modal.hidden = false;
        document.body.classList.add('rdv-modal-open');
        const t = new Date();
        viewYear = t.getFullYear();
        viewMonth = t.getMonth();
        selectedIso = '';
        selectedSlot = '';
        slotsWrap.hidden = true;
        if (form) {
            form.reset();
        }
        if (msgField) msgField.value = '';
        document.querySelectorAll('.rdv-slot-btn').forEach((b) => b.classList.remove('is-selected'));
        document.querySelectorAll('.rdv-cal__day.is-selected').forEach((el) => el.classList.remove('is-selected'));
        if (errEl) {
            errEl.hidden = true;
            errEl.textContent = '';
        }
        if (mainBlock) mainBlock.hidden = false;
        if (successBlock) successBlock.hidden = true;
        updateSubmitState();
        renderCalendar();
        const closeBtn = modal.querySelector('.rdv-modal__close');
        if (closeBtn) closeBtn.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('rdv-modal-open');
    }

    function renderCalendar() {
        if (!grid || !titleEl) return;
        titleEl.textContent = MONTHS[viewMonth] + ' ' + viewYear;

        const first = new Date(viewYear, viewMonth, 1);
        const startWeekday = (first.getDay() + 6) % 7;
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const today = startOfToday();

        const frag = document.createDocumentFragment();
        const prevMonthDays = new Date(viewYear, viewMonth, 0).getDate();

        for (let i = 0; i < startWeekday; i++) {
            const d = prevMonthDays - startWeekday + i + 1;
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'rdv-cal__day rdv-cal__day--muted';
            cell.textContent = String(d);
            cell.disabled = true;
            frag.appendChild(cell);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'rdv-cal__day';
            cell.textContent = String(d);
            const cur = new Date(viewYear, viewMonth, d);
            cur.setHours(0, 0, 0, 0);
            if (cur < today) {
                cell.classList.add('rdv-cal__day--disabled');
                cell.disabled = true;
            } else {
                const iso = toIso(viewYear, viewMonth, d);
                if (iso === selectedIso) cell.classList.add('is-selected');
                cell.addEventListener('click', function () {
                    selectedIso = iso;
                    selectedSlot = '';
                    document.querySelectorAll('.rdv-slot-btn').forEach((b) => b.classList.remove('is-selected'));
                    slotsWrap.hidden = false;
                    const dt = new Date(viewYear, viewMonth, d);
                    slotsHint.textContent =
                        'Date choisie : ' +
                        d +
                        ' ' +
                        MONTHS[viewMonth].toLowerCase() +
                        ' ' +
                        viewYear +
                        ' — choisissez un créneau.';
                    renderCalendar();
                    updateMessage();
                    updateSubmitState();
                });
            }
            frag.appendChild(cell);
        }

        const totalCells = startWeekday + daysInMonth;
        const trailing = (7 - (totalCells % 7)) % 7;
        for (let i = 1; i <= trailing; i++) {
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'rdv-cal__day rdv-cal__day--muted';
            cell.textContent = String(i);
            cell.disabled = true;
            frag.appendChild(cell);
        }

        grid.innerHTML = '';
        grid.appendChild(frag);
    }

    function updateMessage() {
        if (!msgField) return;
        if (!selectedIso) {
            msgField.value = '';
            return;
        }
        var parts = ['Demande de rendez-vous'];
        parts.push('Date souhaitée : ' + selectedIso + '.');
        if (selectedSlot) parts.push('Créneau : ' + selectedSlot + '.');
        msgField.value = parts.join(' ');
    }

    function updateSubmitState() {
        if (!submitBtn || !form) return;
        const nameOk = form.querySelector('[name="full_name"]')?.value.trim();
        const emailOk = form.querySelector('[name="email"]')?.value.trim();
        const privacyOk = form.querySelector('[name="privacy_ok"]')?.checked;
        submitBtn.disabled = !(selectedIso && selectedSlot && nameOk && emailOk && privacyOk);
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-open-rdv-modal]')) {
            e.preventDefault();
            openModal();
            return;
        }
        if (e.target.closest('[data-rdv-modal-close]')) {
            e.preventDefault();
            closeModal();
        }
    });

    modal.querySelector('[data-rdv-prev-month]')?.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 0) {
            viewMonth = 11;
            viewYear--;
        }
        renderCalendar();
    });

    modal.querySelector('[data-rdv-next-month]')?.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 11) {
            viewMonth = 0;
            viewYear++;
        }
        renderCalendar();
    });

    document.querySelectorAll('.rdv-slot-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!selectedIso) return;
            selectedSlot = btn.getAttribute('data-rdv-slot') || '';
            document.querySelectorAll('.rdv-slot-btn').forEach((b) => b.classList.remove('is-selected'));
            btn.classList.add('is-selected');
            updateMessage();
            updateSubmitState();
        });
    });

    form?.addEventListener('input', updateSubmitState);
    form?.addEventListener('change', updateSubmitState);

    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        if (errEl) {
            errEl.hidden = true;
            errEl.textContent = '';
        }
        const fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (res) {
                return res.text().then(function (text) {
                    var data = null;
                    if (text) {
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            data = null;
                        }
                    }
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (r) {
                if (r.ok && r.data && r.data.ok) {
                    if (mainBlock) mainBlock.hidden = true;
                    if (successBlock) successBlock.hidden = false;
                    return;
                }
                if (errEl) {
                    errEl.hidden = false;
                    errEl.textContent =
                        r.data && r.data.error === 'privacy'
                            ? 'Veuillez accepter la politique de confidentialité pour envoyer votre demande.'
                            : 'Envoi impossible pour le moment. Réessayez ou écrivez-nous depuis la page Contact.';
                }
            })
            .catch(function () {
                if (errEl) {
                    errEl.hidden = false;
                    errEl.textContent = 'Erreur réseau. Vérifiez votre connexion ou utilisez la page Contact.';
                }
            });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });
})();
