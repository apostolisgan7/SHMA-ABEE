/**
 * Off-Canvas Cart / Quote List
 * mode = 'yith' → YITH Request a Quote
 * mode = 'wc'   → standard WooCommerce mini-cart
 */
export function initOffcanvasCart() {
    const cart        = document.getElementById('offcanvas-cart');
    const cartToggle  = document.querySelector('.header-cart .cart-contents');
    const cartClose   = document.querySelector('.offcanvas-cart__close');
    const cartOverlay = document.querySelector('.offcanvas-cart__overlay');

    if (!cart || !cartToggle) return;

    const mode   = window.ruined_cart_mode || 'wc';
    const ajaxUrl = '/wp-admin/admin-ajax.php';

    const yithUrl = (typeof ywraq_frontend !== 'undefined' && ywraq_frontend.ajaxurl)
        ? ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action')
        : '/?wc-ajax=yith_ywraq_action';

    // ── Open / Close ──────────────────────────────────────────────────────────
    function toggleCart(show = true) {
        if (show) {
            document.body.classList.add('offcanvas-cart-open');
            cart.classList.add('is-open');
            window.__lenis__?.stop();
            document.documentElement.classList.add('scroll-locked');
            setTimeout(() => cartClose && cartClose.focus(), 100);
        } else {
            document.body.classList.remove('offcanvas-cart-open');
            cart.classList.remove('is-open');
            window.__lenis__?.start();
            document.documentElement.classList.remove('scroll-locked');
        }
    }

    cartClose   && cartClose.addEventListener('click',   () => toggleCart(false));
    cartOverlay && cartOverlay.addEventListener('click', () => toggleCart(false));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && cart.classList.contains('is-open')) toggleCart(false);
    });

    // ── Count badge ───────────────────────────────────────────────────────────
    function updateCount(count) {
        let wrapper = document.querySelector('.header-cart .count-wrapper');
        const badge = document.querySelector('.header-cart .count');

        if (count > 0) {
            if (badge)   badge.textContent    = count;
            if (wrapper) wrapper.style.display = '';
            if (!wrapper) {
                const link = document.querySelector('.header-cart .cart-contents');
                if (link) {
                    link.insertAdjacentHTML('beforeend',
                        `<div class="count-wrapper"><span class="count">${count}</span></div>`);
                }
            }
        } else {
            if (wrapper) wrapper.style.display = 'none';
        }
    }

    // ── Refresh cart body via AJAX ─────────────────────────────────────────────
    function refreshCart() {
        const action = mode === 'yith' ? 'rv_raq_mini_list' : 'rv_wc_mini_list';
        return fetch(ajaxUrl, {
            method:      'POST',
            credentials: 'same-origin',
            headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:        new URLSearchParams({ action }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const body = document.querySelector('.offcanvas-cart__body');
                if (body) body.innerHTML = data.data.html;
                updateCount(data.data.count);
            }
        })
        .catch(console.error);
    }

    // ── Open on click (always refresh first) ─────────────────────────────────
    cartToggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        refreshCart().then(() => toggleCart(true));
    });

    // ── YITH mode ─────────────────────────────────────────────────────────────
    if (mode === 'yith') {
        // Qty +/- buttons (event delegation)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.raq-qty-btn');
            if (!btn) return;

            e.preventDefault();
            const key     = btn.dataset.key;
            const isPlus  = btn.classList.contains('qty-plus');
            const valEl   = btn.closest('.mini-cart__qty')?.querySelector('.qty-value');
            const current = parseInt(valEl?.textContent || '1', 10);
            const newQty  = isPlus ? current + 1 : Math.max(1, current - 1);

            if (newQty === current) return;
            if (valEl) valEl.textContent = newQty;

            fetch(yithUrl, {
                method:      'POST',
                credentials: 'same-origin',
                headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:        new URLSearchParams({
                    ywraq_action: 'update_item_quantity',
                    key,
                    quantity: newQty,
                }),
            })
            .then(r => r.json())
            .then(() => refreshCart())
            .catch(console.error);
        });

        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('yith_wwraq_added_successfully', () => {
                refreshCart().then(() => toggleCart(true));
            });
            jQuery(document).on('yith_wwraq_removed_successfully', () => {
                refreshCart();
            });
        }
    }

    // ── WC mode ───────────────────────────────────────────────────────────────
    if (mode === 'wc' && typeof jQuery !== 'undefined') {
        jQuery(document.body).on('added_to_cart', () => {
            refreshCart().then(() => toggleCart(true));
        });
        jQuery(document.body).on('removed_from_cart wc_fragments_refreshed', () => {
            refreshCart();
        });
    }

    return { toggleCart, refreshCart };
}
