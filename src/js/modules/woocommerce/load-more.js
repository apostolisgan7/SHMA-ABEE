export function initLoadMoreProducts() {

    // 1. EVENT DELEGATION: Ακούμε στο document για να μην "χάνεται" το κουμπί μετά τα φίλτρα
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.rv-load-more');
        if (!btn) return;

        e.preventDefault(); // 🔥 Σταματάει το URL jump (#)

        const container = btn.closest('.rv-load-more-wrap');
        const dataEl = container.querySelector('.rv-load-more-data');
        const grid = document.querySelector('.products');

        if (!dataEl || !grid || btn.classList.contains('is-loading')) return;

        let page = parseInt(grid.dataset.page || '1', 10);
        const max = parseInt(dataEl.dataset.max, 10);

        page++;

        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');

        const formData = new FormData();
        const query = JSON.parse(dataEl.dataset.query);

        formData.append('action', 'rv_load_more_products');
        formData.append('page', page);
        formData.append('query', JSON.stringify(query));

        try {
            const res = await fetch(window.ajaxurl, {
                method: 'POST',
                body: formData,
            });

            const html = await res.text();

            if (html.trim()) {
                const prevCount = grid.querySelectorAll('.rv-product-card').length;

                grid.insertAdjacentHTML('beforeend', html);
                grid.dataset.page = page;

                // Ενημέρωση result count (προαιρετικό)
                const resultCountEl = document.getElementById('rv-result-count');
                if (resultCountEl) {
                    const total = parseInt(dataEl.dataset.total, 10);
                    const shown = grid.querySelectorAll('.rv-product-card').length;
                    resultCountEl.textContent = `Showing 1–${shown} of ${total} results`;
                }

                // GSAP Animation
                const cards = grid.querySelectorAll('.rv-product-card');
                const newCards = Array.from(cards).slice(prevCount);

                gsap.fromTo(newCards,
                    { autoAlpha: 0, y: 30 },
                    { autoAlpha: 1, y: 0, duration: 0.6, ease: 'power3.out', stagger: 0.08, clearProps: 'all' }
                );

                if (window.__lenis__) window.__lenis__.resize();
            }

            // Αν φτάσαμε στο τέλος, σβήσε το wrapper
            if (page >= max) {
                container.remove();
            }

        } catch (err) {
            console.error("Load more failed:", err);
        } finally {
            btn.classList.remove('is-loading');
            btn.removeAttribute('aria-busy');
        }
    });

    // 2. YITH HOOK: Όταν ο χρήστης αλλάζει φίλτρο, μηδενίζουμε το pagination στο grid
    // Χρησιμοποιούμε jQuery γιατί το YITH εκπέμπει jQuery events
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('yith-wcan-ajax-filtered', function() {
            const grid = document.querySelector('.products');
            if (grid) {
                grid.dataset.page = '1';
            }
        });
    }
}