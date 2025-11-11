/**
 * Mobile Menu Module
 */

export function initMobileMenu() {
    const menuButton = document.querySelector('.mobile-menu-button');
    const body = document.body;

    if (!menuButton) return;

    menuButton.addEventListener('click', () => {
        menuButton.classList.toggle('active');
        body.classList.toggle('menu-open');
    });
}
