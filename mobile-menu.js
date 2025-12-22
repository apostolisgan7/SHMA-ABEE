function initMobileMenu() {
    const menuElement = document.querySelector("#menu");
    if (!menuElement) return;

    // Προσθήκη κλάσης για έλεγχο JS
    document.documentElement.classList.add('js');
    document.body.classList.add('mmenu-loading');

    try {
        const mmenu = new Mmenu(menuElement, {
            // Configuration
            offCanvas: {
                position: "left-front",
                blockUI: true // Εμποδίζει το scroll πίσω από το μενού
            },
            theme: "white",
            counters: { add: false },
            iconPanels: { add: true, visible: 1 },
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
        }, {
            // Off-canvas settings
            offCanvas: {
                page: {
                    selector: "#page" // Βεβαιώσου ότι όλο το site σου είναι μέσα σε ένα <div id="page">
                }
            }
        });

        const api = mmenu.API;
        const btn = document.querySelector(".mobile-menu-button");

        if (btn) {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                api.open();
                btn.classList.add("active");
            });
        }

        // Close events
        api.bind("open:start", () => {
            // Κλειδώνει το scroll στο body όταν ανοίγει
            document.documentElement.classList.add("mm-wrapper--fixed");
        });

        api.bind("close:finish", () => {
            btn?.classList.remove("active");
            document.documentElement.classList.remove("mm-wrapper--fixed");
        });

        document.addEventListener("click", (e) => {
            if (e.target.closest(".mm-close-custom")) {
                api.close();
                if (btn) btn.classList.remove("active");
            }
        });

    } catch (error) {
        console.error('Mmenu Error:', error);
    } finally {
        document.body.classList.remove('mmenu-loading');
    }
}

document.addEventListener("DOMContentLoaded", initMobileMenu);