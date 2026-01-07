import gsap from 'gsap';

export function initProductList() {
    const header = document.querySelector('.archive-header');
    const sidebar = document.querySelector('.shop_sidebar');
    const products = document.querySelector('.products');
    const loadMore = document.querySelector('.rv-load-more-wrap');

    // 🔒 run once
    if (document.body.dataset.archiveAnimated === 'true') return;
    document.body.dataset.archiveAnimated = 'true';

    const tl = gsap.timeline({
        defaults: {
            duration: 1,
            ease: 'power3.out',
        }
    });

    // --- HEADER ---
    if (header) {
        const left = header.querySelector('.archive-header__left');
        const center = header.querySelector('.archive-header__center');
        const right = header.querySelector('.archive-header__right');

        tl.from([left, center, right], {
            autoAlpha: 0,
            y: 20,
            stagger: 0.08,
        }, 0);
    }

    // --- SIDEBAR ---
    if (sidebar) {
        tl.from(sidebar, {
            autoAlpha: 0,
            y: 30,
        }, 0.15);
    }

    // --- PRODUCTS GRID (container only, όχι cards) ---
    if (products) {
        tl.from(products, {
            autoAlpha: 0,
            y: 20,
        }, 0.2);
    }

    // --- LOAD MORE ---
    if (loadMore) {
        tl.from(loadMore, {
            autoAlpha: 0,
            y: 16,
        }, '+=0.2');
    }
}
