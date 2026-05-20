import { initProductGalleryObserver } from './modules/woocommerce/single-product/product-gallery.js';
import { initSummary }                from './modules/woocommerce/single-product/summary.js';
import { initProductTabs }            from './modules/woocommerce/single-product/tabs.js';
import { initVideoBox }               from './modules/woocommerce/single-product/video.js';
import { initProductCatalogs }        from './modules/woocommerce/single-product/product-catalogs.js';
import { initMobileStickyPanel }      from './modules/woocommerce/single-product/mobile-sticky-panel.js';

document.addEventListener('DOMContentLoaded', () => {
    initProductGalleryObserver();
    initSummary();
    initProductTabs();
    initVideoBox();
    initProductCatalogs();
    initMobileStickyPanel();
});
