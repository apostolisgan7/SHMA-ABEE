const MOBILE_BREAKPOINT = 993;

export function initArchiveHeaderFiltersToggle() {
    const trigger = document.querySelector('.archive-header__filters');
    const modal = document.querySelector('.yith-wcan-filters');

    if (!trigger || !modal) return;

    trigger.addEventListener('click', (e) => {
        if (window.innerWidth > MOBILE_BREAKPOINT) return;

        e.preventDefault();

        // open YITH modal
        modal.classList.add('open', 'is-open');
        document.body.classList.add('filters-modal-open');
    });
}

export function ensureYithModalInBody() {
    if (window.innerWidth > MOBILE_BREAKPOINT) {
        return null;
    }

    const modal = document.querySelector('.yith-wcan-filters');

    if (!modal) {
        return null;
    }

    // Αν δεν είναι ήδη child του body → μετακίνησέ το
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    console.log('[YITH] Filters moved to body (mobile)', modal);
    return modal;
}

// 🔹 GLOBAL modal close handler (MOBILE ONLY)
export function initYithModalCloseHandler() {
    document.addEventListener('click', (e) => {
        if (window.innerWidth > MOBILE_BREAKPOINT) return;

        if (
            e.target.closest('.yith-wcan-filters .close-button') ||
            e.target.closest('.yith-wcan-filters .apply-filters')
        ) {
            const modal = document.querySelector('.yith-wcan-filters');
            if (!modal) return;

            modal.classList.remove('open', 'is-open');
            document.body.classList.remove('filters-modal-open');
        }
    });
}

// 🔹 INIT
export function initFilters() {
    ensureYithModalInBody();
    initYithModalCloseHandler();
    initArchiveHeaderFiltersToggle();

}
