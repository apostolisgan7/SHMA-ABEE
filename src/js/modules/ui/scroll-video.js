import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

// ---------------------------------------
// SAFE DEFAULTS (FIX infinite refresh)
// ---------------------------------------
ScrollTrigger.defaults({
    anticipatePin: 1,
    fastScrollEnd: true,
    invalidateOnRefresh: false, // CRITICAL FIX
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
    if (!sections.length) {
        return;
    }

    sections.forEach((section) => {
        const video = section.querySelector(".rv-scroll-video__video");
        if (!video) {
            return;
        }

        video.muted = true;
        video.playsInline = true;

        const src = video.currentSrc || video.src;

        // unlock for iOS
        once(document.documentElement, "touchstart", () => {
            video.play().then(() => video.pause()).catch(() => {});
        }, { passive: true });

        // ---------------------------------------
        // SETUP SCROLL TRIGGER
        // ---------------------------------------
        const setupScrollTrigger = () => {
            const duration = video.duration;

            if (!duration || !isFinite(duration)) {
                console.warn("[ScrollVideo] Duration invalid:", duration);
                return;
            }


            video.pause();
            video.currentTime = 0;

            const getSafeScrollDistance = () => {
                const doc = document.documentElement;
                const maxScrollable = doc.scrollHeight - window.innerHeight;
                const fromTop =
                    section.getBoundingClientRect().top + window.pageYOffset;

                const available = Math.max(0, maxScrollable - fromTop);
                const desired = window.innerHeight * 2.5;

                return Math.min(desired, available || desired);
            };

            gsap.to(video, {
                currentTime: duration - 0.01,
                ease: "none",
                scrollTrigger: {
                    trigger: section.parentElement,
                    start: "top top",
                    end: () => "+=" + getSafeScrollDistance(),
                    scrub: true,
                    pin: section,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                    // markers: true,
                }
            });


        };

        // ---------------------------------------
        // LOAD VIDEO AS BLOB (safe for Safari/iOS)
        // ---------------------------------------
        const prepareSourceForScrubbing = () => {


            if (!("fetch" in window) || !src) {



                video.addEventListener("loadedmetadata", setupScrollTrigger, {
                    once: true,
                });
                return;
            }

            fetch(src)
                .then((res) => res.blob())
                .then((blob) => {

                    const blobURL = URL.createObjectURL(blob);
                    const t = video.currentTime || 0;

                    video.src = blobURL;
                    video.currentTime = t + 0.01;

                    video.addEventListener("loadedmetadata", () => {


                        setupScrollTrigger();
                    }, { once: true });
                })
                .catch((err) => {
                    video.addEventListener("loadedmetadata", setupScrollTrigger, {
                        once: true,
                    });
                });
        };

        // ---------------------------------------
        // INITIAL LOAD
        // ---------------------------------------
        if (video.readyState >= 1) {
            prepareSourceForScrubbing();
        } else {
            video.addEventListener("loadedmetadata", prepareSourceForScrubbing, {
                once: true,
            });
        }
    });

    // ---------------------------------------
    // FIX: prevent cascading refresh loops
    // ---------------------------------------
    ScrollTrigger.config({
        autoRefreshEvents: "visibilitychange", // SAFE MODE
    });

    ScrollTrigger.clearScrollMemory();
}
