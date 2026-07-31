
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

    // Keep the real (hidden) qty input in sync on every keystroke, so
    // clicking "Add to cart" without first blurring the visible input
    // still submits the value the user actually typed.
    visibleInput.addEventListener('input', () => {
        const typed = parseInt(visibleInput.value);
        if (!isNaN(typed)) qtyInput.value = typed;
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
     * TITLE on variation change (per-variation SoftOne name, _erp_name)
     * ========================= */
    const titleEl = summary.querySelector('.rv-product-title');

    if (titleEl) {
        const originalTitle = titleEl.textContent;

        jQuery(document.body).on('found_variation.rvTitle', (e, variation) => {
            if (variation.erp_name) {
                titleEl.textContent = variation.erp_name;
            }
        });

        jQuery(document.body).on('reset_data.rvTitle', () => {
            titleEl.textContent = originalTitle;
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

    /* =========================
     * RAQ "already in quote" live check
     *
     * The product page is served from full-page cache, so the "already in
     * quote" state baked into the cached HTML only reflects whoever's
     * session generated that cache entry. Re-check the real state for the
     * current visitor after load and fix up the DOM if it's wrong.
     * ========================= */
    document.querySelectorAll('.yith-ywraq-add-to-quote').forEach((wrapper) => {
        const link       = wrapper.querySelector('.add-request-quote-button');
        const productId  = link?.dataset.product_id;
        if (!productId) return;

        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'rv_ywraq_check_exists', product_id: productId })
        })
            .then(r => r.ok ? r.json() : null)
            .then((res) => {
                if (!res?.success) return;

                // The label text itself is static (same for every visitor), but a
                // stale cached page can still have it baked in empty/wrong from
                // before the label was set. Refresh it unconditionally so it's
                // never left blank waiting on a reload.
                const responseElAlways = wrapper.querySelector(`.yith_ywraq_add_item_response-${productId}`);
                if (responseElAlways && res.data.message) {
                    responseElAlways.textContent = res.data.message;
                }

                // Scoped to this product: a page can have more than one RAQ
                // button (e.g. related/upsell products below the main one), and
                // only the main product's variations_form should drive its own
                // wrapper's variation matching.
                const variationsForm = Array.from(document.querySelectorAll('form.variations_form'))
                    .find(f => f.dataset.product_id === productId);
                if (variationsForm) {
                    // Variable product: YITH's own JS decides button-vs-message by
                    // matching the *selected* variation id against this attribute.
                    // Refresh it, then force WooCommerce to re-run that match.
                    wrapper.setAttribute('data-variation', res.data.variations || '');
                    jQuery(variationsForm).trigger('check_variations');

                    // YITH's own AJAX "add to quote" handler wipes this div's
                    // text (`.html('')`) right after a successful add, and never
                    // refills it — so switching variations afterward re-shows it
                    // empty. Patch it back in every time the message would show.
                    const restoreMessage = () => {
                        if (res.data.message && !responseElAlways?.textContent) {
                            responseElAlways.textContent = res.data.message;
                        }
                    };
                    jQuery(variationsForm).on('found_variation.rvRaqFix', restoreMessage);
                    jQuery(document).on('yith_wwraq_added_successfully.rvRaqFix', restoreMessage);
                    return;
                }

                if (!res.data.exists) return;

                const addButton = wrapper.querySelector('.yith-ywraq-add-button');
                if (addButton) {
                    addButton.classList.remove('show');
                    addButton.classList.add('hide');
                    addButton.style.display = 'none';
                }

                const responseEl = wrapper.querySelector(`.yith_ywraq_add_item_response-${productId}`);
                if (responseEl) {
                    responseEl.textContent = res.data.message;
                    responseEl.classList.remove('hide');
                    responseEl.classList.add('show');
                    responseEl.style.display = 'block';
                }

                const browseEl = wrapper.querySelector(`.yith_ywraq_add_item_browse-list-${productId}`);
                if (browseEl) {
                    browseEl.classList.remove('hide');
                    browseEl.classList.add('show');
                    browseEl.style.display = 'block';
                }
            })
            .catch(err => console.error('RAQ exists check error:', err));
    });

}
