// 🔹 GLOBAL modal close handler
document.addEventListener('click', (e) => {
    if (
        e.target.matches('.yith-wcan-filters .close-button') ||
        e.target.matches('.yith-wcan-filters .apply-filters')
    ) {
        const modal = e.target.closest('.yith-wcan-filters');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('is-open');
            document.body.classList.remove('filters-modal-open');
        }
    }
});

// 🔹 MOVE YITH MODAL TO BODY (GLOBAL)
function ensureYithModalInBody() {
    const modal = document.querySelector('.yith-wcan-filters.filters-modal');

    if (!modal) return null;

    // Αν δεν είναι ήδη child του body → μετακίνησέ το
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    return modal;
}




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
