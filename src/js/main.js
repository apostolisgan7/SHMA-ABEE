// Import Tailwind CSS FIRST to make its classes available to SCSS via @apply
import '../css/tailwind.css';

// Import SCSS
import '../scss/main.scss';

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Import Utils
import { initSmoothScroll } from './utils/smooth-scroll';
import './utils/helpers';

// Import UI Components
import { initStickyHeader } from './modules/ui/sticky-header';
import { initThemeToggle } from './modules/ui/theme-toggle';
import { initMobileMenu } from './modules/ui/mobile-menu';

// Import GSAP core and plugins
let gsap;
let ScrollTrigger;
let ScrollToPlugin;
// Load GSAP with error handling
const loadGSAP = async () => {
    try {
        // Use dynamic imports to handle potential loading issues
        const gsapModule = await import('gsap');
        gsap = gsapModule.default || gsapModule;
        
        const scrollTriggerModule = await import('gsap/ScrollTrigger');
        ScrollTrigger = scrollTriggerModule.default || scrollTriggerModule;
        
        const scrollToPluginModule = await import('gsap/ScrollToPlugin');
        ScrollToPlugin = scrollToPluginModule.default || scrollToPluginModule;
        
        if (gsap && ScrollTrigger && ScrollToPlugin) {
            gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
            window.gsap = gsap; // Make available globally
            return true;
        }
    } catch (e) {
        console.warn('Error loading GSAP:', e);
    }
    return false;
};

// Import Splitting.js
import Splitting from 'splitting';

// Import WooCommerce
import { initWooCommerce } from './modules/woocommerce';
import { initOffcanvasCart } from './modules/woocommerce/offcanvas-cart';

import SearchPopup from './modules/ui/SearchPopup';

document.addEventListener('DOMContentLoaded', () => {
    new SearchPopup();
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    console.log('Theme initialized');
    
    // Initialize Alpine.js
    Alpine.start();
    
    // Initialize theme toggle (runs immediately)
    if (typeof initThemeToggle === 'function') {
        initThemeToggle();
    }
    
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
        
        // Initialize mobile menu with GSAP fallback
        if (typeof initMobileMenu === 'function') {
            initMobileMenu();
        }
    }).catch((error) => {
        console.error('Error loading GSAP:', error);
        // If GSAP fails, try initializing without it
        if (typeof initMobileMenu === 'function') {
            // Otherwise, wait for GSAP to be ready
            setTimeout(() => {
                initMobileMenu();
            }, 50);
        }
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