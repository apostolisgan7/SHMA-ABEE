// src/modules/ui/menus/catalog-menu.js
import { gsap } from "gsap";

export function initCatalogMenu() {
    const btn = document.querySelector('[data-catalog-toggle]');
    const panel = document.querySelector('[data-catalog-panel]');
    const overlay = document.querySelector('[data-catalog-overlay]');
    const close = document.querySelector('[data-catalog-close]');

    if (!btn || !panel || !overlay) return;

    const tl = gsap.timeline({ paused: true });

    tl.set([panel, overlay], { pointerEvents: 'auto' });
    tl.to(overlay, { duration: 0.2, opacity: 1 }, 0);
    tl.fromTo(panel,
        { y: -20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.25, ease: "power2.out" },
        0.05
    );

    const open = () => {
        tl.play(0);
        document.documentElement.classList.add('is-catalog-open');
    };

    const closeMenu = () => {
        gsap.to(panel, { opacity: 0, y: -20, duration: 0.2 });
        gsap.to(overlay, {
            opacity: 0,
            duration: 0.2,
            onComplete: () => {
                panel.style.pointerEvents = 'none';
                overlay.style.pointerEvents = 'none';
                document.documentElement.classList.remove('is-catalog-open');
            }
        });
    };

    btn.addEventListener('click', open);
    overlay.addEventListener('click', closeMenu);
    if (close) close.addEventListener('click', closeMenu);

    // Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.documentElement.classList.contains('is-catalog-open')) {
            closeMenu();
        }
    });
}
