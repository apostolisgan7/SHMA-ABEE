/**
 * SearchPopup (Custom integration with FiboSearch)
 * Handles:
 * - Popup open/close
 * - FiboSearch live results within custom container
 * - DOM healing between openings
 */
export default class SearchPopup {
    constructor() {
        console.log('%c[SearchPopup] Initializing...', 'color:#4caf50');

        this.button = document.querySelector('.search-toggle');
        this.popup = document.querySelector('.search-popup');
        this.closeBtn = this.popup?.querySelector('.search-close');
        this.resultsContainer = this.popup?.querySelector('#rv-fibo-results');

        // Ensure input reference is correct (FiboSearch wraps it later)
        this.input = this.popup?.querySelector('.dgwt-wcas-search-input');

        // Mutation observer instance
        this.observer = null;

        if (!this.button || !this.popup) {
            console.error('❌ [SearchPopup] Required elements missing!');
            return;
        }

        this.activeClass = 'active';
        this.init();
    }

    /**
     * 🧠 Initialize event listeners
     */
    init() {
        // Open popup
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            this.openPopup();
        });

        // Close popup
        this.closeBtn?.addEventListener('click', () => this.closePopup());

        // ESC to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closePopup();
        });

        // Click overlay
        this.popup.addEventListener('click', (e) => {
            if (e.target === this.popup) this.closePopup();
        });
    }

    /**
     * ▶ Open Popup & ensure FiboSearch is ready
     */
    openPopup() {
        console.log('%c[SearchPopup] Opening popup...', 'color:#2196f3');
        this.popup.classList.add(this.activeClass);
        document.body.classList.add('search-active');
        this.popup.setAttribute('aria-hidden', 'false');

        // Re-establish input reference in case the DOM was manipulated
        this.input = this.popup.querySelector('.dgwt-wcas-search-input');

        // Focus the input
        setTimeout(() => this.input?.focus(), 150);

        // ✅ Rebind observer to catch results
        this.bindFiboObserver();
    }



    /**
     * ⏹ Close popup & reset state
     */
    closePopup() {
        console.log('%c[SearchPopup] Closing popup...', 'color:red');

        this.popup.classList.remove(this.activeClass);
        document.body.classList.remove('search-active');
        this.popup.setAttribute('aria-hidden', 'true');

        // Re-establish input reference
        this.input = this.popup.querySelector('.dgwt-wcas-search-input');

        // 🔥 1) Tell FiboSearch to hide/clear any floating suggestions internally
        if (this.input) {
            // We do NOT clear the value here, so the previous search term remains
            // this.input.value = '';
            this.input.dispatchEvent(new Event('keyup'));
            this.input.blur();
        }

        // 🔥 2) IMPORTANT: Do NOT clear the visual results container (to persist results)
        // this.clearResults();

        // 🔥 3) Disconnect observer
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
    }



    /**
     * 🔄 Set up MutationObserver to detect new results
     */
    bindFiboObserver() {
        // Target is the immediate wrapper of the FiboSearch input
        const target = document.querySelector('.dgwt-wcas-sf-wrapp');

        if (!target) {
            console.warn('[SearchPopup] Target wrapper for observer not found.');
            return;
        }

        // Disconnect previous if exists
        if (this.observer) {
            this.observer.disconnect();
        }

        // Watch for FiboSearch to insert its suggestions wrapper
        this.observer = new MutationObserver(() => {
            const suggestions = document.querySelector('.dgwt-wcas-suggestions-wrapp');
            if (suggestions && suggestions.parentNode !== this.resultsContainer) {
                console.log('%c[SearchPopup] Results detected and moving!', 'color:#ff9800');
                this.moveResults();
            }
        });

        this.observer.observe(target, { childList: true, subtree: true });
    }

    /**
     * 📦 Move plugin suggestions into custom results container
     */
    moveResults() {
        const suggestions = document.querySelector('.dgwt-wcas-suggestions-wrapp');

        if (suggestions && this.resultsContainer) {
            // Clear any previous content in the custom container
            this.resultsContainer.innerHTML = '';

            // Remove inline style that might conflict
            suggestions.removeAttribute('style');
            suggestions.classList.add('rv-search-results-custom');

            // Append the suggestions to your custom wrapper
            this.resultsContainer.appendChild(suggestions);
        } else {
            console.warn('[SearchPopup] moveResults() called but no suggestions found');
        }
    }

    /**
     * 🧹 Clear custom container
     */
    clearResults() {
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = '';
        }
    }
}