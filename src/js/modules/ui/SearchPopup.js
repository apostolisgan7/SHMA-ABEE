import { gsap } from "gsap";

export default class SearchPopup {
    constructor() {
        this.buttons = document.querySelectorAll('.search-toggle');
        this.popup = document.querySelector('.search-popup');
        this.overlay = document.querySelector('.search-overlay');
        this.closeBtn = this.popup?.querySelector('.search-close');
        this.resultsContainer = this.popup?.querySelector('#rv-fibo-results');
        this.wrapper = this.popup?.querySelector('.search-results-wrapper');
        this.container = this.popup?.querySelector('.search-container');
        this.input = null;
        this.observer = null;
        this.stateChecker = null;
        this._onInput = null;

        if (this.buttons.length === 0 || !this.popup) return;

        this.activeClass = 'active';
        this.tl = gsap.timeline({ paused: true });

        this.init();
        this.buildTimeline();
    }

    init() {
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

        document.addEventListener('click', (e) => {
            const link = e.target.closest('#rv-default-products .dgwt-wcas-suggestion');

            if (link) {
                e.stopImmediatePropagation();
            }
        });
    }

    resetUI() {
        if (!this.wrapper) return;

        // 1. Επιστροφή στο Default State
        this.wrapper.classList.remove('search-has-query');

        const titleElement = this.popup.querySelector('#rv-search-title');
        const defaultTitle = titleElement?.dataset.defaultTitle ?? '';
        if (titleElement && titleElement.textContent.trim() !== defaultTitle) {
            gsap.to(titleElement, {
                opacity: 0, y: -5, duration: 0.2,
                onComplete: () => {
                    titleElement.textContent = defaultTitle;
                    gsap.to(titleElement, { opacity: 1, y: 0, duration: 0.25 });
                }
            });
        }

        // 2. ΚΡΙΣΙΜΟ: Αντί για innerHTML = '', μετακινούμε τα αποτελέσματα στο body
        // ώστε η Fibo να μπορεί να τα ξαναβρεί/καθαρίσει μόνη της
        const suggestions = this.resultsContainer?.querySelector('.dgwt-wcas-suggestions-wrapp');
        if (suggestions) {
            suggestions.style.display = 'none'; // Τα κρύβουμε
            document.body.appendChild(suggestions); // Τα επιστρέφουμε στο body
        }
    }

    openPopup() {
        this.popup.classList.add(this.activeClass);
        this.popup.setAttribute('aria-hidden', 'false');
        this.overlay?.classList.add(this.activeClass);
        document.body.classList.add('search-active');
        window.__lenis__?.stop();
        document.documentElement.classList.add('scroll-locked');
        this.tl.play();

        this.input = this.popup.querySelector('.dgwt-wcas-search-input');

        if (this.input) {
            this.tl.then(() => this.input.focus());

            this._onInput = () => this.handleInputState();
            this.input.addEventListener('input', this._onInput);

            if (this.stateChecker) clearInterval(this.stateChecker);
            this.stateChecker = setInterval(() => {
                if (this.input.value.trim().length === 0 && this.wrapper.classList.contains('search-has-query')) {
                    this.resetUI();
                }
            }, 200);
        }

        this.bindFiboObserver();
    }

    closePopup() {
        this.tl.reverse().then(() => {
            this.popup.classList.remove(this.activeClass);
            this.popup.setAttribute('aria-hidden', 'true');
            this.overlay?.classList.remove(this.activeClass);
            document.body.classList.remove('search-active');
            window.__lenis__?.start();
            document.documentElement.classList.remove('scroll-locked');

            if (this.stateChecker) clearInterval(this.stateChecker);

            if (this.input) {
                if (this._onInput) {
                    this.input.removeEventListener('input', this._onInput);
                    this._onInput = null;
                }
                this.input.value = '';
                this.resetUI();
            }
            if (this.observer) this.observer.disconnect();
        });
    }

    handleInputState() {
        if (!this.input || !this.wrapper) return;

        const titleElement  = this.popup.querySelector('#rv-search-title');
        const hasQuery      = this.input.value.trim().length > 0;
        const currentTitle  = titleElement?.textContent.trim();
        const defaultTitle  = titleElement?.dataset.defaultTitle  ?? '';
        const resultsTitle  = titleElement?.dataset.resultsTitle  ?? '';
        const newTitle      = hasQuery ? resultsTitle : defaultTitle;

        if (titleElement && currentTitle !== newTitle) {
            gsap.to(titleElement, {
                opacity: 0, y: -5, duration: 0.2,
                onComplete: () => {
                    titleElement.textContent = newTitle;
                    gsap.to(titleElement, { opacity: 1, y: 0, duration: 0.25 });
                }
            });
        }

        if (hasQuery) {
            this.wrapper.classList.add('search-has-query');
        } else {
            this.resetUI();
        }
    }

