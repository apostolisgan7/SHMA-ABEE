
function lockScroll() {
    document.documentElement.classList.add('menu-open');
    document.body.classList.add('menu-open');

    if (window.lenis) {
        window.lenis.stop();
    }
}

function unlockScroll() {
    document.documentElement.classList.remove('menu-open');
    document.body.classList.remove('menu-open');

    if (window.lenis) {
        window.lenis.start();
    }
}




function initMobileMenu() {

    // Add js class to html element
    document.documentElement.classList.add('js');
    
    const menu = document.querySelector("#menu");
    if (!menu) {
        console.log("Menu #menu not found");
        return;
    }
    
    // Add loading class to body
    document.body.classList.add('mmenu-loading');
    
    // Add class to indicate Mmenu is initializing
    menu.classList.add('mmenu-initializing');
    
    // Small delay to ensure CSS transitions work properly
    setTimeout(() => {
        try {
            // Remove initializing class
            menu.classList.remove('mmenu-initializing');
            
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
                            `<a href='#'><img src='/wp-content/themes/Ruined/src/img/icons/phone-cal.svg' alt='phone'></a>`,
                            `<a href='#'><img src='/wp-content/themes/Ruined/src/img/icons/info-circle.svg' alt='info'></a>`
                        ]
                    }
                ]
            });

            const api = mmenu.API;

            api.bind("open:start", () => {
                lockScroll();
            });

            api.bind("close:finish", () => {
                unlockScroll();
                if (btn) btn.classList.remove("active");
            });


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


            document.body.classList.remove('mmenu-loading');
            
        } catch (error) {
            console.error('Error initializing mobile menu:', error);
            // If there's an error, make sure to show the menu anyway
            menu.classList.remove('mmenu-initializing');
            document.body.classList.remove('mmenu-loading');
        }
    }, 100); // 100ms delay to ensure CSS transitions work properly
}

document.addEventListener("DOMContentLoaded", initMobileMenu);
