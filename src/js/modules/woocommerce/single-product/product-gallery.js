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

    if (galleryInitialized && mainEl === galleryRoot) return;

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
            992: {
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

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        initZoomLens(mainEl);
    }
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

    if (galleryRoot?._zoomCleanup) {
        galleryRoot._zoomCleanup();
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

    // Use .product-gallery-area (never replaced by YITH WCCL) so the observer
    // survives when YITH replaces .woocommerce-product-gallery via AJAX.
    const observeTarget = document.querySelector('.product-gallery-area')
        || document.querySelector('.product')
        || document.body;
    galleryObserver.observe(observeTarget, {
        childList: true,
        subtree: true
    });

    // YITH WCCL fires this event after replacing the gallery via AJAX.
    // Use jQuery because YITH triggers it via jQuery's event system.
    if (typeof jQuery !== 'undefined') {
        jQuery(document).off('yith_wccl_product_gallery_loaded.rv')
            .on('yith_wccl_product_gallery_loaded.rv', () => initProductGallery());
    }
}

/* =========================
   ZOOM LENS
========================= */
function initZoomLens(container) {
    lens = document.createElement('div');
    lens.className = 'rv-zoom-lens';
    container.appendChild(lens);

    const ZOOM = 2;
    let rafId;

    function onMouseMove(e) {
        cancelAnimationFrame(rafId);
        rafId = requestAnimationFrame(() => {
            const img = container.querySelector('.swiper-slide-active img');
            if (!img) return;

            const imgRect = img.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();

            const imgX = e.clientX - imgRect.left;
            const imgY = e.clientY - imgRect.top;

            if (imgX < 0 || imgY < 0 || imgX > imgRect.width || imgY > imgRect.height) {
                lens.style.opacity = '0';
                return;
            }

            const containerX = e.clientX - containerRect.left;
            const containerY = e.clientY - containerRect.top;
            const lensW = lens.offsetWidth;
            const lensH = lens.offsetHeight;

            lens.style.opacity = '1';
            lens.style.left = `${containerX - lensW / 2}px`;
            lens.style.top = `${containerY - lensH / 2}px`;
            lens.style.backgroundImage = `url(${img.dataset.src || img.src})`;
            lens.style.backgroundSize = `${imgRect.width * ZOOM}px ${imgRect.height * ZOOM}px`;
            lens.style.backgroundPosition = `-${imgX * ZOOM - lensW / 2}px -${imgY * ZOOM - lensH / 2}px`;
        });
    }

    function onMouseLeave() {
        lens.style.opacity = '0';
    }

    container.addEventListener('mousemove', onMouseMove);
    container.addEventListener('mouseleave', onMouseLeave);

    container._zoomCleanup = () => {
        container.removeEventListener('mousemove', onMouseMove);
        container.removeEventListener('mouseleave', onMouseLeave);
        cancelAnimationFrame(rafId);
        delete container._zoomCleanup;
    };
}

/* =========================
   FANCYBOX
========================= */
let _fancyboxHandler = null;

function initFancybox() {
    // Use event delegation on the stable gallery wrapper so that
    // replacing .rv-gallery-thumbs (WooCommerce variable product behaviour)
    // never breaks the button handler.
    const wrapper = document.querySelector('.woocommerce-product-gallery');
    if (!wrapper) return;

    // Remove any previous delegation to avoid duplicates
    if (_fancyboxHandler) {
        wrapper.removeEventListener('click', _fancyboxHandler);
    }

    _fancyboxHandler = (e) => {
        if (!e.target.closest('.rv-gallery-zoom')) return;
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
                Toolbar: { display: ['close', 'zoom', 'fullscreen'] }
            }
        );
    };

    wrapper.addEventListener('click', _fancyboxHandler);
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
        mainSwiper.update();
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

export function initProductGalleryObserver() {
    initProductGallery();
}