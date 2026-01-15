function initCustomQty() {
    const qtyInput = document.querySelector('.quantity input.qty');
    if (!qtyInput) return;

    // αποφυγή διπλού init
    if (qtyInput.dataset.customized) return;
    qtyInput.dataset.customized = 'true';

    const min = parseInt(qtyInput.min) || 1;
    const max = parseInt(qtyInput.max) || Infinity;
    const step = parseInt(qtyInput.step) || 1;

    // wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'rv-qty';

    const value = document.createElement('div');
    value.className = 'rv-qty-value';
    value.textContent = qtyInput.value;

    const controls = document.createElement('div');
    controls.className = 'rv-qty-controls';

    const inc = document.createElement('button');
    inc.type = 'button';
    inc.className = 'rv-qty-btn';
    inc.innerHTML = '<svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path d="M0.911945 6.68422L6.68302 0.911987L12.4541 6.68422" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/>\n' +
        '</svg>\n';

    const dec = document.createElement('button');
    dec.type = 'button';
    dec.className = 'rv-qty-btn';
    dec.innerHTML = '<svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path d="M12.4543 0.91197L6.68319 6.6842L0.912109 0.91197" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/>\n' +
        '</svg>\n';

    controls.appendChild(inc);
    controls.appendChild(dec);
    wrapper.appendChild(value);
    wrapper.appendChild(controls);

    qtyInput.parentNode.appendChild(wrapper);

    function update(newVal) {
        newVal = Math.max(min, Math.min(max, newVal));
        qtyInput.value = newVal;
        value.textContent = newVal;
        qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    inc.addEventListener('click', () => {
        update(parseInt(qtyInput.value) + step);
    });

    dec.addEventListener('click', () => {
        update(parseInt(qtyInput.value) - step);
    });
}



export function initSummary() {
    const summary = document.querySelector('.rv-product-header');
    if (!summary) return;

    /* =========================
       * PRICE MIRROR (THE FIX)
       * ========================= */
    const priceBox = summary.querySelector('.summary.entry-summary');
    if (priceBox) {
        const originalHTML = priceBox.innerHTML;

        jQuery(document.body).on('found_variation', function (e, variation) {
            if (variation?.price_html) {
                priceBox.innerHTML = variation.price_html;
            }
        });

        jQuery(document.body).on('reset_data', function () {
            priceBox.innerHTML = originalHTML;
        });
    }

    /* =========================
     * COPY SKU
     * ========================= */

    summary.addEventListener('click', (e) => {
        const btn = e.target.closest('.copy-sku');
        if (!btn) return;

        const sku = btn.closest('.rv-product-sku')?.dataset.sku;
        if (!sku) return;

        if (navigator.clipboard) {
            navigator.clipboard.writeText(sku).then(() => {
                btn.classList.add('copied');
                setTimeout(() => btn.classList.remove('copied'), 1200);
            });
        } else {
            console.warn('Clipboard not available');
        }
    });


    /* =========================
     * SCROLL TO DETAILS
     * ========================= */
    const detailsLink = summary.querySelector('.rv-more-details');
    if (detailsLink) {
        detailsLink.addEventListener('click', (e) => {
            e.preventDefault();

            const target = document.querySelector('#tab-description, .woocommerce-tabs');
            if (!target) return;

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    }


    /* =========================
     * VIDEO TUTORIAL
     * ========================= */
    const videoTrigger = summary.querySelector('.rv-video-trigger');
    if (videoTrigger) {
        videoTrigger.addEventListener('click', (e) => {
            e.preventDefault();

            // εδώ μπορείς να κουμπώσεις Fancybox / ACF URL

        });
    }

    initCustomQty();

    jQuery(document).on('found_variation reset_data', () => {
        initCustomQty();
    });


}
