/**
 * Utility functions
 */

/**
 * Debounce function to limit how often a function can fire
 * @param {Function} func - The function to debounce
 * @param {number} wait - Time to wait in milliseconds
 * @returns {Function}
 */
export function debounce(func, wait = 100) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function to limit the rate at which a function can fire
 * @param {Function} func - The function to throttle
 * @param {number} limit - Time limit in milliseconds
 * @returns {Function}
 */
export function throttle(func, limit = 100) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

/**
 * Check if an element is in the viewport
 * @param {Element} element - The element to check
 * @param {number} offset - Optional offset in pixels
 * @returns {boolean}
 */
export function isInViewport(element, offset = 0) {
    if (!element) return false;
    
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= -offset &&
        rect.left >= -offset &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) + offset &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) + offset
    );
}

/**
 * Smooth scroll to an element
 * @param {string} selector - CSS selector of the element to scroll to
 * @param {Object} options - Scroll options
 */
export function smoothScrollTo(selector, options = {}) {
    const target = document.querySelector(selector);
    if (!target) return;
    
    const defaultOptions = {
        behavior: 'smooth',
        block: 'start',
        ...options
    };
    
    target.scrollIntoView(defaultOptions);
}

/**
 * Get URL parameters
 * @param {string} name - Parameter name
 * @returns {string|null}
 */
export function getUrlParameter(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
}

/**
 * Toggle class on scroll
 * @param {Element} element - The element to toggle class on
 * @param {string} className - The class to toggle
 * @param {number} scrollPosition - The scroll position at which to toggle the class
 */
export function toggleClassOnScroll(element, className, scrollPosition = 100) {
    if (!element) return;
    
    const toggle = () => {
        if (window.scrollY > scrollPosition) {
            element.classList.add(className);
        } else {
            element.classList.remove(className);
        }
    };
    
    window.addEventListener('scroll', toggle, { passive: true });
    toggle(); // Initial check
}

/**
 * Check if device is mobile
 * @returns {boolean}
 */
export function isMobile() {
    return window.innerWidth < 768;
}

/**
 * Check if device is touch enabled
 * @returns {boolean}
 */
export function isTouchDevice() {
    return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
}
