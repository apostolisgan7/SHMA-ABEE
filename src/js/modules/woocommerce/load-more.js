export function initLoadMoreProducts() {

    const btn = document.querySelector('.rv-load-more');
    const grid = document.querySelector('.products');
    const dataEl = document.querySelector('.rv-load-more-data'); // 👈 data holder

    if (!btn || !grid || !dataEl) {
        return;
    }

    let page = parseInt(grid.dataset.page || '1', 10);
    const max = parseInt(dataEl.dataset.max, 10);

    btn.addEventListener('click', async (e) => {
        e.preventDefault(); // 🔥 ΑΠΑΡΑΙΤΗΤΟ

        page++;

        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');

        const formData = new FormData();
        formData.append('action', 'rv_load_more_products');
        formData.append('page', page);

        try {
            const res = await fetch(window.ajaxurl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            });

            const html = await res.text();

            if (html.trim()) {
                const prevCount = grid.querySelectorAll('.rv-product-card').length;

                grid.insertAdjacentHTML('beforeend', html);
                grid.dataset.page = page;

                const cards = grid.querySelectorAll('.rv-product-card');
                const newCards = Array.from(cards).slice(prevCount);

                // 🔥 αρχική κατάσταση (ΠΡΙΝ το animation)
                gsap.set(newCards, {
                    autoAlpha: 0,
                    y: 30,
                });

                // 🔥 stagger animation (ένα-ένα)
                gsap.to(newCards, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power3.out',
                    stagger: 0.08,
                    clearProps: 'all',
                });

                // 🔥 LENIS resize
                if (window.__lenis__) {
                    window.__lenis__.resize();
                }
            }


            if (page >= max) {
                btn.remove();
            }

        } catch (err) {
        } finally {
            btn.classList.remove('is-loading');
            btn.removeAttribute('aria-busy');
        }
    });
}
