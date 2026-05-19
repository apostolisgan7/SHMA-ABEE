import { gsap } from 'gsap';

const THRESHOLD = 400;

export function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    gsap.set(btn, { opacity: 0, y: 16, pointerEvents: 'none' });

    let visible = false;
    let ticking = false;

    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(update);
            ticking = true;
        }
    }

    function update() {
        ticking = false;
        const scrolled = window.scrollY > THRESHOLD;

        if (scrolled && !visible) {
            visible = true;
            gsap.to(btn, { opacity: 1, y: 0, pointerEvents: 'auto', duration: 0.45, ease: 'power3.out' });
        } else if (!scrolled && visible) {
            visible = false;
            gsap.to(btn, { opacity: 0, y: 16, pointerEvents: 'none', duration: 0.3, ease: 'power2.in' });
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    // Click → scroll to top
    btn.addEventListener('click', () => {
        gsap.timeline()
            .to(btn, { scale: 0.88, duration: 0.1, ease: 'power1.in' })
            .to(btn, { scale: 1,    duration: 0.25, ease: 'elastic.out(1, 0.5)' });

        if (window.__lenis__) {
            window.__lenis__.scrollTo(0, {
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            });
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Hover lift
    btn.addEventListener('mouseenter', () => {
        gsap.to(btn, { y: -4, duration: 0.2, ease: 'power2.out' });
    });
    btn.addEventListener('mouseleave', () => {
        gsap.to(btn, { y: 0,  duration: 0.2, ease: 'power2.out' });
    });
}
