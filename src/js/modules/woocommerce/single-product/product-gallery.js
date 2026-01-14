// Import Swiper & Fancybox
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

// Instances
let mainSwiper = null;
let thumbsSwiper = null;
let lens = null;
let galleryInitialized = false;
let galleryRoot = null;
let galleryObserver = null;

/* =========================
   INIT GALLERY
========================= */
function initProductGallery() {
    const mainEl = document.querySelector('.rv-gallery-main');
    const thumbsEl = document.querySelector('.rv-gallery-thumbs');

    if (!mainEl || !thumbsEl) return;

    // Αν το ίδιο DOM → μην ξανακάνεις init
    if (galleryInitialized && mainEl === galleryRoot) return;

    // Αν έχει αντικατασταθεί → καθάρισε
    destroyGallery();

    galleryInitialized = true;
    galleryRoot = mainEl;

    /* Thumbnails */
    thumbsSwiper = new Swiper(thumbsEl, {
        slidesPerView: 4,
        spaceBetween: 10,
        watchSlidesProgress: true,
        freeMode: true,
        direction: 'vertical',
        breakpoints: {
            0: {
                direction: 'horizontal',
                slidesPerView: 4,
                spaceBetween: 8
            },
            768: {
                direction: 'vertical',
                slidesPerView: 4,
                spaceBetween: 10
            }
        }
    });

    /* Main */
    mainSwiper = new Swiper(mainEl, {
        speed: 400,
        spaceBetween: 10,
        loop: true,
        watchSlidesProgress: true,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        thumbs: {
            swiper: thumbsSwiper,
            slideThumbActiveClass: 'swiper-slide-thumb-active'
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        }
    });

    initZoomLens(mainEl);
    initFancybox();
    initVariationHandlers();

    requestAnimationFrame(() => {
        mainSwiper.update();
        thumbsSwiper.update();
    });

    observeGalleryReplacement();
}

/* =========================
   DESTROY (SAFE)
========================= */
function destroyGallery() {
    if (galleryObserver) {
        galleryObserver.disconnect();
        galleryObserver = null;
    }

    if (mainSwiper) {
        mainSwiper.destroy(true, true);
        mainSwiper = null;
    }

    if (thumbsSwiper) {
        thumbsSwiper.destroy(true, true);
        thumbsSwiper = null;
    }

    if (lens && lens.parentNode) {
        lens.parentNode.removeChild(lens);
        lens = null;
    }

    galleryInitialized = false;
    galleryRoot = null;
}

/* =========================
   OBSERVE ROOT REPLACEMENT
========================= */
function observeGalleryReplacement() {
    if (!galleryRoot) return;

    galleryObserver = new MutationObserver(() => {
        const current = document.querySelector('.rv-gallery-main');

        if (current && current !== galleryRoot) {
            console.log('[Gallery] DOM replaced → re-init');
            initProductGallery();
        }
    });

    galleryObserver.observe(document.querySelector('.product') || document.body, {
        childList: true,
        subtree: true
    });
}

/* =========================
   ZOOM LENS
========================= */
function initZoomLens(container) {
    lens = document.createElement('div');
    lens.className = 'rv-zoom-lens';
    container.appendChild(lens);

    const ZOOM = 2;

    container.addEventListener('mousemove', e => {
        const img = container.querySelector('.swiper-slide-active img');
        if (!img) return;

        const rect = img.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        if (x < 0 || y < 0 || x > rect.width || y > rect.height) {
            lens.style.opacity = '0';
            return;
        }

        lens.style.opacity = '1';
        lens.style.left = `${x - lens.offsetWidth / 2}px`;
        lens.style.top = `${y - lens.offsetHeight / 2}px`;
        lens.style.backgroundImage = `url(${img.src})`;
        lens.style.backgroundSize = `${rect.width * ZOOM}px ${rect.height * ZOOM}px`;
        lens.style.backgroundPosition = `${(x / rect.width) * 100}% ${(y / rect.height) * 100}%`;
    });

    container.addEventListener('mouseleave', () => {
        lens.style.opacity = '0';
    });
}

/* =========================
   FANCYBOX
========================= */
function initFancybox() {
    const zoomBtn = document.querySelector('.rv-gallery-zoom');
    if (!zoomBtn) return;

    zoomBtn.onclick = e => {
        e.preventDefault();
        if (!mainSwiper) return;

        const images = document.querySelectorAll('.main-slide-image');

        Fancybox.show(
            [...images].map(img => ({
                src: img.dataset.src || img.src,
                type: 'image'
            })),
            {
                startIndex: mainSwiper.realIndex ?? mainSwiper.activeIndex,
                Thumbs: false,
                Toolbar: { display: ["close", "zoom", "fullscreen"] }
            }
        );
    };
}

/* =========================
   VARIATIONS (SMART)
========================= */
function initVariationHandlers() {
    const form = document.querySelector('form.variations_form');
    if (!form) return;

    if (window.yith_wccl) {
        window.yith_wccl.disable_ajax = true;
    }

    jQuery(form).off('found_variation reset_data');

    jQuery(form).on('found_variation', (_, variation) => {
        if (!variation?.image || !mainSwiper) return;

        const slide = mainSwiper.slides[0];
        const img = slide?.querySelector('img');
        if (!img) return;

        if (!img.dataset.originalSrc) {
            img.dataset.originalSrc = img.src;
            img.dataset.originalSrcset = img.srcset;
            img.dataset.originalDataSrc = img.dataset.src;
        }

        img.src = variation.image.src;
        img.srcset = variation.image.src;
        img.dataset.src = variation.image.full_src || variation.image.src;

        smartUpdate();
    });

    jQuery(form).on('reset_data', () => {
        if (!mainSwiper) return;

        mainSwiper.slides.forEach(slide => {
            const img = slide.querySelector('img');
            if (!img?.dataset.originalSrc) return;

            img.src = img.dataset.originalSrc;
            img.srcset = img.dataset.originalSrcset;
            img.dataset.src = img.dataset.originalDataSrc;
        });

        mainSwiper.slideToLoop(0);
        smartUpdate();
    });
}

/* =========================
   SMART UPDATE
========================= */
function smartUpdate() {
    requestAnimationFrame(() => {
        mainSwiper.updateSlides();
        mainSwiper.updateSize();
        mainSwiper.updateProgress();
        thumbsSwiper.update();
    });
}

/* =========================
   BOOT
========================= */
document.addEventListener('DOMContentLoaded', () => {
    if (document.body.classList.contains('single-product')) {
        initProductGallery();
    }
});

// backward compatibility
export function initProductGalleryObserver() {
    initProductGallery();
}

// debug
window.initProductGallery = initProductGallery;
