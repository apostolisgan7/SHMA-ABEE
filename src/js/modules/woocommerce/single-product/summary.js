
function initCustomQty() {
    const qtyInput = document.querySelector('.quantity input.qty');
    if (!qtyInput) return;

    if (qtyInput.dataset.customized) return;
    qtyInput.dataset.customized = 'true';

    const min  = parseInt(qtyInput.min)  || 1;
    const max  = parseInt(qtyInput.max)  || Infinity;
    const step = parseInt(qtyInput.step) || 1;

    const wrapper = document.createElement('div');
    wrapper.className = 'rv-qty';

    const visibleInput = document.createElement('input');
    visibleInput.type      = 'number';
    visibleInput.className = 'rv-qty-value';
    visibleInput.value     = qtyInput.value;
    visibleInput.min       = min;
    if (isFinite(max)) visibleInput.max = max;
    visibleInput.step      = step;

    const controls = document.createElement('div');
    controls.className = 'rv-qty-controls';

    const inc = document.createElement('button');
    inc.type      = 'button';
    inc.className = 'rv-qty-btn';
    inc.innerHTML = '<svg width="14" height="8" viewBox="0 0 14 8" fill="none"><path d="M0.911945 6.68422L6.68302 0.911987L12.4541 6.68422" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    const dec = document.createElement('button');
    dec.type      = 'button';
    dec.className = 'rv-qty-btn';
    dec.innerHTML = '<svg width="14" height="8" viewBox="0 0 14 8" fill="none"><path d="M12.4543 0.91197L6.68319 6.6842L0.912109 0.91197" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    controls.appendChild(inc);
    controls.appendChild(dec);
    wrapper.appendChild(visibleInput);
    wrapper.appendChild(controls);

    qtyInput.parentNode.appendChild(wrapper);

    Object.assign(qtyInput.style, { position: 'absolute', opacity: '0', pointerEvents: 'none', width: '0', height: '0' });

    function update(newVal) {
        newVal = Math.max(min, Math.min(max, newVal));
        qtyInput.value       = newVal;
        visibleInput.value   = newVal;
        qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    inc.addEventListener('click', () => update(parseInt(qtyInput.value) + step));
    dec.addEventListener('click', () => update(parseInt(qtyInput.value) - step));

    visibleInput.addEventListener('change', () => update(parseInt(visibleInput.value) || min));
    visibleInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); visibleInput.blur(); } });
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
     * SKU on variation change
     * ========================= */
    const skuEl      = summary.querySelector('.rv-product-sku');
    const panelSkuEl = document.querySelector('.rv-mobile-panel-sku');

    if (skuEl || panelSkuEl) {
        const skuSpan          = skuEl?.querySelector('span');
        const originalSku      = skuEl?.dataset.sku ?? '';
        const originalSkuText  = skuSpan?.textContent ?? '';
        const originalPanelSku = panelSkuEl?.textContent ?? '';

        jQuery(document.body).on('found_variation.rvSku', (e, variation) => {
            const hasSku = Boolean(variation.sku);
            if (skuEl) {
                skuEl.hidden = !hasSku;
                if (hasSku) {
                    skuEl.dataset.sku = variation.sku;
                    if (skuSpan) skuSpan.textContent = 'Κωδικός: ' + variation.sku;
                }
            }
            if (panelSkuEl) {
                panelSkuEl.hidden = !hasSku;
                if (hasSku) panelSkuEl.textContent = 'Κωδικός: ' + variation.sku;
            }
        });

        jQuery(document.body).on('reset_data.rvSku', () => {
            if (skuEl) {
                skuEl.hidden = false;
                skuEl.dataset.sku = originalSku;
                if (skuSpan) skuSpan.textContent = originalSkuText;
            }
            if (panelSkuEl) {
                panelSkuEl.hidden = false;
                panelSkuEl.textContent = originalPanelSku;
            }
        });
    }

    /* =========================
     * STOCK STATUS on variation change
     * ========================= */
    const stockEl = summary.querySelector('.rv-product-stock');

    if (stockEl) {
        const stockSpan     = stockEl.querySelector('span');
        const originalClass = stockEl.className;
        const originalLabel = stockSpan?.textContent ?? '';

        const STOCK_MAP = {
            in:        { label: 'Διαθέσιμο',           cls: 'rv-product-stock dot_icon stock-in' },
            out:       { label: 'Μη διαθέσιμο',        cls: 'rv-product-stock dot_icon stock-out' },
            backorder: { label: 'Κατόπιν παραγγελίας', cls: 'rv-product-stock dot_icon stock-backorder' },
        };

        jQuery(document.body).on('found_variation.rvStock', (e, variation) => {
            let key = variation.is_in_stock ? 'in' : 'out';
            if (!variation.is_in_stock && variation.backorders_allowed) key = 'backorder';
            const { label, cls } = STOCK_MAP[key];
            stockEl.className = cls;
            if (stockSpan) stockSpan.textContent = label;
        });

        jQuery(document.body).on('reset_data.rvStock', () => {
            stockEl.className = originalClass;
            if (stockSpan) stockSpan.textContent = originalLabel;
        });
    }

    /* =========================
     * COPY SKU
     * ========================= */

    summary.addEventListener('click', (e) => {
        const btn = e.target.closest('.copy-sku');
        if (!btn) return;

        // Σταματάμε το refresh της σελίδας
        e.preventDefault();

        const sku = btn.closest('.rv-product-sku')?.dataset.sku;
        if (!sku) return;

        // Λειτουργία για το "Copied" animation/feedback
        const showSuccess = (element) => {
            element.classList.add('copied');
            // Προαιρετικά: αν θες να αλλάξεις το κείμενο προσωρινά
            // const originalTitle = element.getAttribute('aria-label');
            // element.setAttribute('aria-label', 'Αντιγράφτηκε!');

            setTimeout(() => {
                element.classList.remove('copied');
                // element.setAttribute('aria-label', originalTitle);
            }, 1200);
        };

        // 1. Δοκιμή με το σύγχρονο API (θέλει HTTPS/Localhost)
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(sku)
                .then(() => showSuccess(btn))
                .catch(err => console.error('Error:', err));
        }
        // 2. Fallback μέθοδος για HTTP / Τοπικά δίκτυα
        else {
            const textArea = document.createElement("textarea");
            textArea.value = sku;

            // Κάνουμε το textarea αόρατο
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            textArea.style.top = "0";
            document.body.appendChild(textArea);

            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showSuccess(btn);
                }
            } catch (err) {
                console.error('Fallback copy failed', err);
            }

            document.body.removeChild(textArea);
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
