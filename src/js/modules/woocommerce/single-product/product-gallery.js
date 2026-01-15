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
let pendingVariationImage = null;


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

    // Apply variation image AFTER gallery rebuild
    if (pendingVariationImage) {
        requestAnimationFrame(() => {
            const img = document.querySelector(
                '.rv-gallery-main .swiper-slide-active img'
            );

            if (!img) return;

            if (!img.dataset.originalSrc) {
                img.dataset.originalSrc = img.src;
                img.dataset.originalSrcset = img.srcset;
                img.dataset.originalDataSrc = img.dataset.src;
            }

            img.src = pendingVariationImage.src;
            img.srcset = pendingVariationImage.src;
            img.dataset.src =
                pendingVariationImage.full_src || pendingVariationImage.src;

            img.decode?.().catch(() => {});
            smartUpdate();
        });
    }

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

    jQuery(document).off('found_variation.rv reset_data.rv');

    // 👉 ΟΤΑΝ ΒΡΕΘΕΙ VARIATION
    jQuery(document).on(
        'found_variation.rv',
        'form.variations_form',
        function (_, variation) {

            if (!variation?.image?.src) return;

            pendingVariationImage = variation.image;

        }
    );

    // 👉 RESET
    jQuery(document).on(
        'reset_data.rv',
        'form.variations_form',
        function () {
            pendingVariationImage = null;
        }
    );
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
