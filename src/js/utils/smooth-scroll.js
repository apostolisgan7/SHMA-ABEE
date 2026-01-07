import Lenis from '@studio-freight/lenis';
import { ScrollTrigger } from "gsap/ScrollTrigger";
import gsap from "gsap";

gsap.registerPlugin(ScrollTrigger);

export function initSmoothScroll() {
    if (typeof window === "undefined") return null;

    // --- Prevent DOUBLE initialization ---
    if (window.__lenis__) {
        console.warn("[Lenis] Already initialized → SKIP");
        return window.__lenis__;
    }

    const lenis = new Lenis({
        duration: 1.1,
        easing: (t) => 1 - Math.pow(2, -10 * t),
        smooth: true,
        smoothTouch: false,
    });

    // Save globally
    window.__lenis__ = lenis;

    // --- GSAP integration ---
    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    // ScrollTrigger sync BUT no refresh loops
    lenis.on("scroll", () => {
        ScrollTrigger.update();
    });

    document.documentElement.classList.add("smooth-scroll");
    document.documentElement.setAttribute("data-scroll", "");


    return lenis;
}

export default initSmoothScroll;
