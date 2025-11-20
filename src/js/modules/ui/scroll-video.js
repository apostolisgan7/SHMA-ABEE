import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export function initScrollVideo() {
    const sections = document.querySelectorAll(".rv-scroll-video");
    if (!sections.length) return;

    sections.forEach((section) => {
        const video = section.querySelector(".rv-scroll-video__video");
        if (!video) return;

        const init = () => {
            const duration = video.duration;
            if (!duration || !isFinite(duration)) return;

            video.pause();
            video.currentTime = 0;
            video.muted = true;

            // Store the ScrollTrigger instance for cleanup
            const scrollTrigger = ScrollTrigger.create({
                trigger: section,
                start: "top top",
                end: () => `bottom top+=${section.offsetHeight}`,
                pin: true,
                scrub: true,
                anticipatePin: 1,
                invalidateOnRefresh: true,

                onUpdate: (self) => {
                    const t = self.progress * duration;
                    const clamped = Math.min(Math.max(t, 0), duration - 0.01);
                    video.currentTime = clamped;
                },

                onLeave: () => {
                    video.pause();
                    if (scrollTrigger) scrollTrigger.disable();
                },
                onEnterBack: () => {
                    video.pause();
                    if (scrollTrigger) scrollTrigger.enable();
                },
                onLeaveBack: () => {
                    video.pause();
                }
            });
        };

        if (video.readyState >= 1) {
            init();
        } else {
            video.addEventListener("loadedmetadata", init, { once: true });
        }
    });
}
