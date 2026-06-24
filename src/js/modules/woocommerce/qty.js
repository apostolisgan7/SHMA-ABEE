function sendCartUpdate(key, params, wrapper) {
    wrapper?.classList.add('is-loading');

    fetch(wc_cart_fragments_params.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            action: 'ruined_update_cart_qty',
            cart_item_key: key,
            nonce: window.ruined_nonce || '',
            ...params
        })
    })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (data?.success) {
                document.body.dispatchEvent(new Event('wc_fragment_refresh'));
            }
        })
        .catch(err => console.error('Cart update error:', err))
        .finally(() => wrapper?.classList.remove('is-loading'));
}

export function initQty() {
    // +/- buttons
    document.addEventListener('click', function (e) {
        const plus  = e.target.closest('.qty-plus');
        const minus = e.target.closest('.qty-minus');
        if (!plus && !minus) return;

        e.preventDefault();
        const btn     = plus || minus;
        const key     = btn.dataset.key;
        const delta   = plus ? 1 : -1;
        const wrapper = btn.closest('.mini-cart__qty');

        sendCartUpdate(key, { delta }, wrapper);
    });

    // direct input (blur or Enter)
    document.addEventListener('change', function (e) {
        const input = e.target.closest('.mini-cart__qty .qty-value');
        if (!input) return;

        const key     = input.dataset.key;
        const qty     = Math.max(1, parseInt(input.value) || 1);
        input.value   = qty;
        const wrapper = input.closest('.mini-cart__qty');

        sendCartUpdate(key, { qty }, wrapper);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        const input = e.target.closest('.mini-cart__qty .qty-value');
        if (!input) return;
        input.blur();
    });
}
