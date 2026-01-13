// Import Swiper core and required modules
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';


function initSimpleGallery() {

    const mainEl = document.querySelector('.rv-gallery-main');
    const thumbsEl = document.querySelector('.rv-gallery-thumbs');



    try {
        console.log(' Creating Swiper instances...');

        // Initialize thumbnails first
        const thumbsSwiper = new Swiper(thumbsEl, {
            spaceBetween: 10,
            slidesPerView: 4,
            watchSlidesProgress: true,
            direction: 'vertical',
            freeMode: true,
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
            },
            on: {
                init: function() {
                    // Make thumbs visible after initialization
                    thumbsEl.style.opacity = '1';
                }
            }
        });

        // Initialize main slider with thumbs
        const mainSwiper = new Swiper(mainEl, {
            init: false,
            speed: 400,
            spaceBetween: 10,
            loop: true,
            watchSlidesProgress: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: {
                swiper: thumbsSwiper,
                slideThumbActiveClass: 'swiper-slide-thumb-active'
            },
            on: {
                init: function() {
                    console.log(' Main Swiper initialized with thumbs!');
                    console.log('Total slides:', this.slides.length);
                    
                    // Force update and redraw
                    this.update();
                    this.updateSize();
                    this.updateSlides();
                    this.updateProgress();
                    this.updateSlidesClasses();
                    
                    // Add custom class when initialized
                    mainEl.classList.add('is-initialized');
                },
            }
        });

        // Initialize main swiper
        mainSwiper.init();
        
        // Expose instances for debugging
        window.swiperInstance = mainSwiper;
        window.thumbsSwiper = thumbsSwiper;

        // Click handler for thumbnails
        const thumbSlides = thumbsEl.querySelectorAll('.swiper-slide');
        thumbSlides.forEach((thumb, index) => {
            thumb.addEventListener('click', () => {
                mainSwiper.slideTo(index);
            });
        });

        // Force update after a short delay
        setTimeout(() => {
            if (mainSwiper) {
                mainSwiper.update();
                thumbsSwiper.update();
            }
        }, 500);

    } catch (error) {
        console.error(' Error initializing gallery:', error);
    }
}

// Initialize when DOM is ready
function init() {

    // Try to initialize immediately
    initSimpleGallery();

    // Also try after a short delay in case of dynamic content
    setTimeout(initSimpleGallery, 1000);
}

// Start initialization
document.addEventListener('DOMContentLoaded', init);

// Export for backward compatibility
export function initProductGalleryObserver() {
    init();
}
