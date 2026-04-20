import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export function initHistory() {
    const section = document.querySelector(".history-horizontal");

    // 🔥 FIX 1: Πρώτα ελέγχουμε αν υπάρχει το section, μετά ψάχνουμε τα παιδιά του
    if (!section) return;

    const track = section.querySelector(".history-track");
    const progress = section.querySelector(".progress-bar span");

    // 🔥 FIX 2: Αν λείπει το track, σταμάτα για να μη βγάλει error παρακάτω
    if (!track) return;

    // Καθαρισμός παλιών Triggers (χρήσιμο σε Single Page Apps/Next.js κτλ)
    ScrollTrigger.getAll().forEach(t => {
        if (t.trigger === section) t.kill();
    });

    const getScrollAmount = () => track.scrollWidth - window.innerWidth;

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: section,
            start: "top top",
            end: () => "+=" + getScrollAmount(),
            pin: true,
            scrub: 1,
            invalidateOnRefresh: true,
            anticipatePin: 1,
        }
    });

    tl.to(track, {
        x: () => -getScrollAmount(),
        ease: "none"
    }, 0);

    if (progress) {
        tl.to(progress, {
            scaleX: 1,
            ease: "none"
        }, 0);
    }

    // Lenis integration
    if (typeof lenis !== "undefined") {
        lenis.on('scroll', ScrollTrigger.update);
    }
}