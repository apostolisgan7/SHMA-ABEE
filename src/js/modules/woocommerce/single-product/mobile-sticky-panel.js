import { destroySmoothScroll, initSmoothScroll } from '../../../utils/smooth-scroll.js';

let _savedScrollY = 0;

function lockBodyScroll() {
    _savedScrollY = window.scrollY || window.pageYOffset;
    document.body.style.position = 'fixed';
    document.body.style.top      = `-${_savedScrollY}px`;
    document.body.style.left     = '0';
    document.body.style.right    = '0';
    document.body.style.overflow = 'hidden';
}

function unlockBodyScroll() {
    document.body.style.position = '';
    document.body.style.top      = '';
    document.body.style.left     = '';
    document.body.style.right    = '';
    document.body.style.overflow = '';
    window.scrollTo(0, _savedScrollY);
}

export function initMobileStickyPanel() {
    const BREAKPOINT = 992;

    const summary    = document.querySelector('.single_top_wrapper > .summary.entry-summary');
    const panel      = document.getElementById('rv-mobile-panel');
    const panelBody  = document.getElementById('rv-mobile-panel-body');
    const stickyBtn  = document.querySelector('.rv-mobile-sticky-btn');

    if (!summary || !panel || !panelBody || !stickyBtn) return;

    const overlay  = panel.querySelector('.rv-mobile-panel-overlay');
    const closeBtn = panel.querySelector('.rv-mobile-panel-close');

    let isOpen         = false;
    let summaryInPanel = false;

    const summaryParent      = summary.parentElement;
    const summaryNextSibling = summary.nextElementSibling;

    const isMobile = () => window.innerWidth <= BREAKPOINT;

    function moveSummaryToPanel() {
        if (summaryInPanel) return;
        panelBody.appendChild(summary);
        summaryInPanel = true;
    }

    function restoreSummary() {
        if (!summaryInPanel) return;
        if (summaryNextSibling) {
            summaryParent.insertBefore(summary, summaryNextSibling);
        } else {
            summaryParent.appendChild(summary);
        }
        summaryInPanel = false;
    }

    function openPanel() {
        // Move summary into panel only now (not on page load)
        // so WooCommerce/Alpine init happens in the correct DOM location
        moveSummaryToPanel();

        destroySmoothScroll();
        lockBodyScroll();

        panel.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
        stickyBtn.setAttribute('aria-expanded', 'true');
        stickyBtn.querySelector('span').textContent = 'Κλείσιμο';
        isOpen = true;
    }

    function closePanel() {
        stickyBtn.focus(); // return focus before aria-hidden is set
        panel.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
        stickyBtn.setAttribute('aria-expanded', 'false');
        stickyBtn.querySelector('span').textContent = 'Αίτηση Προσφοράς';
        unlockBodyScroll();
        initSmoothScroll();

        // Move summary back so it stays in its original DOM location
        restoreSummary();
        isOpen = false;
    }

    stickyBtn.addEventListener('click', () => {
        isOpen ? closePanel() : openPanel();
    });

    overlay?.addEventListener('click', closePanel);
    closeBtn?.addEventListener('click', closePanel);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) closePanel();
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (!isMobile() && isOpen) closePanel();
        }, 100);
    });
}
