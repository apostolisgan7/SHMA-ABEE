import gsap from "gsap";

export function initProductCatalogs() {
    const items = document.querySelectorAll(".catalog-item");
    const preview = document.getElementById("catalog-preview");

    if (!items.length || !preview) return;

    let activeSrc = preview.getAttribute("src");

    items.forEach(item => {
        const image = item.dataset.image;
        if (!image) return;

        item.addEventListener("mouseenter", () => {
            if (image === activeSrc) return;

            gsap.to(preview, {
                opacity: 0,
                duration: 0.3,
                onComplete: () => {
                    preview.src = image;
                    activeSrc = image;

                    gsap.to(preview, {
                        opacity: 1,
                        duration: 0.4
                    });
                }
            });
        });
    });
}
