/* Laboratoire / hub — recherche client & formulaire participation */

document.addEventListener('DOMContentLoaded', () => {
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
