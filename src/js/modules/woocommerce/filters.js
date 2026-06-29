const MOBILE_BREAKPOINT = 993;

function moveModalToBody() {
    if (window.innerWidth > MOBILE_BREAKPOINT) return;
    const inSidebar = document.querySelector('.yith-wcan-filters:not(body > .yith-wcan-filters)');
    if (!inSidebar) return;
    document.querySelectorAll('body > .yith-wcan-filters').forEach(el => {
        if (el !== inSidebar) el.remove();
    });
    document.body.appendChild(inSidebar);
}

export function initArchiveHeaderFiltersToggle() {
    document.addEventListener('click', (e) => {
        if (window.innerWidth > MOBILE_BREAKPOINT) return;
        if (!e.target.closest('.archive-header__filters')) return;
        e.preventDefault();

        const modal = document.querySelector('.yith-wcan-filters');
        if (!modal) return;

        modal.style.removeProperty('display');
        modal.classList.add('open', 'is-open');
        document.body.classList.add('filters-modal-open');
    });
}

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

let _yithInitCount = 0;

export function initFilters() {
    moveModalToBody();
    initYithModalCloseHandler();
    initArchiveHeaderFiltersToggle();

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('yith_wcan_init_shortcodes', () => {
            _yithInitCount++;
            if (_yithInitCount === 1) return;

            setTimeout(() => {
                moveModalToBody();
            }, 300);
        });
    }

    window.addEventListener('pageshow', (e) => {
        if (e.persisted) moveModalToBody();
    });
}
