document.addEventListener('alpine:init', () => {
    Alpine.data('shopSorting', () => ({
        open: false,
        selectEl: null,
        currentLabel: 'Ανά ημερομηνία',

        options: [
            { value: 'menu_order', label: 'Ανά προεπιλογή' },
            { value: 'date', label: 'Ανά ημερομηνία' },
            { value: 'price', label: 'Αύξουσα τιμή' },
            { value: 'price-desc', label: 'Φθίνουσα τιμή' },
            { value: 'popularity', label: 'Δημοφιλή' },
            { value: 'rating', label: 'Καλύτερη βαθμολογία' },
        ],

        init() {
            this.selectEl = document.querySelector('.woocommerce-ordering select');

            if (!this.selectEl) return;

            const current = this.selectEl.value;
            const found = this.options.find(o => o.value === current);
            if (found) this.currentLabel = found.label;
        },

        select(option) {
            this.currentLabel = option.label;
            this.open = false;

            if (!this.selectEl) return;

            this.selectEl.value = option.value;
            this.selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }));
});
