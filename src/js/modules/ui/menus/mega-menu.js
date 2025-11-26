import gsap from "gsap";

export function initMegaMenu() {

    const menu      = document.getElementById("megaMenu");
    const overlay   = document.getElementById("megaMenuOverlay");
    const openBtn   = document.querySelector(".desktop-menu-button");
    const closeBtn  = document.getElementById("megaMenuClose");

    if (!menu || !overlay || !openBtn) return;

    const tl = gsap.timeline({
        paused: true,
        defaults: { ease: "power2.out" }
    });

    // OPEN ANIMATION (same style as catalog)
    tl.to(overlay, {
        opacity: 1,
        visibility: "visible",
        pointerEvents: "auto",
        backdropFilter: "blur(0px)",
        duration: 0.35
    })

        .to(overlay, {
            backdropFilter: "blur(8px)",
            duration: 0.4
        }, "-=0.25")

        .fromTo(menu,
            { y: 40, opacity: 0, scale: 0.95 },
            { y: 0, opacity: 1, scale: 1, duration: 0.45, ease: "power3.out" },
            "-=0.2"
        )

        .from(".mega-item", {
            x: -20,
            opacity: 0,
            stagger: 0.05,
            duration: 0.35,
            clearProps: "all"
        }, "-=0.25")

        .from(".mega-footer-col", {
            y: 20,
            opacity: 0,
            stagger: 0.06,
            duration: 0.4,
            clearProps: "all",
            ease: "power2.out"
        }, "-=0.25");


    // -------------------- OPEN --------------------
    function openMenu() {

        // ⭐ MUST MAKE VISIBLE BEFORE PLAY
        menu.style.visibility = "visible";
        overlay.style.visibility = "visible";

        menu.classList.add("is-open");
        overlay.classList.add("is-open");
        openBtn.classList.add("active");

        document.body.style.overflow = "hidden";

        tl.restart();
    }

    // -------------------- CLOSE --------------------
    function closeMenu() {

        tl.reverse();

        tl.eventCallback("onReverseComplete", () => {

            menu.classList.remove("is-open");
            overlay.classList.remove("is-open");
            openBtn.classList.remove("active");

            // ⭐ FULL HIDE AFTER ANIMATION
            menu.style.visibility = "hidden";
            overlay.style.visibility = "hidden";

            document.body.style.overflow = "";
        });
    }


    // EVENTS
    openBtn.addEventListener("click", openMenu);
    closeBtn?.addEventListener("click", closeMenu);
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) closeMenu();
    });

    window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeMenu();
    });
}
