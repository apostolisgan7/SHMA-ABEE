import Lenis from '@studio-freight/lenis';
import { ScrollTrigger } from "gsap/ScrollTrigger";
import gsap from "gsap";

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

// Global reference to Lenis instance
let lenisInstance = null;

// Debounce helper function
const debounce = (func, wait) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
};

// Handle dynamic content changes
const handleContentChange = (lenis) => {
    // Recalculate scroll boundaries
    lenis.emit('resize');
    
    // Update ScrollTrigger
    ScrollTrigger.refresh();
    
    // Force update Lenis
    lenis.resize();
};

// Initialize smooth scrolling
export function initSmoothScroll() {
    // Skip if running on server-side
    if (typeof window === "undefined") return null;

    // Return existing instance if already initialized
    if (window.__lenis__) {
        console.warn("[Lenis] Using existing instance");
        return window.__lenis__;
    }

    // Configure Lenis with optimal settings
    const lenis = new Lenis({
        duration: 1.1,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Improved easing
        smooth: true,
        smoothTouch: false,
        infinite: false, // Prevent infinite scroll issues
        direction: 'vertical',
        gestureDirection: 'vertical',
        smoothWheel: true,
        wheelMultiplier: 1.1,
        touchMultiplier: 2,
    });

    // Store instance globally
    window.__lenis__ = lenis;
    lenisInstance = lenis;

    // GSAP integration
    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });
    
    // Improve performance
    gsap.ticker.lagSmoothing(0);
    gsap.ticker.fps(60);

    // Handle scroll events
    lenis.on('scroll', ({ scroll, limit, velocity, direction, progress }) => {
        // Update ScrollTrigger on scroll
        ScrollTrigger.update();
        
        // Handle near-bottom scroll
        const isNearBottom = scroll + window.innerHeight >= limit - 10;
        if (isNearBottom) {
            document.documentElement.classList.add('at-bottom');
        } else {
            document.documentElement.classList.remove('at-bottom');
        }
    });

    // Handle resize events with debounce
    const handleResize = debounce(() => {
        lenis.resize();
        ScrollTrigger.refresh();
    }, 100);

    // Handle dynamic content changes
    const observer = new MutationObserver(debounce(() => {
        handleContentChange(lenis);
    }, 150));

    // Start observing the document with the configured parameters
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        characterData: true
    });

    // Add event listeners
    window.addEventListener('resize', handleResize, { passive: true });
    
    // Handle page transitions or AJAX loads
    document.addEventListener('DOMContentLoaded', () => {
        // Initial refresh after everything is loaded
        setTimeout(() => {
            lenis.resize();
            ScrollTrigger.refresh();
        }, 500);
    });

    // Add CSS classes for styling
    document.documentElement.classList.add("smooth-scroll");
    document.documentElement.setAttribute("data-smooth-scroll", "enabled");

    // Initial refresh
    requestAnimationFrame(() => {
        lenis.resize();
        ScrollTrigger.refresh();
    });

    return lenis;
}

// Export utility functions
export const refreshSmoothScroll = () => {
    if (lenisInstance) {
        lenisInstance.resize();
        ScrollTrigger.refresh();
    }
};

export const scrollTo = (target, options = {}) => {
    if (!lenisInstance) return;
    
    const defaultOptions = {
        offset: 0,
        immediate: false,
        duration: 1.1,
        ...options
    };
    
    lenisInstance.scrollTo(target, defaultOptions);
};

export default initSmoothScroll;
