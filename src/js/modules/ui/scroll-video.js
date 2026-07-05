import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

// Touch devices (iOS/Android) don't render background-attachment:fixed reliably,
// so the fixed "parallax" bg used on desktop is replaced here with a real
// scroll-scrubbed transform on the media layer.
export function initScrollVideo() {
    const isTouch = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
    if (!isTouch) return;

    document.querySelectorAll('.rv-scroll-video__media').forEach((media) => {
        const wrapper = media.closest('.rv-scroll-video-wrapper') || media.closest('.rv-scroll-video');
        if (!wrapper) return;

        gsap.fromTo(media,
            { yPercent: -15 },
            {
                yPercent: 15,
                ease: 'none',
                scrollTrigger: {
                    trigger: wrapper,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true,
                },
            }
        );
    });
}
