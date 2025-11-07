import Lenis from '@studio-freight/lenis';

/**
 * Initialize smooth scrolling with Lenis
 * @returns {Object} Lenis instance
 */
export function initSmoothScroll() {
    // Only initialize if window is defined (client-side)
    if (typeof window === 'undefined') return null;

    // Initialize Lenis with options
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Custom easing function
        smooth: true,
        smoothTouch: false, // Disable smooth scrolling on touch devices
        touchMultiplier: 1.5,
    });

    // Update scroll position on each frame
    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // Add data-scroll attribute to html when initialized
    document.documentElement.setAttribute('data-scroll', '');

    // Add smooth scroll class to html
    document.documentElement.classList.add('smooth-scroll');

    // Expose lenis instance globally for debugging
    window.lenis = lenis;

    console.log('Smooth scrolling initialized');
    return lenis;
}

// Auto-initialize if this is the main module
if (typeof window !== 'undefined') {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSmoothScroll);
    } else {
        initSmoothScroll();
    }
}

// Export for manual initialization
export default initSmoothScroll;
