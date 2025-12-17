import { gsap } from "gsap";

/**
 * SearchPopup (Custom integration with FiboSearch)
 * Handles:
 * - Popup open/close with GSAP animation
 * - FiboSearch live results within custom container (moveResults)
 * - Dynamic view state based on search input
 */
export default class SearchPopup {
    constructor() {
        // Get all search toggle buttons (both mobile and desktop)
        this.buttons = document.querySelectorAll('.search-toggle');
        this.popup = document.querySelector('.search-popup');
        this.overlay = document.querySelector('.search-overlay');
        this.closeBtn = this.popup?.querySelector('.search-close');

        this.resultsContainer = this.popup?.querySelector('#rv-fibo-results');
        this.defaultContainer = this.popup?.querySelector('#rv-default-products');
        this.wrapper = this.popup?.querySelector('.search-results-wrapper');
        this.container = this.popup?.querySelector('.search-container');
        this.content = this.popup?.querySelector('.search-content');
        this.sidebar = this.popup?.querySelector('.search-help');

        this.input = null;
        this.observer = null;

        if (this.buttons.length === 0 || !this.popup) return;

        this.activeClass = 'active';
        this.tl = gsap.timeline({ paused: true });

        this.init();
        this.buildTimeline();
    }

    init() {
        // Add click event to all search toggle buttons
        this.buttons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.openPopup();
            });
        });

        this.closeBtn?.addEventListener('click', () => this.closePopup());
        this.overlay?.addEventListener('click', () => this.closePopup());

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closePopup();
        });
    }

    // Μέθοδος που ορίζει το Animation Sequence
    buildTimeline() {
        // Καθορίζουμε την αρχική κατάσταση (SET)
        gsap.set(this.overlay, { autoAlpha: 0 });
        gsap.set(this.popup, { autoAlpha: 0 });
        gsap.set(this.container, { y: 20 });

        // Κρύβουμε όλα τα παιδιά του container για να τα κάνουμε stagger
        gsap.set([
            '.search-header > *',
            '.dgwt-wcas-search-form', // Εξασφαλίζουμε ότι ο form είναι κρυμμένος
            '.search-tag',
            '.search-help',
            '.section-title', // Προσθήκη του τίτλου ενότητας
            '#rv-default-products'
        ], { opacity: 0, y: 10 });


        // 1. OVERLAY & POPUP WRAPPER
        this.tl.to(this.overlay, {
            opacity: 1,
            duration: 0.25,
            ease: "power2.out"
        }, 0)
            .to(this.popup, {
                autoAlpha: 1,
                duration: 0.2
            }, 0)

            // 2. SEARCH CONTAINER (Εμφάνιση και Ανέβασμα)
            .to(this.container, {
                y: 0,
                opacity: 1,
                duration: 0.4,
                ease: "power3.out"
            }, 0.1)

            // 3. HEADER ANIMATION
            // Eμφάνιση των στοιχείων της κεφαλίδας (site-logo, search-close)
            .to('.search-header > *', {
                opacity: 1,
                y: 0,
                stagger: 0.05,
                duration: 0.3,
                ease: "power2.out"
            }, 0.2)

            // 4. SEARCH FORM, INPUT & TAGS
            .to('.dgwt-wcas-search-form', { // Εμφάνιση του form container
                opacity: 1,
                y: 0,
                duration: 0.3
            }, 0.25)
            .to(".search-tag", { // Tags κάτω από το input
                y: 0,
                opacity: 1,
                stagger: 0.05,
                duration: 0.3,
                ease: "power2.out"
            }, 0.3)

            // 5. CONTENT (Sidebar, Section Title & Default Products)
            .to(['.search-help', '.section-title', '#rv-default-products'], {
                opacity: 1,
                y: 0,
                stagger: 0.08, // Ελαφρύ stagger ανάμεσα στα στοιχεία
                duration: 0.4,
                ease: "power2.out"
            }, 0.4); // Start time
    }

    // --- ΟΛΕΣ ΟΙ ΑΛΛΕΣ ΜΕΘΟΔΟΙ ΠΑΡΑΜΕΝΟΥΝ ΟΠΩΣ ΠΡΙΝ ---

    openPopup() {
        this.popup.classList.add(this.activeClass);
        this.overlay?.classList.add(this.activeClass);
        document.body.classList.add('search-active');
        this.popup.setAttribute('aria-hidden', 'false');

        this.input = this.popup.querySelector('.dgwt-wcas-search-input');

        this.tl.play();

        if (this.input) {
            // Χρησιμοποιούμε setTimeout για να εστιάσουμε ΜΟΝΟ αφού τελειώσει το GSAP animation του input
            this.tl.then(() => {
                if(this.input) {
                    this.input.focus();
                }
            });

            this.input.addEventListener('keyup', this.handleInputState.bind(this));
            this.input.addEventListener('input', this.handleInputState.bind(this));

            const clearBtn = this.popup.querySelector('.dgwt-wcas-search-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', this.handleClearClick.bind(this));
            }
            this.handleInputState();
        }

        this.bindFiboObserver();
    }

    closePopup() {
        this.tl.reverse().then(() => {
            this.popup.classList.remove(this.activeClass);
            this.overlay?.classList.remove(this.activeClass);
            document.body.classList.remove('search-active');
            this.popup.setAttribute('aria-hidden', 'true');

            if (this.input) {
                this.input.value = '';
                this.input.blur();
                this.wrapper?.classList.remove('search-has-query');

                this.input.removeEventListener('keyup', this.handleInputState.bind(this));
                this.input.removeEventListener('input', this.handleInputState.bind(this));

                const clearBtn = this.popup.querySelector('.dgwt-wcas-search-clear');
                if (clearBtn) {
                    clearBtn.removeEventListener('click', this.handleClearClick.bind(this));
                }
            }
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
        });
    }

    handleClearClick() {
        setTimeout(() => {
            this.handleInputState();
        }, 50);
    }

    handleInputState() {
        if (!this.input || !this.wrapper) return;

        if (this.input.value.trim().length > 0) {
            this.wrapper.classList.add('search-has-query');
        } else {
            this.wrapper.classList.remove('search-has-query');
        }
    }

    bindFiboObserver() {
        const target = document.querySelector('.dgwt-wcas-sf-wrapp');
        if (!target) return;
        if (this.observer) this.observer.disconnect();

        this.observer = new MutationObserver(() => {
            const suggestions = document.querySelector('.dgwt-wcas-suggestions-wrapp');
            if (suggestions && suggestions.parentNode !== this.resultsContainer) {
                this.moveResults();
            }
        });

        this.observer.observe(target, { childList: true, subtree: true });
    }

    moveResults() {
        const suggestions = document.querySelector('.dgwt-wcas-suggestions-wrapp');
        if (suggestions && this.resultsContainer) {
            this.resultsContainer.innerHTML = '';
            suggestions.removeAttribute('style');
            suggestions.classList.add('rv-search-results-custom');
            this.resultsContainer.appendChild(suggestions);
        }
    }
}