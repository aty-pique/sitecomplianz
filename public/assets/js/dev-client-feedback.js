/**
 * Retours client (dev-notes) : bouton en haut à droite pour activer le mode commentaire,
 * puis clic sur la page pour ouvrir le formulaire (POST /dev-feedback).
 * Actif uniquement si le script est chargé (DEV_CLIENT_FEEDBACK_ENABLED=1).
 */
(function () {
    let armed = false;

    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.id = 'dev-fb-toggle';
    toggle.className = 'dev-fb-toggle';
    toggle.setAttribute('aria-pressed', 'false');
    toggle.setAttribute('aria-label', 'Activer le mode retours pour le développeur');
    toggle.title = 'Retours développeur — activer puis cliquer sur la page pour commenter';
    toggle.innerHTML =
        '<span class="dev-fb-toggle__ico" aria-hidden="true">' +
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' +
        '</span><span class="dev-fb-toggle__txt">Retours</span>';

    const banner = document.createElement('div');
    banner.id = 'dev-fb-armed-banner';
    banner.className = 'dev-fb-armed-banner';
    banner.hidden = true;
    banner.setAttribute('role', 'status');
    banner.textContent =
        'Mode retours activé — cliquez sur la page pour placer votre commentaire. Recliquez sur « Retours » pour annuler.';

    const root = document.createElement('div');
    root.id = 'dev-fb-root';
    root.className = 'dev-fb-root';
    root.hidden = true;
    root.innerHTML = [
        '<div class="dev-fb-backdrop" data-dev-fb-close tabindex="-1"></div>',
        '<div class="dev-fb-panel" role="dialog" aria-modal="true" aria-labelledby="dev-fb-title">',
        '  <form class="dev-fb-form" method="post" action="/dev-feedback">',
        '    <h2 id="dev-fb-title">Note au développeur</h2>',
        '    <p class="dev-fb-help">La page ci-dessous est indiquée automatiquement.</p>',
        '    <label class="dev-fb-label" for="dev-fb-page">URL</label>',
        '    <input id="dev-fb-page" class="dev-fb-input dev-fb-input--url" type="text" name="page_url" readonly>',
        '    <label class="dev-fb-label" for="dev-fb-msg">Commentaire <abbr title="requis">*</abbr></label>',
        '    <textarea id="dev-fb-msg" class="dev-fb-textarea" name="message" rows="5" required maxlength="8000" placeholder="Ce que le développeur doit prendre en compte…"></textarea>',
        '    <input type="hidden" name="ajax" value="1">',
        '    <div class="dev-fb-actions">',
        '      <button type="button" class="btn btn-outline" data-dev-fb-close>Annuler</button>',
        '      <button type="submit" class="btn btn-primary">Envoyer</button>',
        '    </div>',
        '    <p class="dev-fb-status" role="status" aria-live="polite"></p>',
        '    <p class="dev-fb-foot"><a href="/dev-feedback">Voir tous les retours</a></p>',
        '  </form>',
        '</div>',
    ].join('');

    document.body.appendChild(toggle);
    document.body.appendChild(banner);
    document.body.appendChild(root);

    const backdrop = root.querySelector('.dev-fb-backdrop');
    const panel = root.querySelector('.dev-fb-panel');
    const form = root.querySelector('.dev-fb-form');
    const pageInput = root.querySelector('#dev-fb-page');
    const msgInput = root.querySelector('#dev-fb-msg');
    const statusEl = root.querySelector('.dev-fb-status');

    function setArmed(on) {
        armed = on;
        document.body.classList.toggle('dev-fb-armed', on);
        banner.hidden = !on;
        toggle.setAttribute('aria-pressed', on ? 'true' : 'false');
        toggle.classList.toggle('dev-fb-toggle--active', on);
        toggle.title = on
            ? 'Mode retours actif — cliquez sur la page ou recliquez pour annuler'
            : 'Retours développeur — activer puis cliquer sur la page pour commenter';
    }

    function open(x, y) {
        root.hidden = false;
        document.body.classList.add('dev-fb-open');
        pageInput.value = window.location.href;
        msgInput.value = '';
        statusEl.textContent = '';

        void panel.offsetWidth;
        const rect = panel.getBoundingClientRect();
        let left = x - rect.width / 2;
        let top = y - rect.height / 2;
        left = Math.max(12, Math.min(left, window.innerWidth - rect.width - 12));
        top = Math.max(12, Math.min(top, window.innerHeight - rect.height - 12));
        panel.style.left = left + 'px';
        panel.style.top = top + 'px';

        msgInput.focus();
    }

    function close() {
        root.hidden = true;
        document.body.classList.remove('dev-fb-open');
    }

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!root.hidden) return;
        setArmed(!armed);
    });

    document.addEventListener(
        'click',
        function (e) {
            if (!armed) return;
            if (toggle.contains(e.target)) return;
            if (!root.hidden && root.contains(e.target)) return;
            e.preventDefault();
            e.stopPropagation();
            setArmed(false);
            open(e.clientX, e.clientY);
        },
        true
    );

    root.querySelectorAll('[data-dev-fb-close]').forEach(function (el) {
        el.addEventListener('click', close);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (!root.hidden) close();
            else if (armed) setArmed(false);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        statusEl.textContent = 'Envoi…';
        const fd = new FormData(form);
        fd.set('ajax', '1');
        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        })
            .then(function (r) {
                return r.text().then(function (text) {
                    try {
                        return { ok: r.ok, data: JSON.parse(text) };
                    } catch (err) {
                        return { ok: false, data: { error: text.slice(0, 120) } };
                    }
                });
            })
            .then(function (res) {
                if (res.ok && res.data && res.data.ok) {
                    statusEl.textContent = 'Enregistré. Merci !';
                    msgInput.value = '';
                    window.setTimeout(close, 900);
                } else {
                    statusEl.textContent =
                        (res.data && res.data.error) || 'Erreur lors de l’envoi.';
                }
            })
            .catch(function () {
                statusEl.textContent = 'Erreur réseau.';
            });
    });
})();