    bindFiboObserver() {
        // Find the FiboSearch container or create a target
        const fiboContainer = document.querySelector('.dgwt-wcas-suggestions-wrapp')?.parentElement || 
                           document.querySelector('.dgwt-wcas-search-wrapp') ||
                           this.popup;
        
        this.observer = new MutationObserver((mutations) => {
            const suggestions = document.querySelector('.dgwt-wcas-suggestions-wrapp');
            if (suggestions && suggestions.childNodes.length > 0) {
                this.moveResults();
            }
        });
        
        this.observer.observe(fiboContainer, {
            childList: true, 
            subtree: true,
            attributes: false,
            characterData: false
        });
    }

    moveResults() {
        const suggestions = document.querySelector('.dgwt-wcas-suggestions-wrapp');
        if (!suggestions || !this.resultsContainer) return;

        if (suggestions.parentNode !== this.resultsContainer) {
            this.resultsContainer.innerHTML = '';
            suggestions.removeAttribute('style');
            suggestions.classList.add('rv-search-results-custom');
            this.resultsContainer.appendChild(suggestions);
        }

        const items = suggestions.querySelectorAll('.dgwt-wcas-suggestion-product');
        const newItems = [];

        items.forEach(item => {
            if (item.classList.contains('rv-processed')) return;

            const stContainer = item.querySelector('.dgwt-wcas-st');
            if (stContainer) {
                stContainer.insertAdjacentHTML('beforeend', `
                    <div class="dgwt-wcas-arrow-wrapper">
                        <div class="button-arrow button-arrow--black">
                            <span class="button-arrow__icon">
                                <span class="button-arrow__arrow button-arrow__arrow--front"></span>
                                <span class="button-arrow__arrow button-arrow__arrow--back"></span>
                                <span class="button-arrow__fill"></span>
                            </span>
                        </div>
                    </div>`);
            }

            // 2. Trim & Widow Fix για τον τίτλο
            const titleSpan = item.querySelector('.dgwt-wcas-st-title');
            if (titleSpan) {
                let titleText = titleSpan.innerText.trim();
                const limit = 25; // Ορίζεις εδώ το όριο χαρακτήρων που θέλεις

                // Α. Trim (Περικοπή αν είναι μεγάλο)
                if (titleText.length > limit) {
                    titleText = titleText.substring(0, limit).trim() + '...';
                }

                // Β. Widow Fix (PHP-like logic με &nbsp;)
                const words = titleText.split(' ');
                if (words.length > 1) {
                    const lastWord = words.pop();
                    const mainText = words.join(' ');
                    titleSpan.innerHTML = `${mainText}&nbsp;${lastWord}`;
                } else {
                    titleSpan.textContent = titleText;
                }
            }

            gsap.set(item, { opacity: 0, y: 10 });
            item.classList.add('rv-processed');
            newItems.push(item);
        });

        if (newItems.length > 0) {
            gsap.to(newItems, {
                opacity: 1, y: 0, duration: 0.6, stagger: 0.05, ease: "power2.out", clearProps: "all"
            });
        }
    }

    buildTimeline() {
        const headerChildren  = this.popup.querySelectorAll('.search-header > *');
        const searchForm      = this.popup.querySelector('.dgwt-wcas-search-form');
        const searchTags      = [...this.popup.querySelectorAll('.search-tag')];
        const searchHelp      = this.popup.querySelector('.search-help');
        const sectionTitle    = this.popup.querySelector('.section-title');
        const defaultProducts = this.popup.querySelector('#rv-default-products');

        gsap.set(this.overlay, { autoAlpha: 0 });
        gsap.set(this.popup, { autoAlpha: 0 });
        gsap.set(this.container, { y: 20 });
        gsap.set([...headerChildren, searchForm, ...searchTags, searchHelp, sectionTitle, defaultProducts], { opacity: 0, y: 10 });

        this.tl.to(this.overlay, { opacity: 1, duration: 0.25 }, 0)
            .to(this.popup, { autoAlpha: 1, duration: 0.2 }, 0)
            .to(this.container, { y: 0, opacity: 1, duration: 0.4 }, 0.1)
            .to(headerChildren, { opacity: 1, y: 0, stagger: 0.05, duration: 0.3 }, 0.2)
            .to(searchForm, { opacity: 1, y: 0, duration: 0.3 }, 0.25);

        if (searchTags.length) {
            this.tl.to(searchTags, { y: 0, opacity: 1, stagger: 0.05, duration: 0.3 }, 0.3);
        }

        this.tl.to([searchHelp, sectionTitle, defaultProducts], { opacity: 1, y: 0, stagger: 0.08, duration: 0.4 }, 0.4);
    }
}