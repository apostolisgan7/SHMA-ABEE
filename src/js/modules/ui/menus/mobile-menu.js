import { gsap } from 'gsap';

/**
 * Sets --vh-real CSS custom property based on window.innerHeight.
 * window.innerHeight is always the VISIBLE viewport (excludes browser chrome),
 * unlike CSS vh which on Chrome uses the large viewport.
 *
 * Also sets --real-vh for use as: height: calc(var(--real-vh) * 100)
 * if you ever need a full-height value in CSS.
 */
function setViewportHeight() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh-real', `${vh}px`);
    document.documentElement.style.setProperty('--real-vh', `${window.innerHeight}px`);
}

// Set on load
setViewportHeight();

// Update on resize (orientation change, browser chrome show/hide)
// Debounced to avoid excessive repaints during resize
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(setViewportHeight, 100);
});

// Also update on visualViewport resize (more reliable on mobile)
if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(setViewportHeight, 100);
    });
}


export function initMobileMenu() {
    const menu = document.querySelector('.mobile-menu');
    const backdrop = document.querySelector('.mobile-menu-backdrop');
    const openBtn = document.querySelector('.mobile-menu-button');
    const closeBtn = menu?.querySelector('.mobile-menu__close');
    const backBtn = menu?.querySelector('.mobile-menu__back');
    const track = menu?.querySelector('.mobile-menu__track');
    const titleEl = menu?.querySelector('.mobile-menu__title');

    const accountBtn = document.getElementById('account_button_mobile');
    const searchBtn  = document.getElementById('search_button_mobile');

    if (!menu || !openBtn || !track || !titleEl || !backBtn) return;

    let isAnimating = false;
    let menuTl = null;

    let panelHistory = [0];
    let titleHistory = ['Menu'];

    /* -------------------------
       UI
    ------------------------- */
    const updateUI = () => {
        titleEl.textContent = titleHistory[titleHistory.length - 1];

        if (panelHistory.length <= 1) {
            backBtn.classList.remove('is-visible');
            backBtn.setAttribute('aria-hidden', 'true');
        } else {
            backBtn.classList.add('is-visible');
            backBtn.setAttribute('aria-hidden', 'false');
        }
    };

    const getMenuElements = () => {
        return menu.querySelectorAll(`
            .menu-item,
            .mobile-menu__title,
            .mobile-menu__close,
            .bottom-nav
        `);
    };

    /* -------------------------
       OPEN
    ------------------------- */
    const openMenu = () => {
        panelHistory = [0];
        titleHistory = ['Menu'];
        updateUI();

        // Refresh viewport height at moment of open
        // (handles case where browser chrome state has changed since page load)
        setViewportHeight();

        openBtn.classList.add('is-active');
        menu.classList.add('is-open');
        menu.setAttribute('aria-hidden', 'false');
        window.__lenis__?.stop();
        document.documentElement.classList.add('scroll-locked');

        const elements = getMenuElements();
        gsap.set(elements, { y: 8, autoAlpha: 0 });

        menuTl?.kill();
        menuTl = gsap.timeline()
            .to(backdrop, {
                opacity: 1,
                duration: 0.25,
                pointerEvents: 'auto'
            }, 0)
            .to(menu, {
                x: 0,
                duration: 0.5,
                ease: 'expo.out'
            }, 0)
            .to(elements, {
                y: 0,
                autoAlpha: 1,
                duration: 0.35,
                ease: 'power2.out',
                stagger: 0.035,
                onComplete: () => closeBtn?.focus()
            }, 0.1);
    };

    /* -------------------------
       CLOSE
    ------------------------- */
    const closeMenu = (onDone) => {
        openBtn.classList.remove('is-active');
        menu.classList.remove('is-open');
        menu.setAttribute('aria-hidden', 'true');

        const elements = getMenuElements();

        menuTl?.kill();
        menuTl = gsap.timeline({
            onComplete: () => {
                window.__lenis__?.start();
                document.documentElement.classList.remove('scroll-locked');
                resetMenu();
                if (onDone) {
                    onDone();
                } else {
                    openBtn.focus();
                }
            }
        })
            .to(elements, {
                y: 6,
                autoAlpha: 0,
                duration: 0.25,
                ease: 'power2.in',
                stagger: 0.02
            }, 0)
            .to(menu, {
                x: '-120%',
                duration: 0.45,
                ease: 'expo.in'
            }, 0)
            .to(backdrop, {
                opacity: 0,
                duration: 0.25,
                pointerEvents: 'none'
            }, '<0.1');
    };

    const resetMenu = () => {
        panelHistory = [0];
        titleHistory = ['Menu'];
        updateUI();
        gsap.set(track, { x: 0 });
    };

    /* -------------------------
       EVENTS
    ------------------------- */
    openBtn.addEventListener('click', openMenu);
    closeBtn?.addEventListener('click', () => closeMenu());
    backdrop?.addEventListener('click', () => closeMenu());

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.classList.contains('is-open')) closeMenu();
    });

    /* -------------------------
       MENU ITEMS
    ------------------------- */
    menu.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', () => {

            // Simple link — navigate after close animation
            const link = item.dataset.link;
            if (link) {
                closeMenu(() => { window.location.href = link; });
                return;
            }

            if (!item.classList.contains('has-children')) return;
            if (isAnimating) return;

            isAnimating = true;

            const id = item.dataset.id;
            const title = item.dataset.title || 'Menu';

            const panels = [...menu.querySelectorAll('.mobile-menu__panel')];
            const index = panels.findIndex(p => p.dataset.parent === id);

            if (index === -1) {
                isAnimating = false;
                return;
            }

            panelHistory.push(index);
            titleHistory.push(title);
            updateUI();

            gsap.to(track, {
                x: `-${index * 100}%`,
                duration: 0.45,
                ease: 'power4.out',
                onComplete: () => {
                    isAnimating = false;
                }
            });
        });
    });

    /* -------------------------
       BACK
    ------------------------- */
    backBtn.addEventListener('click', () => {
        if (panelHistory.length <= 1 || isAnimating) return;
        isAnimating = true;

        panelHistory.pop();
        titleHistory.pop();

        const targetIndex = panelHistory[panelHistory.length - 1];
        updateUI();

        gsap.to(track, {
            x: `-${targetIndex * 100}%`,
            duration: 0.45,
            ease: 'power4.out',
            onComplete: () => {
                isAnimating = false;
            }
        });
    });

    /* -------------------------
       BOTTOM NAV ACTIONS
    ------------------------- */
    const closeMobileMenu = (onDone) => {
        if (!menu.classList.contains('is-open')) {
            onDone?.();
            return;
        }
        closeMenu(onDone);
    };

    if (accountBtn) {
        accountBtn.addEventListener('click', () => {
            closeMobileMenu(() => {
                document.querySelector('.js-auth-modal-trigger')?.click();
            });
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            closeMobileMenu(() => {
                document.querySelector('.search-toggle')?.click();
            });
        });
    }
}