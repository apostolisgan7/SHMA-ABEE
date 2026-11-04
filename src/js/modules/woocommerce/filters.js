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

export function initProductCatExclusiveFilter() {
    // YITH tracks active state via .active class on .filter-item, not checkbox.checked.
    // mergeProperties() joins active terms from all blocks with '+' — we prevent that
    // by clearing .active on other product_cat blocks before YITH reads the DOM.
    document.addEventListener('click', (e) => {
        const link = e.target.closest('.yith-wcan-filter[data-taxonomy="product_cat"] label > a');
        if (!link) return;

        const clickedBlock = link.closest('.yith-wcan-filter');
        const preset = link.closest('.yith-wcan-filters');
        if (!clickedBlock || !preset) return;

        preset.querySelectorAll('.yith-wcan-filter[data-taxonomy="product_cat"]').forEach(block => {
            if (block === clickedBlock) return;
            block.querySelectorAll('.filter-item.active').forEach(item => {
                item.classList.remove('active');
                const cb = item.querySelector('input[type="checkbox"]');
                if (cb) {
                    cb.checked = false;
                    cb.closest('.checked')?.classList.remove('checked');
                }
            });
        });
    }, true);
}

// The current category's own filter item starts closed; open it (children stay closed)
// so its sub-categories are visible. Same classes + inline display YITH's toggle uses.
function openActiveFilterCategories() {
    document.querySelectorAll('.yith-wcan-filter[data-taxonomy="product_cat"] li.filter-item.active.hierarchy-collapsable.closed').forEach(li => {
        const list = li.querySelector(':scope > ul');
        if (!list) return;
        li.classList.replace('closed', 'opened');
        list.style.display = 'block';
    });
}

let _yithInitCount = 0;

export function initFilters() {
    moveModalToBody();
    initYithModalCloseHandler();
    initArchiveHeaderFiltersToggle();
    // YITH sets its own open/closed state (and may re-render the filters) while it
    // initialises, so keep re-applying for the first few seconds
    openActiveFilterCategories();
    const filtersObserver = new MutationObserver(openActiveFilterCategories);
    filtersObserver.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
    setTimeout(() => filtersObserver.disconnect(), 4000);

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('yith_wcan_init_shortcodes', () => {
            openActiveFilterCategories();
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
