import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export function initHistory() {
    const section = document.querySelector(".history-horizontal");
    const track = section.querySelector(".history-track");
    const progress = section.querySelector(".progress-bar span");

    if (!section || !track) return;

    // Καθαρίζουμε τυχόν προηγούμενα instances αν ξανατρέχει η function
    ScrollTrigger.getAll().forEach(t => {
        if (t.trigger === section) t.kill();
    });

    const getScrollAmount = () => track.scrollWidth - window.innerWidth;

    // Κύριο Timeline
    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: section,
            start: "top top",
            end: () => "+=" + getScrollAmount(),
            pin: true,
            scrub: 1, // Λίγο παραπάνω scrub (π.χ. 1) βοηθάει στην εξομάλυνση με τον Lenis
            invalidateOnRefresh: true,
            anticipatePin: 1, // Βοηθάει στο να μην "πηδάει" το scroll κατά το pinning
        }
    });

    // Οριζόντια κίνηση
    tl.to(track, {
        x: () => -getScrollAmount(),
        ease: "none"
    }, 0);

    // Progress Bar (Μέσα στο timeline για μηδενικό lag)
    if (progress) {
        tl.to(progress, {
            scaleX: 1,
            ease: "none"
        }, 0);
    }

    // Σύνδεση Lenis με ScrollTrigger
    // Αν ο lenis είναι global, χρησιμοποίησε το παρακάτω:
    if (typeof lenis !== "undefined") {
        lenis.on('scroll', ScrollTrigger.update);
    }
}