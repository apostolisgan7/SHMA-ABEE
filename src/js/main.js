// Import Tailwind CSS FIRST to make its classes available to SCSS via @apply
import '../css/tailwind.css';

// Import SCSS
import '../scss/main.scss';

// ===== POLYFILLS & GLOBAL UTILITIES =====
// Import lodash utilities for missing dependencies
import { merge } from 'lodash-es';
import { debounce } from 'lodash-es';

// Make lodash utilities globally available for third-party scripts
window._ = { merge, debounce };

// Add lodash compatibility layer
if (typeof window.lodash !== 'undefined') {
    window.lodash.merge = merge;
    window.lodash.debounce = debounce;
}

// Add polyfills for commonly missing functions
if (typeof window.styled === 'undefined') {
    window.styled = function(tag) {
        return function() {
            return tag; // Simple polyfill that returns the HTML tag
        };
    };
}

// Global error handling for undefined properties
window.addEventListener('error', function(e) {
    if (e.message.includes('Cannot read properties of undefined')) {
        console.warn('Caught undefined property error:', e.message);
        e.preventDefault();
    }
    
    // Handle lodash conflicts
    if (e.message.includes('_.noConflict is not a function')) {
        console.warn('Lodash conflict detected, using polyfills');
        e.preventDefault();
    }
});

// ===== CORE IMPORTS =====
// Import Alpine.js
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import {shopHeader} from './modules/woocommerce/grid-view';
import './modules/woocommerce/shop-sorting';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.data('shopHeader', shopHeader);

// Import Utils
import {initSmoothScroll} from './utils/smooth-scroll';
import './utils/helpers';
import './utils/contact-form';
import {initYITHWishlist} from './utils/wishlist';

// Import UI Components
import {initStickyHeader} from './modules/ui/sticky-header';
import {initCatalogMenu} from './modules/ui/menus/catalog-menu.js';
import {initMobileMenu } from './modules/ui/menus/mobile-menu.js';
import {initMegaMenu} from './modules/ui/menus/mega-menu.js';
import {initSwipers} from './modules/ui/swipers-handler';
import {initScrollVideo} from './modules/ui/scroll-video';
import {initHistory} from './modules/ui/history-horizontal';
import {initAuthModal} from './modules/woocommerce/login-modal';
import {initLoadMoreProducts} from './modules/woocommerce/load-more.js';
import {initFilters} from './modules/woocommerce/filters.js';
import {initProductList} from './modules/woocommerce/product-list.js';
import {initProductGalleryObserver} from './modules/woocommerce/single-product/product-gallery.js';
import {initSummary} from './modules/woocommerce/single-product/summary.js';
import {initProductTabs} from './modules/woocommerce/single-product/tabs.js';
import {initVideoBox} from './modules/woocommerce/single-product/video.js';
import {initProductCatalogs} from './modules/woocommerce/single-product/product-catalogs.js';

// Import GSAP core and plugins
import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {ScrollToPlugin} from 'gsap/ScrollToPlugin';

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

// Make GSAP available globally
window.gsap = gsap;

// Add error boundary for dynamic imports
const loadModule = async (module) => {
    try {
        return await module;
    } catch (error) {
        console.error(`Failed to load module:`, error);
        return null;
    }
};

// Load GSAP with error handling
const loadGSAP = async () => {
    try {
        // GSAP is already imported and registered
        return true;
    } catch (e) {
        console.warn('Error with GSAP:', e);
        return false;
    }
};

// Import Splitting.js
import Splitting from 'splitting';

// Import WooCommerce
import {initWooCommerce} from './modules/woocommerce';
import {initOffcanvasCart} from './modules/woocommerce/offcanvas-cart';
import {initQty} from './modules/woocommerce/qty';

import SearchPopup from './modules/ui/SearchPopup';

document.addEventListener('DOMContentLoaded', () => {
    new SearchPopup();
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    console.log("%cTheme initialized", "color:#4CAF50");

    // Initialize Alpine.js
    Alpine.start();

    // Initialize Swipers and product components
    initSwipers();
    initFilters();
    initProductGalleryObserver();
    initSummary();
    initProductTabs();
    initVideoBox();
    initHistory();
    initProductCatalogs();
    // Initialize Lenis for smooth scrolling
    const lenis = initSmoothScroll();

    initYITHWishlist();

    // 2) Force a ScrollTrigger refresh AFTER Lenis settles
    setTimeout(() => {
        ScrollTrigger.refresh();
    }, 150);

    initAuthModal();

    if (typeof Splitting !== 'undefined') {
        Splitting();
    }

    // 3) Only now initialize Scroll-based animations
    loadGSAP().then(() => {

        if (typeof initStickyHeader === 'function') {
            initStickyHeader();
        }

        //  ⬇⬇ *ΜΕΤΑ το refresh* — εδώ πρέπει να μπει
        initScrollVideo();
        initLoadMoreProducts();
        initProductList();
        if (typeof initMegaMenu === 'function') {
            initMegaMenu();
        }
        if (typeof initCatalogMenu === 'function') {
            initCatalogMenu();
        }
            initMobileMenu();
    });

    if (document.body.classList.contains('woocommerce') && typeof initWooCommerce === 'function') {
        initWooCommerce();
    }
    initQty();
    if (document.getElementById('offcanvas-cart')) {
        initOffcanvasCart();
    }
});


// Export for HMR
if (import.meta.webpackHot) {
    import.meta.webpackHot.accept();
}