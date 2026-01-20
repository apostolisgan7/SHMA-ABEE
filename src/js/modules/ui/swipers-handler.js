import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

function initSwipers() {
    // --------------------------
    // HOME SERVICES SWIPER
    // --------------------------
    document.querySelectorAll(".rv-home-services__carousel").forEach((carousel) => {
        new Swiper(carousel, {
            modules: [Navigation, Pagination],
            slidesPerView: 1.3,
            spaceBetween: 16,
            speed: 500,
            navigation: {
                nextEl: carousel.querySelector(".rv-hp__nav--next"),
                prevEl: carousel.querySelector(".rv-hp__nav--prev"),
            },
            pagination: {
                el: carousel.querySelector(".swiper-pagination"),
                clickable: true,
            },
            breakpoints: {
                640:  { slidesPerView: 1.2 },
                768:  { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            }
        });
    });

    // --------------------------
    // GRID SERVICES SWIPER (Mobile only)
    // --------------------------
    const initGridServicesCarousel = () => {
        // Only initialize on mobile
        if (window.innerWidth >= 992) return;
        
        document.querySelectorAll(".grid-services__carousel:not(.swiper-initialized)").forEach((carousel) => {
            new Swiper(carousel, {
                modules: [Pagination],
                slidesPerView: 1.3,
                spaceBetween: 16,
                speed: 500,
                pagination: {
                    el: carousel.querySelector(".swiper-pagination"),
                    clickable: true,
                },
                breakpoints: {
                    0:   { slidesPerView: 1.3 },
                    480: { slidesPerView: 1.5 },
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 2.5 }
                }
            });
        });
    };
    
    // Initialize on load
    initGridServicesCarousel();
    
    // Re-initialize on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            initGridServicesCarousel();
        }, 250);
    });

    // --------------------------
    // HOME PRODUCTS SWIPER
    // --------------------------
    document.querySelectorAll(".rv-home-products__carousel").forEach((carousel) => {
        const wrapper = carousel.closest('.rv-home-products');
        const nextBtn = wrapper.querySelector(".rv-hp__nav--next");
        const prevBtn = wrapper.querySelector(".rv-hp__nav--prev");
        const pag = carousel.querySelector(".swiper-pagination");

        new Swiper(carousel, {
            modules: [Navigation, Pagination],
            slidesPerView: 1.3,
            spaceBetween: 16,
            speed: 500,
            navigation: {
                nextEl: nextBtn,
                prevEl: prevBtn,
            },
            pagination: {
                el: pag,
                type: 'progressbar',
            },
            breakpoints: {
                640:  { slidesPerView: 1.6 },
                880:  { slidesPerView: 2.4 },
                1200: { slidesPerView: 3.2 },
                1440: { slidesPerView: 4 },
            }
        });
    });

// --------------------------
    // RELATED PRODUCTS SWIPER
    // --------------------------
    document.querySelectorAll(".rv-related-products__carousel").forEach((carousel) => {
        const wrapper = carousel.closest('.rv-related-products');

        // Σιγουρευόμαστε ότι τα buttons υπάρχουν στο σωστό wrapper
        const nextBtn = wrapper ? wrapper.querySelector(".rv-rp__nav--next") : null;
        const prevBtn = wrapper ? wrapper.querySelector(".rv-rp__nav--prev") : null;
        const pag = carousel.querySelector(".swiper-pagination");

        new Swiper(carousel, {
            modules: [Navigation, Pagination],
            slidesPerView: 1.3,
            spaceBetween: 16,
            speed: 500,
            grabCursor: true,
            watchSlidesProgress: true,
            navigation: {
                nextEl: nextBtn,
                prevEl: prevBtn,
            },
            pagination: {
                el: pag,
                type: 'progressbar',
            },
            breakpoints: {
                640:  { slidesPerView: 1.6 },
                880:  { slidesPerView: 2.4 },
                1200: { slidesPerView: 3.2 },
                1440: { slidesPerView: 4 },
            }
        });
    });
}

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", initSwipers);

// Export the function for manual initialization
export { initSwipers };
