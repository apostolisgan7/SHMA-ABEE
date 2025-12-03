import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

// helper για once listener (όπως στο παλιό σου script)
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

        // βεβαιώσου ότι έχει τα σωστά attributes
        video.muted = true;
        video.playsInline = true;

        const src = video.currentSrc || video.src;

        // iOS «ξεκλείδωμα» – χρειάζεται μία user interaction
        once(document.documentElement, "touchstart", () => {
            video.play().then(() => {
                video.pause();
            }).catch(() => {});
        }, { passive: true });

        // όταν είμαστε έτοιμοι να στήσουμε το ScrollTrigger
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
                    // εδώ ορίζεις ΠΟΣΟ scroll θέλεις, π.χ. 2.5x το ύψος του viewport
                    end: () => "+=" + window.innerHeight * 2.5,
                    scrub: true,
                    pin: true,
                    anticipatePin: 1,
                    invalidateOnRefresh: true,
                    // markers: true, // βάλε το προσωρινά για debug
                }
            });
        };


        // blob hack για iOS/Safari ώστε να δουλεύει το scrubbing στο currentTime
        const prepareSourceForScrubbing = () => {
            if (!("fetch" in window) || !src) {
                // fallback: απλά στήσε το scroll trigger με το κανονικό src
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
                    // αν αποτύχει, πάλι στήσε το κανονικό
                    if (video.readyState >= 1) {
                        setupScrollTrigger();
                    } else {
                        video.addEventListener("loadedmetadata", setupScrollTrigger, { once: true });
                    }
                });
        };

        // ξεκίνα τη διαδικασία
        if (video.readyState >= 1) {
            prepareSourceForScrubbing();
        } else {
            video.addEventListener("loadedmetadata", prepareSourceForScrubbing, { once: true });
        }
    });
}
