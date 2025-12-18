export function shopHeader() {
    return {
        view: localStorage.getItem('shop_view') || 'grid',
        filtersHidden: document.body.classList.contains('shop-filters-hidden'),

        init() {
            document.body.classList.add(`shop-view-${this.view}`);
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
        },

        // 🔹 Labels
        get filtersLabel() {
            return this.filtersHidden
                ? 'Εμφάνιση Φίλτρων'
                : 'Απόκρυψη Φίλτρων';
        },

        get viewLabel() {
            return this.view === 'grid'
                ? 'Προβολή σε Grid'
                : 'Προβολή σε Λίστα';
        }
    }
}
