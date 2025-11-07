/**
 * Mobile Menu Module
 * Simple and reliable mobile menu implementation
 */
export function initMobileMenu() {
    console.log('Initializing mobile menu...');
    
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const menuOverlay = document.getElementById('mobile-menu-overlay');
    const menuContainer = document.getElementById('mobile-menu');
    
    // Check for required elements
    if (!menuToggle || !menuOverlay || !menuContainer) {
        console.warn('Mobile menu elements not found:', { menuToggle, menuOverlay, menuContainer });
        return;
    }
    
    console.log('Mobile menu elements found, setting up...');
    
    let isOpen = false;
    
    // Simple toggle function
    function toggleMenu(forceState) {
        isOpen = typeof forceState === 'boolean' ? forceState : !isOpen;
        
        console.log('Toggling menu:', isOpen ? 'open' : 'close');
        
        if (isOpen) {
            // Open menu
            menuOverlay.style.display = 'block';
            document.body.classList.add('menu-open');
            menuToggle.classList.add('active');
            
            // Force reflow/repaint
            void menuOverlay.offsetHeight;
            
            // Remove the invisible class and add active
            menuOverlay.classList.remove('invisible');
            menuOverlay.classList.add('active');
            
        } else {
            // Close menu
            menuOverlay.classList.remove('active');
            document.body.classList.remove('menu-open');
            menuToggle.classList.remove('active');
            
            // Wait for animation to finish before adding invisible class
            setTimeout(() => {
                if (!menuOverlay.classList.contains('active')) {
                    menuOverlay.style.display = 'none';
                    menuOverlay.classList.add('invisible');
                }
            }, 300);
        }
    }
    
    // Click handler for menu toggle
    function handleMenuClick(e) {
        e.preventDefault();
        console.log('Menu button clicked');
        toggleMenu();
    }
    
    // Click handler for overlay (close when clicking outside menu)
    function handleOverlayClick(e) {
        if (e.target === menuOverlay) {
            console.log('Overlay clicked, closing menu');
            toggleMenu(false);
        }
    }
    
    // Close menu when clicking on a link
    function handleLinkClick() {
        console.log('Menu link clicked, closing menu');
        toggleMenu(false);
    }
    
    // Close menu with Escape key
    function handleKeyDown(e) {
        if (e.key === 'Escape' && isOpen) {
            console.log('Escape key pressed, closing menu');
            toggleMenu(false);
        }
    }
    
    // Add event listeners
    console.log('Adding event listeners...');
    menuToggle.addEventListener('click', handleMenuClick);
    menuOverlay.addEventListener('click', handleOverlayClick);
    document.addEventListener('keydown', handleKeyDown);
    
    // Add click listeners to all menu links
    const menuLinks = menuContainer.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', handleLinkClick);
    });
    
    console.log('Mobile menu initialization complete');
    
    // Return cleanup function
    return function cleanup() {
        console.log('Cleaning up mobile menu...');
        menuToggle.removeEventListener('click', handleMenuClick);
        menuOverlay.removeEventListener('click', handleOverlayClick);
        document.removeEventListener('keydown', handleKeyDown);
        menuLinks.forEach(link => {
            link.removeEventListener('click', handleLinkClick);
        });
    };
}
