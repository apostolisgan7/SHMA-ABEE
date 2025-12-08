/**
 * Sticky Header Module
 * Handles the sticky header functionality
 */

export function initStickyHeader() {
    const header = document.querySelector('.site-header');
    if (!header) {
        console.error('Sticky Header: No .site-header element found');
        return;
    }
    
    // Always enable sticky header
    document.body.classList.add('has-sticky-header');

    let lastScroll = 0;
    const scrollThreshold = 100; // Pixels to scroll before showing/hiding
    
    // Add initial class
    header.classList.add('sticky-header');
    
    // Throttle scroll events for better performance
    let ticking = false;
    
    const handleScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                updateHeader();
                ticking = false;
            });
            ticking = true;
        }
    };
    
    const updateHeader = () => {
        const currentScroll = window.pageYOffset;
        const isScrolledDown = currentScroll > lastScroll;
        const isPastThreshold = currentScroll > scrollThreshold;
        
        // Show/hide header based on scroll direction
        if (isScrolledDown && isPastThreshold) {
            header.classList.add('header-hidden');
            header.classList.remove('header-visible');
        } else {
            header.classList.remove('header-hidden');
            header.classList.add('header-visible');
            
            // Add shadow when scrolled
            if (currentScroll > 10) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
        }
        
        lastScroll = currentScroll <= 0 ? 0 : currentScroll;
    };
    
    // Add scroll event listener
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Initial check
    updateHeader();

    // Cleanup function (for potential HMR or dynamic removal)
    return () => {
        window.removeEventListener('scroll', handleScroll);
    };
}
