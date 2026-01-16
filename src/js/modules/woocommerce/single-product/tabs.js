import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import gsap from "gsap";

let fancyboxInitialized = false;

export function animateTabContent(el) {
    if (!el) return;

    // 1. Σταματάμε τα πάντα και τα κάνουμε αόρατα ΑΚΑΡΙΑΙΑ
    gsap.killTweensOf(el);
    const revealItems = el.querySelectorAll('.rv-manual-wrap, .rv-tech-diagram, .rv-tech-table .row, .rv-project-thumb, h3');
    gsap.killTweensOf(revealItems);

    // Προετοιμασία: Τα μηδενίζουμε όλα πριν το timeline
    gsap.set(el, { opacity: 0, y: 15 });
    if (revealItems.length > 0) {
        gsap.set(revealItems, { opacity: 0, y: 10 });
    }

    const tl = gsap.timeline({
        delay: 0.05 // Μικρό delay για να σιγουρευτούμε ότι το display:block έχει ολοκληρωθεί
    });

    tl.to(el, {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: "power2.out",
        force3D: true
    });

    if (revealItems.length > 0) {
        tl.to(revealItems, {
            opacity: 1,
            y: 0,
            duration: 0.3,
            stagger: 0.05,
            ease: "power1.out"
        }, "-=0.2");
    }
}

export function initProductTabs() {
    if (fancyboxInitialized) return;
    fancyboxInitialized = true;

    // Intersection Observer για το πρώτο load (Scroll)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const activeTab = entry.target.querySelector('.rv-tab-content:not([style*="display: none"])');
                if (activeTab) animateTabContent(activeTab);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    const container = document.querySelector('.rv-product-tabs');
    if (container) observer.observe(container);

    // --- ΤΟ ΚΛΕΙΔΙ ΓΙΑ ΤΟ GLITCH ---
    window.addEventListener('rv-tab-changed', () => {
        // Πρώτα κρύβουμε ΟΛΑ τα tabs ακαριαία με GSAP πριν καν αλλάξει το Alpine
        // Έτσι, όταν το Alpine δείξει το νέο tab, αυτό θα είναι ήδη opacity 0
        const allTabs = document.querySelectorAll('.rv-tab-content');
        gsap.set(allTabs, { opacity: 0 });

        setTimeout(() => {
            const activeTab = document.querySelector('.rv-tab-content:not([style*="display: none"])');
            if (activeTab) {
                animateTabContent(activeTab);
            }
        }, 40); // Πολύ γρήγορο timeout
    });

    // Fancybox Logic (Παραμένει ίδια)
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.rv-gallery [data-fancybox], .rv-tech-diagram [data-fancybox]');
        if (!trigger) return;
        e.preventDefault();

        let items = [];
        let startIndex = 0;
        const gallery = trigger.closest('.rv-gallery');
        if (gallery) {
            const images = gallery.querySelectorAll('img');
            images.forEach((img, index) => {
                items.push({ src: img.currentSrc || img.src, type: 'image' });
                if (img === trigger.querySelector('img') || img === trigger) startIndex = index;
            });
        } else {
            const href = trigger.getAttribute('href') || trigger.querySelector('img')?.src;
            if(href) items.push({ src: href, type: 'image' });
        }
        Fancybox.show(items, {
            startIndex,
            Thumbs: gallery ? { type: 'classic' } : false,
            Toolbar: { display: { left: [], middle: [], right: ['close'] } },
        });
    });
}

const init = () => { if (!fancyboxInitialized) initProductTabs(); };
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}