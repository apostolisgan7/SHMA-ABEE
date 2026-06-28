import gsap from 'gsap';
import { Flip } from 'gsap/Flip';
gsap.registerPlugin(Flip);

function flipTo5Cols() {
    const cards = document.querySelectorAll('.shop_content ul.products li.product');
    if (!cards.length) return;
    const state = Flip.getState(cards);
    document.body.classList.add('shop-filters-5col');
    Flip.from(state, {
        duration: 0.45,
        ease: 'power2.inOut',
        stagger: { amount: 0.12, from: 'start' },
    });
}

function flipFrom5Cols() {
    const cards = document.querySelectorAll('.shop_content ul.products li.product');
    if (!cards.length) {
        document.body.classList.remove('shop-filters-5col');
        return;
    }
    const state = Flip.getState(cards);
    document.body.classList.remove('shop-filters-5col');
    Flip.from(state, {
        duration: 0.35,
        ease: 'power2.out',
        stagger: { amount: 0.08, from: 'end' },
    });
}

// 🔹 Alpine component
export function shopHeader() {
    return {
        view: localStorage.getItem('shop_view') || 'grid',
        filtersHidden: document.body.classList.contains('shop-filters-hidden'),

        init() {
            document.body.classList.add(`shop-view-${this.view}`);
            if (this.filtersHidden) {
                document.body.classList.add('shop-filters-5col');
            }
        },

        setView(type) {
            document.body.classList.remove(`shop-view-${this.view}`);
            this.view = type;
            document.body.classList.add(`shop-view-${type}`);
            localStorage.setItem('shop_view', type);
        },

        toggleFilters() {
            this.filtersHidden = !this.filtersHidden;
            document.body.classList.toggle('shop-filters-hidden');

            clearTimeout(this._colTimer);
            if (this.filtersHidden) {
                // Wait for content to finish expanding, then Flip cards to 5 cols
                this._colTimer = setTimeout(flipTo5Cols, 480);
            } else {
                // Flip cards back immediately (before content starts shrinking)
                flipFrom5Cols();
            }
        },

        handleFiltersClick() {
            // Mobile modal is handled entirely by filters.js (initArchiveHeaderFiltersToggle)
            if (window.innerWidth <= 993) return;
            this.toggleFilters();
        },

        get filtersLabel() {
            return this.filtersHidden ? 'Εμφάνιση Φίλτρων' : 'Απόκρυψη Φίλτρων';
        },

        get viewLabel() {
            return this.view === 'grid'
                ? 'Προβολή σε Grid'
                : 'Προβολή σε Λίστα';
        }
    };
}
