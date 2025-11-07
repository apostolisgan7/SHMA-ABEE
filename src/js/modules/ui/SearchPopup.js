/**
 * SearchPopup (Custom integration with FiboSearch)
 * Handles:
 * - Popup open/close
 * - FiboSearch live results within custom container
 * - DOM healing between openings
 */
export default class SearchPopup {
    constructor() {
        this.button = document.querySelector('.search-toggle');
        this.popup = document.querySelector('.search-popup');
        this.overlay = document.querySelector('.search-overlay');
        this.closeBtn = this.popup?.querySelector('.search-close');
        this.resultsContainer = this.popup?.querySelector('#rv-fibo-results');
        this.input = this.popup?.querySelector('.dgwt-wcas-search-input');
        this.observer = null;

        if (!this.button || !this.popup) return;

        this.activeClass = 'active';
        this.init();
    }

    init() {
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            this.openPopup();
        });

        this.closeBtn?.addEventListener('click', () => this.closePopup());
        this.overlay?.addEventListener('click', () => this.closePopup());

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closePopup();
        });
    }

    openPopup() {
        this.popup.classList.add(this.activeClass);
        this.overlay?.classList.add(this.activeClass);
        document.body.classList.add('search-active');
        this.popup.setAttribute('aria-hidden', 'false');

        this.input = this.popup.querySelector('.dgwt-wcas-search-input');
        setTimeout(() => this.input?.focus(), 150);
        this.bindFiboObserver();
    }

    closePopup() {
        this.popup.classList.remove(this.activeClass);
        this.overlay?.classList.remove(this.activeClass);
        document.body.classList.remove('search-active');
        this.popup.setAttribute('aria-hidden', 'true');

        if (this.input) {
            this.input.dispatchEvent(new Event('keyup'));
            this.input.blur();
        }

        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
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
