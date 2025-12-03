function initMobileMenu() {
    console.log("Mobile menu init…");

    const menu = document.querySelector("#menu");
    if (!menu) {
        console.log("Menu #menu not found");
        return;
    }

    // Init Mmenu
    const mmenu = new Mmenu("#menu", {
        offCanvas: { position: "left-front" },
        theme: "white",
        counters: { add: false },

        iconbar: {
            use: true,
            top: [
                `<a href='#/'><img src='/wp-content/themes/Ruined/src/img/icons/home.svg' alt='home'></a>`,
                `<a href='#/'><img src='/wp-content/themes/Ruined/src/img/icons/account.svg' alt='user'></a>`
            ],
            bottom: [
                `<a href='#/'><img src='/wp-content/themes/Ruined/src/img/icons/instagram1.svg' alt='instagram'></a>`,
                `<a href='#/'><img src='/wp-content/themes/Ruined/src/img/icons/facebook1.svg' alt='facebook'></a>`
            ]
        },

        iconPanels: { add: true, visible: 1 },

        navbars: [
            {
                position: "top",
                content: [
                    "prev",
                    "title",
                    `<button class="mm-close-custom" aria-label="Close menu">
                        <svg width="20" height="20" viewBox="0 0 24 24">
                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>`
                ]
            },
            {
                position: "bottom",
                content: [
                    `<a href='#'><img src='/wp-content/themes/Ruined/src/img/icons/instagram1.svg' alt='instagram'></a>`,
                    `<a href='#'><img src='/wp-content/themes/Ruined/src/img/icons/facebook1.svg' alt='facebook'></a>`
                ]
            }
        ]
    });

    const api = mmenu.API;

    // --- MOBILE BUTTON ---
    const btn = document.querySelector(".mobile-menu-button");

    if (btn) {
        btn.addEventListener("click", () => {
            btn.classList.add("active");
            api.open();
        });
    }

    document.addEventListener("click", (e) => {
        if (e.target.closest(".mm-close-custom")) {
            api.close();
            if (btn) btn.classList.remove("active");
        }
    });

    api.bind("close:finish", () => {
        if (btn) btn.classList.remove("active");
    });
}

document.addEventListener("DOMContentLoaded", initMobileMenu);
