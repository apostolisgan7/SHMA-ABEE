export function initQty() {
    document.addEventListener('click', function (e) {

        const plus  = e.target.closest('.qty-plus');
        const minus = e.target.closest('.qty-minus');

        if (!plus && !minus) return;

        e.preventDefault();

        const btn   = plus || minus;
        const key   = btn.dataset.key;
        const delta = plus ? 1 : -1;

        const wrapper = btn.closest('.mini-cart__qty');
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
                delta: delta,
                nonce: window.ruined_nonce || ''
            })
        })
            .then(response => {
                if (!response.ok) {
                    console.error('Cart update failed');
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    document.body.dispatchEvent(new Event('wc_fragment_refresh'));
                } else {
                    console.error('Cart update error:', data?.data || 'Unknown error');
                }
            })
            .catch(error => {
                console.error('Cart update error:', error);
            })
            .finally(() => {
                wrapper?.classList.remove('is-loading');
            });

    });
}
