import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Splitting from 'splitting';

/* ── Split chars + wrap σε overflow-clip (exact same pattern as title-reveal) ── */
function prepareChars(el) {
    if (!el || el._charsReady) return el?._chars || [];

    const result = Splitting({ target: el, by: 'chars' });
    const words  = result[0]?.words || [];
    const chars  = result[0]?.chars || [];
    if (!chars.length) return [];

    words.forEach(w => { w.style.cssText = 'display:inline-block;white-space:nowrap;'; });
    chars.forEach(char => {
        const clip = document.createElement('span');
        clip.style.cssText = 'display:inline-block;overflow:hidden;vertical-align:top;';
        char.parentNode.insertBefore(clip, char);
        clip.appendChild(char);
        char.style.display = 'inline-block';
    });

    gsap.set(chars, { yPercent: 110 });
    gsap.set(el, { opacity: 1 });

    el._chars      = chars;
    el._charsReady = true;
    return chars;
}

/* ── Play chars reveal ── */
function playChars(chars, delay = 0) {
    if (!chars?.length) return;
    gsap.set(chars, { yPercent: 110 });
    gsap.to(chars, {
        yPercent: 0,
        duration: 1.2,
        stagger: { amount: 0.35, ease: 'power2.in' },
        ease: 'power4.out',
        delay,
    });
}

/* ── Animate year + title (chars) + text (fade) of a single slide ── */
function animateSlideContent(slide, delay = 0) {
    const yearEl  = slide.querySelector('.history-horizontal__year');
    const titleEl = slide.querySelector('.history-horizontal__card-title');
    const textEl  = slide.querySelector('.history-horizontal__text');

    playChars(yearEl?._chars,  delay);
    playChars(titleEl?._chars, delay + 0.1);

    if (textEl) {
        gsap.fromTo(textEl,
            { autoAlpha: 0, y: 6 },
            { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power2.out', delay: delay + 0.25 }
        );
    }
}

/* ─────────────────────────────────────────── */
export function initHistory() {
    const sections = document.querySelectorAll('.history-horizontal');
    if (!sections.length) return;

    sections.forEach(section => {
        const sectionTitle = section.querySelector('.history-horizontal__title');
        const slides       = Array.from(section.querySelectorAll('.history-horizontal__slide'));
        const pagination   = section.querySelector('.history-horizontal__pagination');
        const swiperEl     = section.querySelector('.history-horizontal__swiper');
        const seen         = new Set();

        // Pre-split chars + pre-hide text in every slide
        slides.forEach(slide => {
            prepareChars(slide.querySelector('.history-horizontal__year'));
            prepareChars(slide.querySelector('.history-horizontal__card-title'));
            const textEl = slide.querySelector('.history-horizontal__text');
            if (textEl) gsap.set(textEl, { autoAlpha: 0 });
        });

        // Section title fade-up
        if (sectionTitle) {
            gsap.fromTo(sectionTitle,
                { autoAlpha: 0, y: 36 },
                {
                    autoAlpha: 1, y: 0,
                    duration: 1.1, ease: 'power3.out',
                    scrollTrigger: { trigger: section, start: 'top 82%', once: true },
                }
            );
        }

        // Slides stagger + content on scroll entry
        if (slides.length) {
            gsap.set(slides, { autoAlpha: 0, y: 60, scale: 0.96 });

            ScrollTrigger.create({
                trigger: section,
                start: 'top 78%',
                once: true,
                onEnter() {
                    // Stagger slides in
                    gsap.to(slides, {
                        autoAlpha: 1, y: 0, scale: 1,
                        duration: 1, stagger: 0.14, ease: 'power3.out',
                        clearProps: 'scale',
                    });

                    // Content animation for initially visible slides
                    const swiper       = swiperEl?.swiper;
                    const initialCount = Math.ceil(swiper?.params?.slidesPerView || 2.3);
                    slides.slice(0, initialCount).forEach((slide, i) => {
                        seen.add(slide);
                        animateSlideContent(slide, 0.25 + i * 0.14);
                    });
                },
            });
        }

        // Pagination
        if (pagination) {
            gsap.fromTo(pagination,
                { autoAlpha: 0, y: 10 },
                {
                    autoAlpha: 1, y: 0, duration: 0.8, ease: 'power2.out',
                    scrollTrigger: { trigger: section, start: 'top 70%', once: true },
                }
            );
        }

        // Swiper slide change: fade + content reveal για το νέο slide
        const swiper = swiperEl?.swiper;
        if (swiper) {
            swiper.on('slideChangeTransitionEnd', function () {
                const perView = Math.ceil(this.params.slidesPerView) || 2;
                const end     = Math.min(this.activeIndex + perView, slides.length);

                for (let i = this.activeIndex; i < end; i++) {
                    const slide = slides[i];
                    if (!slide || seen.has(slide)) continue;
                    seen.add(slide);

                    // Chars + text reveal
                    animateSlideContent(slide, 0.08);
                }
            });
        }
    });
}
