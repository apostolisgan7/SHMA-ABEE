import gsap from "gsap";

export function initProductCatalogs() {
    const items = document.querySelectorAll(".catalog-item");
    const preview = document.getElementById("catalog-preview");

    if (!items.length || !preview) return;

    // Use .src (normalized absolute URL) instead of getAttribute to avoid comparison mismatches
    let activeSrc = preview.src;

    items.forEach(item => {
        const image = item.dataset.image;
        if (!image) return;

        item.addEventListener("mouseenter", () => {
            if (image === activeSrc) return;

            gsap.killTweensOf(preview);
            gsap.to(preview, {
                opacity: 0,
                duration: 0.3,
                onComplete: () => {
                    preview.src = image;
                    activeSrc = preview.src;

                    gsap.to(preview, {
                        opacity: 1,
                        duration: 0.4
                    });
                }
            });
        });
    });
}
