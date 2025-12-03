// Import Tailwind CSS FIRST to make its classes available to SCSS via @apply
import '../css/tailwind.css';

// Import SCSS
import '../scss/main.scss';

// Import Alpine.js
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Import Utils
import {initSmoothScroll} from './utils/smooth-scroll';
import './utils/helpers';

// Import UI Components
import {initStickyHeader} from './modules/ui/sticky-header';
import {initCatalogMenu} from './modules/ui/menus/catalog-menu.js';
import {initMegaMenu} from './modules/ui/menus/mega-menu.js';
import {initSwipers} from './modules/ui/swipers-handler';
import {initScrollVideo} from './modules/ui/scroll-video';

// Import GSAP core and plugins
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ScrollToPlugin } from 'gsap/ScrollToPlugin';

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

import SearchPopup from './modules/ui/SearchPopup';

document.addEventListener('DOMContentLoaded', () => {
    new SearchPopup();
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    console.log('Theme initialized');

    // Initialize Alpine.js
    Alpine.start();

    // Initialize Swiper carousels
    initSwipers();

    // Initialize smooth scrolling
    initSmoothScroll();

    // Initialize text splitting
    if (typeof Splitting !== 'undefined') {
        Splitting();
    }

    // Load GSAP
    loadGSAP().then(() => {
        // Initialize components that depend on GSAP
        if (typeof initStickyHeader === 'function') {
            initStickyHeader();
        }
        initScrollVideo();


        if (typeof initMegaMenu === 'function') {
            initMegaMenu();
        }
        if (typeof initCatalogMenu === 'function') {
            initCatalogMenu();
        }
    }).catch((error) => {
        console.error('Error loading GSAP:', error);
    });

    // Initialize WooCommerce if on WooCommerce pages
    if (document.body.classList.contains('woocommerce') && typeof initWooCommerce === 'function') {
        initWooCommerce();
    }
    // Initialize Off-Canvas Cart if elements exist
    if (document.getElementById('offcanvas-cart')) {
        initOffcanvasCart();
    }
});

// Export for HMR
if (import.meta.webpackHot) {
    import.meta.webpackHot.accept();
}