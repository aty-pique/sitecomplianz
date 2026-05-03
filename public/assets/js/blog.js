/* Blog — afficher plus d’articles + redirection newsletter → contact */

document.addEventListener('DOMContentLoaded', () => {
    const loadMore = document.getElementById('blog-load-more');
    if (loadMore) {
        loadMore.addEventListener('click', () => {
            document.querySelectorAll('.blog-card--folded').forEach((el) => {
                el.classList.remove('blog-card--folded');
            });
            loadMore.hidden = true;
        });
    }

    const nlBtn = document.getElementById('blog-nl-submit');
    const nlInput = document.getElementById('blog-nl-email');
    if (nlBtn && nlInput) {
        nlBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const em = nlInput.value.trim();
            const q = em !== '' ? `?prefill_email=${encodeURIComponent(em)}` : '';
            window.location.href = `/contact${q}`;
        });
    }
});
