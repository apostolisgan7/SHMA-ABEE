function initMobileMenu() {
    console.log("Mobile menu init…");

    // Το Mmenu θα ανοίξει ΜΟΝΟ από <a href="#menu">
    const menu = document.querySelector("#menu");
    if (!menu) {
        console.log("Menu #menu not found");
        return;
    }

    // Init Mmenu
    new Mmenu("#menu", {
        offCanvas: { position: "left" },
        theme: "white",
        counters: { add: true },

        iconbar: {
            use: true,
            top: [
                "<a href='#/'><i class='fa fa-home'></i></a>",
                "<a href='#/'><i class='fa fa-user'></i></a>"
            ],
            bottom: [
                "<a href='#/'><i class='fa fa-twitter'></i></a>",
                "<a href='#/'><i class='fa fa-facebook'></i></a>",
                "<a href='#/'><i class='fa fa-linkedin'></i></a>"
            ]
        },

        iconPanels: { add: true, visible: 1 },

        navbars: [
            { position: "top", content: ["searchfield"] },
            { position: "top", content: ["prev", "title"] },
            {
                position: "bottom",
                content: [
                    "<a class='fa fa-envelope' href='#/'></a>",
                    "<a class='fa fa-twitter' href='#/'></a>",
                    "<a class='fa fa-facebook' href='#/'></a>"
                ]
            }
        ]
    });

    // BURGER BUTTON ANIMATION
    const btn = document.querySelector(".mobile-menu-button");
    if (btn) {
        btn.addEventListener("click", () => {
            btn.classList.toggle("active");
        });
    }
}

document.addEventListener("DOMContentLoaded", initMobileMenu);
