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
    console.log("%c[ScrollVideo] INIT", "color:#5bc0de");

    const sections = document.querySelectorAll(".rv-scroll-video");
    if (!sections.length) {
        console.log("[ScrollVideo] No sections found");
        return;
    }

    sections.forEach((section) => {
        const video = section.querySelector(".rv-scroll-video__video");
        if (!video) {
            console.warn("[ScrollVideo] video element not found");
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

            console.log(
                "%c[ScrollVideo] SETUP ScrollTrigger. Duration: " + duration,
                "color:#00d1b2"
            );

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
                    onEnter: () => console.log('[ScrollVideo] ENTER (pinned)'),
                    onLeave: () => console.log('[ScrollVideo] LEAVE (unpinned)'),
                    onEnterBack: () => console.log('[ScrollVideo] ENTER BACK (pinned again)'),
                    onLeaveBack: () => console.log('[ScrollVideo] LEAVE BACK (unpinned again)'),
                    // markers: true,
                }
            });


            console.log("%c[ScrollVideo] ScrollTrigger CREATED", "color:#00e676");
        };

        // ---------------------------------------
        // LOAD VIDEO AS BLOB (safe for Safari/iOS)
        // ---------------------------------------
        const prepareSourceForScrubbing = () => {
            console.log("[ScrollVideo] prepareSourceForScrubbing()");

            if (!("fetch" in window) || !src) {
                console.log("[ScrollVideo] fetch unsupported → skipping blob");

                video.addEventListener("loadedmetadata", setupScrollTrigger, {
                    once: true,
                });
                return;
            }

            fetch(src)
                .then((res) => res.blob())
                .then((blob) => {
                    console.log("%c[ScrollVideo] Blob URL created", "color:#ff9800");

                    const blobURL = URL.createObjectURL(blob);
                    const t = video.currentTime || 0;

                    video.src = blobURL;
                    video.currentTime = t + 0.01;

                    video.addEventListener("loadedmetadata", () => {
                        console.log(
                            "%c[ScrollVideo] VALID duration detected → " +
                            video.duration,
                            "color:#009688"
                        );

                        setupScrollTrigger();
                    }, { once: true });
                })
                .catch((err) => {
                    console.warn("[ScrollVideo] blob fetch failed:", err);

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
            console.log("[ScrollVideo] video.readyState OK → start");
            video.addEventListener("loadedmetadata", prepareSourceForScrubbing, {
                once: true,
            });
        }
    });

    // ---------------------------------------
    // SCROLLTRIGGER DEBUG (KEEP IT!)
    // ---------------------------------------
    ScrollTrigger.addEventListener("refreshInit", () => {
        console.log("%cScrollTrigger → refreshInit", "color:#ff5252");
    });

    ScrollTrigger.addEventListener("refresh", () => {
        console.log(
            "%cScrollTrigger → refresh DONE",
            "color:#4caf50",
            ScrollTrigger.getAll()
        );
    });

    // ---------------------------------------
    // FIX: prevent cascading refresh loops
    // ---------------------------------------
    ScrollTrigger.config({
        autoRefreshEvents: "visibilitychange", // SAFE MODE
    });

    ScrollTrigger.clearScrollMemory();
}
