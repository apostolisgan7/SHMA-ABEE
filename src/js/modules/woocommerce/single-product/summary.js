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
            console.log('Open video tutorial');
        });
    }
}
