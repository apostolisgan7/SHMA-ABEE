import { gsap } from 'gsap';

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

    // history κρατάει PANEL INDEXES
    let history = [0];
    let titleHistory = ['Menu'];

    /* -------------------------
       UI
    ------------------------- */
    const updateUI = () => {
        titleEl.textContent = titleHistory[titleHistory.length - 1];

        if (history.length <= 1) {
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
        history = [0];
        titleHistory = ['Menu'];
        updateUI();

        openBtn.classList.add('is-active');
        menu.classList.add('is-open');

        const elements = getMenuElements();
        gsap.set(elements, { y: 8, autoAlpha: 0 });

        gsap.timeline()
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
                stagger: 0.035
            }, 0.1);
    };

    /* -------------------------
       CLOSE
    ------------------------- */
    const closeMenu = () => {
        openBtn.classList.remove('is-active');
        menu.classList.remove('is-open');

        const elements = getMenuElements();

        gsap.timeline({
            onComplete: resetMenu
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
        history = [0];
        titleHistory = ['Menu'];
        updateUI();
        gsap.set(track, { x: 0 });
    };

    /* -------------------------
       EVENTS
    ------------------------- */
    openBtn.addEventListener('click', openMenu);
    closeBtn?.addEventListener('click', closeMenu);
    backdrop?.addEventListener('click', closeMenu);

    /* -------------------------
       MENU ITEMS
    ------------------------- */
    menu.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', () => {

            // ✅ Απλό link
            const link = item.dataset.link;
            if (link) {
                closeMenu();
                setTimeout(() => {
                    window.location.href = link;
                }, 300);
                return;
            }

            // ❌ δεν έχει children
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

            history.push(index);
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
        if (history.length <= 1 || isAnimating) return;
        isAnimating = true;

        history.pop();
        titleHistory.pop();

        const targetIndex = history[history.length - 1];
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
    const closeMobileMenu = () => {
        if (!menu.classList.contains('is-open')) return false;
        closeBtn?.click();
        return true;
    };

    if (accountBtn) {
        accountBtn.addEventListener('click', () => {
            const didClose = closeMobileMenu();
            setTimeout(() => {
                document.querySelector('.js-auth-modal-trigger')?.click();
            }, didClose ? 500 : 0);
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            const didClose = closeMobileMenu();
            setTimeout(() => {
                document.querySelector('.search-toggle')?.click();
            }, didClose ? 500 : 0);
        });
    }
}
