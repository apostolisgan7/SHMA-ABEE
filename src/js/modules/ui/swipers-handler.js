import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

function initScopedSwiper(rootEl, options = {}) {
    if (!rootEl || rootEl.dataset.swiperInited === '1') return null;

    const scope     = rootEl.closest('[class*="rv-home-"]') || rootEl;
    const navNext   = scope.querySelector('[class*="__nav--next"]');
    const navPrev   = scope.querySelector('[class*="__nav--prev"]');
    const pagEl     = rootEl.querySelector('.swiper-pagination');

    // Φτιάχνουμε modules/params δυναμικά
    const modules   = [];
    const params    = { ...options };

    if (navNext && navPrev) {
        modules.push(Navigation);
        params.navigation = { nextEl: navNext, prevEl: navPrev };
    } else {
        // Προαιρετικά, για ησυχία:
        params.navigation = { enabled: false };
    }

    if (pagEl) {
        modules.push(Pagination);
        params.pagination = { el: pagEl, clickable: true };
    } else {
        params.pagination = { enabled: false };
    }

    params.modules = modules;

    const swiper = new Swiper(rootEl, params);
    rootEl.dataset.swiperInited = '1';
    return swiper;
}

function initSwipers() {
    document.querySelectorAll('.rv-home-services__carousel').forEach((el) => {
        initScopedSwiper(el, {
            slidesPerView: 1.1,
            spaceBetween: 16,
            speed: 500,
            loop: false,
            breakpoints: {
                640:  { slidesPerView: 1.2, spaceBetween: 13 },
                768:  { slidesPerView: 2,   spaceBetween: 13 },
                1024: { slidesPerView: 3,   spaceBetween: 13 }
            }
        });
    });

    document.querySelectorAll('.rv-home-products__carousel').forEach((el) => {
        initScopedSwiper(el, {
            slidesPerView: 1.1,
            spaceBetween: 16,
            speed: 500,
            loop: false,
            breakpoints: {
                640:  { slidesPerView: 1.6, spaceBetween: 20 },
                880:  { slidesPerView: 2.4, spaceBetween: 24 },
                1200: { slidesPerView: 3.2, spaceBetween: 24 },
                1440: { slidesPerView: 4,   spaceBetween: 24 }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initSwipers);
export { initSwipers };
