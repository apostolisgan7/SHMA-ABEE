// Import Tailwind CSS FIRST to make its classes available to SCSS via @apply
import '../css/tailwind.css';

// Import SCSS
import '../scss/main.scss';

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


// Import UI Components
import {initStickyHeader} from './modules/ui/sticky-header';
import {initCatalogMenu} from './modules/ui/menus/catalog-menu.js';
import {initMobileMenu } from './modules/ui/menus/mobile-menu.js';
import {initMegaMenu} from './modules/ui/menus/mega-menu.js';
import {initSwipers} from './modules/ui/swipers-handler';
import {initScrollVideo} from './modules/ui/scroll-video';
import {initHeroVideo} from './modules/ui/hero-video';
import {initHistory} from './modules/ui/history-horizontal';
import {initAnimations, initFooterAnimation, initHeaderAnimation} from './modules/ui/animations';
import {initAuthModal} from './modules/woocommerce/login-modal';
import {initBackToTop} from './modules/ui/back-to-top.js';

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
import {initYithQty} from './utils/yith-qty';

import SearchPopup from './modules/ui/SearchPopup';

document.addEventListener('DOMContentLoaded', () => {
    new SearchPopup();
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    console.log("%cΣΗΜΑ ΑΒΕΕ", "color:#4CAF50");

    // Initialize Alpine.js
    Alpine.start();

    // Initialize Swipers and product components
    initSwipers();
    initBackToTop();
    // Initialize Lenis for smooth scrolling
    const lenis = initSmoothScroll();


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
        initHeroVideo();
        initAnimations();
        initHistory();
        initFooterAnimation();
        initHeaderAnimation();
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
    if (document.getElementById('yith-ywraq-form')) {
        initYithQty();
    }
});


// Export for HMR
if (import.meta.webpackHot) {
    import.meta.webpackHot.accept();
}