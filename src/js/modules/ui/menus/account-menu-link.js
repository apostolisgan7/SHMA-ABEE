/**
 * Wire up a WP menu item marked with the "js-account-menu-link" CSS class
 * (added via Appearance → Menus → Screen Options → CSS Classes).
 *
 * Logged in  → closes whichever menu (mega / mobile) is open, then follows
 *              the link's normal URL (should point to My Account).
 * Logged out → closes the menu, then opens the sigma-auth-modal instead of
 *              navigating.
 *
 * Runs as a capture-phase listener so it intercepts the click before the
 * mega-menu link's default navigation or the mobile-menu's own per-item
 * click handler (which would otherwise navigate immediately).
 */
export function initAccountMenuLink() {
    document.addEventListener('click', (e) => {
        const target = e.target.closest('.js-account-menu-link');
        if (!target) return;

        const linkEl = target.matches('a, button') ? target : target.querySelector('a, button');
        const url = linkEl ? (linkEl.tagName === 'A' ? linkEl.href : linkEl.dataset.link) : null;

        e.preventDefault();
        e.stopImmediatePropagation();

        const isLoggedIn = document.body.classList.contains('logged-in');

        let done = false;
        const proceed = () => {
            if (done) return;
            done = true;
            if (isLoggedIn) {
                if (url) window.location.href = url;
            } else {
                document.querySelector('.js-auth-modal-trigger')?.click();
            }
        };

        // Small gap after the close finishes before the next thing opens —
        // back-to-back with no pause reads as "both popups at once".
        const proceedAfterPause = () => setTimeout(proceed, 150);

        const megaMenu   = document.getElementById('megaMenu');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (megaMenu?.classList.contains('is-open') && window.__ruinedCloseMegaMenu) {
            window.__ruinedCloseMegaMenu(proceedAfterPause);
        } else if (mobileMenu?.classList.contains('is-open') && window.__ruinedCloseMobileMenu) {
            window.__ruinedCloseMobileMenu(proceedAfterPause);
        } else {
            proceed();
            return;
        }

        // Safety net: if the close animation's callback never fires for any
        // reason, don't leave the user stuck with a dead click.
        setTimeout(proceed, 1500);
    }, true);
}
