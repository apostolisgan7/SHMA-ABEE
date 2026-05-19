import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Splitting from 'splitting';

/* ─────────────────────────────────────────────
   HELPERS
───────────────────────────────────────────── */
function toVars(props, el, triggerMode) {
    if (triggerMode !== 'load') {
        const start = el.dataset.animateStart || 'top 82%';
        props.scrollTrigger = { trigger: el, start, once: true };
    }
    return props;
}

function makeTimeline(el, triggerMode, delay) {
    const config = { delay };
    if (triggerMode !== 'load') {
        const start = el.dataset.animateStart || 'top 82%';
        config.scrollTrigger = { trigger: el, start, once: true };
    }
    return gsap.timeline(config);
}

/* ─────────────────────────────────────────────
   ANIMATION TYPES
───────────────────────────────────────────── */

function animateTitleReveal(el, { delay, trigger }) {
    const result = Splitting({ target: el, by: 'chars' });
    const words = result[0]?.words || [];
    const chars = result[0]?.chars || [];
    if (!chars.length) return;

    // Words: inline-block + nowrap keeps chars together, line breaks happen between words
    words.forEach(word => {
        word.style.cssText = 'display:inline-block;white-space:nowrap;';
    });

    // Wrap each char in overflow-hidden clip for slide-up reveal
    chars.forEach(char => {
        const clip = document.createElement('span');
        clip.style.cssText = 'display:inline-block;overflow:hidden;vertical-align:top;';
        char.parentNode.insertBefore(clip, char);
        clip.appendChild(char);
        char.style.display = 'inline-block';
    });

    // Hide chars first, then reveal container — prevents flash between the two
    gsap.set(chars, { yPercent: 110 });
    gsap.set(el, { opacity: 1 });

    gsap.fromTo(chars,
        { yPercent: 110 },
        toVars({
            yPercent: 0,
            duration: 1.4,
            stagger: { amount: 0.4, ease: 'power2.in' },
            ease: 'power4.out',
            delay,
        }, el, trigger)
    );
}

function animateFadeUp(el, { delay, trigger }) {
    gsap.fromTo(el,
        { y: 36, autoAlpha: 0, scale: 0.98 },
        toVars({
            y: 0, autoAlpha: 1, scale: 1,
            duration: 1.1,
            ease: 'power3.out',
            transformOrigin: 'top center',
            delay,
        }, el, trigger)
    );
}

function animateImageReveal(el, { delay, trigger, direction }) {
    const clipFrom = direction === 'right' ? 'inset(0 0 0 100%)' : 'inset(0 100% 0 0)';
    const clipTo   = 'inset(0% 0% 0% 0%)';

    // Make container visible — clip-path handles the reveal
    gsap.set(el, { opacity: 1 });

    const tl = makeTimeline(el, trigger, delay);
    tl.fromTo(el,    { clipPath: clipFrom }, { clipPath: clipTo, duration: 1.5, ease: 'expo.inOut' });

    const media = el.querySelector('img, video');
    if (media) {
        tl.fromTo(media, { scale: 1.15 }, { scale: 1, duration: 1.8, ease: 'power2.out' }, '<');
    }
}

function animateFadeIn(el, { delay, trigger }) {
    gsap.fromTo(el,
        { autoAlpha: 0, x: -8 },
        toVars({
            autoAlpha: 1, x: 0,
            duration: 0.9,
            ease: 'power2.out',
            delay,
        }, el, trigger)
    );
}

function animateCardStagger(el, { delay, trigger }) {
    const slides = Array.from(el.children);
    if (!slides.length) return;

    gsap.set(slides, { y: 50, opacity: 0 });
    gsap.set(el, { opacity: 1 });
    gsap.fromTo(slides,
        { y: 50, opacity: 0 },
        toVars({
            y: 0, opacity: 1,
            duration: 0.85,
            stagger: 0.13,
            ease: 'power3.out',
            delay,
        }, el, trigger)
    );
}

function animateStaggerFade(el, { delay, trigger, stagger }) {
    const children = Array.from(el.children);
    if (!children.length) return;

    gsap.set(children, { autoAlpha: 0, y: 40 });
    gsap.set(el, { opacity: 1 });

    gsap.fromTo(children,
        { y: 40, autoAlpha: 0 },
        toVars({
            y: 0, autoAlpha: 1,
            duration: 0.9,
            stagger: stagger || 0.12,
            ease: 'power3.out',
            delay,
        }, el, trigger)
    );
}

const ANIMATIONS = {
    'title-reveal': animateTitleReveal,
    'fade-up':      animateFadeUp,
    'image-reveal': animateImageReveal,
    'fade-in':      animateFadeIn,
    'stagger-fade': animateStaggerFade,
    'card-stagger': animateCardStagger,
};

/* ─────────────────────────────────────────────
   MAIN INIT
───────────────────────────────────────────── */
export function initAnimations() {
    const els = document.querySelectorAll('[data-animate]');
    if (!els.length) return;

    els.forEach(el => {
        const fn = ANIMATIONS[el.dataset.animate];
        if (!fn) return;
        fn(el, {
            delay:     parseFloat(el.dataset.animateDelay     || 0),
            trigger:   el.dataset.animateTrigger || 'scroll',
            direction: el.dataset.animateDirection || null,
            stagger:   parseFloat(el.dataset.animateStagger   || 0.1),
        });
    });
}

/* ─────────────────────────────────────────────
   HEADER load animation
───────────────────────────────────────────── */
export function initHeaderAnimation() {
    const inner = document.querySelector('[data-header-anim]');
    if (!inner) return;

    const left       = inner.querySelector('.header-left');
    const rightItems = Array.from(inner.querySelectorAll('.header-right > *'));
    const allItems   = [left, ...rightItems].filter(Boolean);

    // Logo slides in from left, rest stagger in from top
    gsap.fromTo(left,
        { autoAlpha: 0, x: -18 },
        { autoAlpha: 1, x: 0, duration: 1.2, ease: 'power3.out', delay: 0.4 }
    );

    gsap.fromTo(rightItems,
        { autoAlpha: 0, y: -14 },
        {
            autoAlpha: 1,
            y: 0,
            duration: 1.3,
            stagger: 0.07,
            ease: 'power3.out',
            delay: 0.5,
        }
    );
}

/* ─────────────────────────────────────────────
   FOOTER divider animation
───────────────────────────────────────────── */
export function initFooterAnimation() {
    const footer = document.getElementById('colophon');
    if (!footer || footer.dataset.footerAnimated) return;
    footer.dataset.footerAnimated = '1';

    const divider = footer.querySelector('.footer-divider');
    if (divider) {
        gsap.fromTo(divider,
            { scaleX: 0 },
            {
                scaleX: 1,
                transformOrigin: 'left center',
                duration: 1.1,
                ease: 'expo.inOut',
                scrollTrigger: { trigger: divider, start: 'top 90%', once: true },
            }
        );
    }
}
