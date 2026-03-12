import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export function initHistory() {
    const section = document.querySelector('.history-horizontal');
    if (!section) return;

    const topImgs = section.querySelectorAll('.top-right img');
    const bottomImgs = section.querySelectorAll('.bottom-left img');
    const infoItems = section.querySelectorAll('.info-item');

    ScrollTrigger.create({
        trigger: section,
        start: "top top",
        end: "+=200%", // Η "ταχύτητα" του scroll
        pin: true,
        scrub: 1,
        onUpdate: (self) => {
            const progress = self.progress;
            const total = infoItems.length;

            // Υπολογισμός του τρέχοντος slide βάσει scroll progress
            let index = Math.floor(progress * total);
            if (index >= total) index = total - 1;

            // Helper function για το fade
            const toggleActive = (elements, idx) => {
                elements.forEach((el, i) => {
                    el.classList.toggle('active', i === idx);
                });
            };

            toggleActive(topImgs, index);
            toggleActive(bottomImgs, index);
            toggleActive(infoItems, index);
        }
    });
}