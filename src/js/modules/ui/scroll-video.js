import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

// --- GLOBAL FIXES ΓΙΑ SCROLLTRIGGER ---
ScrollTrigger.defaults({
    anticipatePin: 1,
    fastScrollEnd: true,
});

// helper once()
function once(el, event, fn, opts) {
    const onceFn = (e) => {
        el.removeEventListener(event, onceFn, opts);
        fn(e);
    };
    el.addEventListener(event, onceFn, opts);
    return onceFn;
}

export function initScrollVideo() {
    const sections = document.querySelectorAll(".rv-scroll-video");
    if (!sections.length) return;

    sections.forEach((section) => {
        const video = section.querySelector(".rv-scroll-video__video");
        if (!video) return;

        video.muted = true;
        video.playsInline = true;

        const src = video.currentSrc || video.src;

        // iOS tap unlock
        once(document.documentElement, "touchstart", () => {
            video.play().then(() => {
                video.pause();
            }).catch(() => {});
        }, { passive: true });

        // -------------------------------
        //  SETUP SCROLLTRIGGER
        // -------------------------------
        const setupScrollTrigger = () => {
            const duration = video.duration || 1;

            if (!duration || !isFinite(duration)) return;

            video.pause();
            video.currentTime = 0;

            gsap.to(video, {
                currentTime: duration - 0.01,
                ease: "none",
                scrollTrigger: {
                    trigger: section,
                    start: "top top",
                    end: () => "+=" + window.innerHeight * 2.5,
                    scrub: true,
                    pin: true,
                    anticipatePin: 1,
                    fastScrollEnd: true,
                    invalidateOnRefresh: true,
                    // markers: true,
                }
            });

            // --- SUPER REFRESH FIX ---
            ScrollTrigger.refresh();
        };

        // -------------------------------
        //  SAFARI / iOS BLOB FIX
        // -------------------------------
        const prepareSourceForScrubbing = () => {
            // If fetch unsupported → fallback
            if (!("fetch" in window) || !src) {
                if (video.readyState >= 1) {
                    setupScrollTrigger();
                } else {
                    video.addEventListener("loadedmetadata", setupScrollTrigger, { once: true });
                }
                return;
            }

            fetch(src)
                .then((response) => response.blob())
                .then((blob) => {
                    const blobURL = URL.createObjectURL(blob);
                    const t = video.currentTime || 0;

                    video.src = blobURL;
                    video.currentTime = t + 0.01;

                    if (video.readyState >= 1) {
                        setupScrollTrigger();
                    } else {
                        video.addEventListener("loadedmetadata", setupScrollTrigger, { once: true });
                    }
                })
                .catch(() => {
                    if (video.readyState >= 1) {
                        setupScrollTrigger();
                    } else {
                        video.addEventListener("loadedmetadata", setupScrollTrigger, { once: true });
                    }
                });
        };

        // -------------------------------
        // INITIAL LOAD
        // -------------------------------
        if (video.readyState >= 1) {
            prepareSourceForScrubbing();
        } else {
            video.addEventListener("loadedmetadata", prepareSourceForScrubbing, { once: true });
        }
    });

    // -------------------------------
    // GLOBAL REFRESH STABILIZERS
    // -------------------------------

    // Refresh after full page load (fixes admin bar, fonts, delays)
    window.addEventListener("load", () => {
        ScrollTrigger.refresh();
    });

    // Extra fallback refresh for tricky browsers
    setTimeout(() => {
        ScrollTrigger.refresh();
    }, 600);

    // One more micro refresh (fixes mobile bouncing)
    requestAnimationFrame(() => {
        ScrollTrigger.refresh();
    });
}
