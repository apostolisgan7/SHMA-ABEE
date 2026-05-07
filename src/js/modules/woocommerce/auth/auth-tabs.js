import gsap from 'gsap';

export function initTabs(overlay, { refreshPasswordElsAndBind, updatePasswordMeter }) {
    const roleBtns   = overlay.querySelectorAll('.sigma-auth-role-btn');
    const rolesInner = overlay.querySelector('.sigma-auth-roles-inner');
    const pill       = overlay.querySelector('.sigma-auth-roles-pill');
    const roleInputs = overlay.querySelectorAll('.js-auth-role-input');

    function syncFields(role) {
        const customer = overlay.querySelector('.js-customer-name-field');
        const company  = overlay.querySelector('.js-company-name-field');
        const muni     = overlay.querySelector('.js-municipality-name-field');
        const vat      = overlay.querySelector('.js-vat-field');

        if (customer) customer.style.display = role === 'customer' ? '' : 'none';
        if (company)  company.style.display  = role === 'company'  ? '' : 'none';
        if (muni)     muni.style.display     = role === 'municipality' ? '' : 'none';
        if (vat)      vat.style.display      = (role === 'company' || role === 'municipality') ? '' : 'none';
    }

    function movePillToButton(btn, animate = true) {
        if (!pill || !rolesInner || !btn) return;
        const wrapRect = rolesInner.getBoundingClientRect();
        const btnRect  = btn.getBoundingClientRect();
        const padding  = 4;
        gsap.to(pill, {
            x: btnRect.left - wrapRect.left - padding,
            width: btnRect.width,
            duration: animate ? 0.32 : 0,
            ease: 'power3.out',
            overwrite: true,
        });
    }

    function setInitialPillPosition() {
        const activeBtn = overlay.querySelector('.sigma-auth-role-btn.is-active') || roleBtns[0];
        if (activeBtn) movePillToButton(activeBtn, false);
    }

    // initial role sync
    const initialBtn = overlay.querySelector('.sigma-auth-role-btn.is-active') || roleBtns[0];
    if (initialBtn?.dataset?.role) syncFields(initialBtn.dataset.role);

    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const role = btn.dataset.role;
            roleBtns.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            roleInputs.forEach(input => (input.value = role));
            syncFields(role);
            movePillToButton(btn, true);
            setTimeout(() => {
                refreshPasswordElsAndBind();
                updatePasswordMeter();
            }, 50);
        });
    });

    return { setInitialPillPosition };
}
