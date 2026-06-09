




// 🔹 Alpine component
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

            clearTimeout(this._colTimer);
            if (this.filtersHidden) {
                // Switch to 5 cols AFTER content finishes expanding (60ms delay + 400ms transition)
                this._colTimer = setTimeout(() => {
                    document.body.classList.add('shop-filters-5col');
                }, 480);
            } else {
                // Revert to 3 cols IMMEDIATELY so it's already 3 when content starts shrinking
                document.body.classList.remove('shop-filters-5col');
            }
        },

        handleFiltersClick() {
            // 📱 MOBILE → modal
            if (window.innerWidth < 768) {
                const modal = document.querySelector('.yith-wcan-filters.filters-modal');
                if (modal) {
                    modal.style.display = 'block';
                    modal.classList.add('is-open');
                    document.body.classList.add('filters-modal-open');
                }
                return;
            }

            // 🖥 DESKTOP → sidebar
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
