/* Laboratoire / hub — recherche client & formulaire participation & modale « bientôt » */

document.addEventListener('DOMContentLoaded', () => {
    const soonModal = document.getElementById('lab-soon-modal');
    const openSoon = () => {
        if (!soonModal) return;
        soonModal.hidden = false;
        soonModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('lab-soon-modal-open');
        const ok = soonModal.querySelector('.lab-soon-modal__ok');
        if (ok) ok.focus();
    };
    const closeSoon = () => {
        if (!soonModal) return;
        soonModal.hidden = true;
        soonModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lab-soon-modal-open');
    };
    document.querySelectorAll('[data-lab-soon-trigger]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            openSoon();
        });
    });
    document.querySelectorAll('[data-lab-soon-close]').forEach((el) => {
        el.addEventListener('click', () => closeSoon());
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && soonModal && !soonModal.hidden) closeSoon();
    });
    const searchInput = document.getElementById('lab-hero-search');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            document.querySelectorAll('.lab-searchable').forEach((el) => {
                const blob = (el.getAttribute('data-lab-search') || '').toLowerCase();
                const show = !q || blob.includes(q);
                el.classList.toggle('is-lab-hidden', !show);
            });
        });
    }

    const form = document.getElementById('lab-participate-form');
    const first = document.getElementById('lab-first');
    const last = document.getElementById('lab-last');
    const full = document.getElementById('lab-full-name');
    if (form && first && last && full) {
        form.addEventListener('submit', () => {
            const a = first.value.trim();
            const b = last.value.trim();
            full.value = `${a} ${b}`.trim();
        });
    }

    const nlBtn = document.getElementById('lab-nl-btn');
    const nlIn = document.getElementById('lab-nl-email');
    if (nlBtn && nlIn) {
        nlBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const em = nlIn.value.trim();
            window.location.href = '/contact' + (em ? `?prefill_email=${encodeURIComponent(em)}` : '');
        });
    }
});
