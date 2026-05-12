// src/modules/ui/menus/catalog-menu.js
import {gsap} from "gsap";

export function initCatalogMenu() {
    const toggleBtn = document.querySelector("[data-catalog-toggle]");
    const closeBtn = document.querySelector("[data-catalog-close]");
    const backdrop = document.getElementById("megaMenuBackdrop");
    const container = document.getElementById("megaMenuContainer");

    if (!toggleBtn || !backdrop || !container) return;

    let firstLoad = true;

    const tl = gsap.timeline({paused: true});

    tl.to(backdrop, {
        opacity: 1,
        display: "flex",
        backdropFilter: "blur(0px)",
        duration: 0.35,
        ease: "power2.out"
    })
        .to(backdrop, {
            backdropFilter: "blur(8px)",
            duration: 0.4,
            ease: "power2.out"
        }, "-=0.2")
        .fromTo(
            container,
            {y: 40, opacity: 0, scale: 0.95},
            {y: 0, opacity: 1, scale: 1, duration: 0.45, ease: "power3.out"},
            "-=0.2"
        )
        // HEADER
        .from(".mega-animate-header", {
            y: -20,
            opacity: 0,
            stagger: 0.05,
            duration: 0.45,
            clearProps: "all",
            ease: "power2.out"
        }, "-=0.3")
        // LEFT ITEMS
        .from(".mega-left-item", {
            x: -20,
            opacity: 0,
            stagger: 0.04,
            duration: 0.35,
            clearProps: "all",
            ease: "power2.out"
        }, "-=0.3")
        // BORDER (εμφανίζεται μαζί με τα left items)
        .from(".mega-left", {
            borderRightColor: "rgba(0,0,0,0)",
            duration: 0.55,
            ease: "power2.out"
        }, "<")
        // RIGHT SIDE (static column)
        .from(".mega-right", {
            x: 40,
            opacity: 0,
            duration: 0.45,
            clearProps: "all",
            ease: "power3.out"
        }, "-=0.35");

    const openCatalog  = () => {
        window.__lenis__?.stop();
        document.documentElement.classList.add('scroll-locked');
        tl.play();
    };
    const closeCatalog = () => {
        window.__lenis__?.start();
        document.documentElement.classList.remove('scroll-locked');
        tl.reverse();
    };

    toggleBtn.addEventListener("click", openCatalog);
    closeBtn?.addEventListener("click", closeCatalog);

    backdrop.addEventListener("click", (e) => {
        if (e.target === backdrop) closeCatalog();
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeCatalog();
    });

    const leftItems = document.querySelectorAll(".mega-left-item");
    const rightPanels = document.querySelectorAll(".mega-right-panel");
    const watermark = document.querySelector("[data-watermark]");

    function updateWatermark(text) {
        if (!watermark) return;

        gsap.to(watermark, {
            opacity: 0,
            duration: 0.15,
            onComplete() {
                watermark.textContent = text;
                gsap.to(watermark, {
                    opacity: 0.03,
                    duration: 0.3
                });
            }
        });
    }

    function activateCategory(catId, el) {
        const newPanel = document.querySelector(`[data-category-panel="${catId}"]`);
        const oldPanel = document.querySelector(".mega-right-panel.active");

        leftItems.forEach(i => i.classList.remove("active"));
        el.classList.add("active");

        updateWatermark(el.querySelector(".item-label").textContent);

        if (!newPanel || newPanel === oldPanel) return;

        // RESET ΟΛΑ ΤΑ PANELS + ΟΛΑ ΤΑ ΠΑΙΔΙΑ ΤΟΥΣ
        rightPanels.forEach(panel => {
            panel.classList.remove("active");

            gsap.set(panel, {
                opacity: 0,
                display: "none",
                y: 0
            });

            gsap.set(panel.querySelectorAll(".mega-animate-right"), {
                opacity: 0,
                y: 0
            });
        });

        // SET NEW PANEL
        newPanel.classList.add("active");
        newPanel.style.display = "flex";

        const subItems = newPanel.querySelectorAll(".sub-item.mega-animate-right");
        const btn = newPanel.querySelector(".catalog_btn.mega-animate-right");

        gsap.fromTo(
            subItems,
            { opacity: 0, y: 15 },
            {
                opacity: 1,
                y: 0,
                duration: 0.25,
                stagger: 0.05,
                ease: "power2.out",
                clearProps: "all"
            }
        );

        if (btn) {
            gsap.fromTo(
                btn,
                { opacity: 0, y: 20 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.45,
                    delay: subItems.length * 0.05 + 0.4,
                    ease: "power3.out",
                    clearProps: "all"
                }
            );
        }
    }



    // Setup hover
    leftItems.forEach(item => {
        item.addEventListener("mouseenter", () => {
            activateCategory(item.dataset.category, item);
        });
    });

    // Default active on load
    if (leftItems[0] && rightPanels[0]) {
        leftItems[0].classList.add("active");
        rightPanels[0].classList.add("active");
        updateWatermark(leftItems[0].querySelector(".item-label").textContent);
    }
}
