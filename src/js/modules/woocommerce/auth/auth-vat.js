import gsap from 'gsap';

export function initVat(overlay, ajaxUrl) {
    let vatTimer = null;
    let vatInProgress = false;

    overlay.addEventListener('blur', e => {
        const vatInput = e.target.closest('input[name="vat"]');
        if (!vatInput) return;

        const role = overlay.querySelector('.js-auth-role-input')?.value || 'customer';
        clearTimeout(vatTimer);

        vatTimer = setTimeout(() => {
            if (vatInProgress) return;
            vatInProgress = true;

            const row  = vatInput.closest('.form-row');
            const form = vatInput.closest('form');
            const vat  = vatInput.value.trim();

            if (!vat || !row || vatInput.offsetParent === null) {
                vatInProgress = false;
                return;
            }

            const nonce = form?.querySelector('input[name="nonce"]')?.value;
            if (!nonce) { vatInProgress = false; return; }

            row.querySelectorAll('.field-error, .field-success').forEach(el => el.remove());
            vatInput.setAttribute('aria-invalid', 'false');
            row.classList.add('is-checking');

            fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'sigma_check_vat', vat, role, nonce }),
            })
                .then(res => res.json())
                .then(data => {
                    row.classList.remove('is-checking');
                    vatInProgress = false;

                    const ok  = !!data?.success;
                    const msg = document.createElement('div');
                    msg.className   = ok ? 'field-success' : 'field-error';
                    msg.textContent = data?.data?.message || (ok ? 'OK' : 'Σφάλμα ελέγχου ΑΦΜ');

                    vatInput.setAttribute('aria-invalid', ok ? 'false' : 'true');
                    row.appendChild(msg);
                    gsap.fromTo(msg, { y: -4, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25 });
                })
                .catch(() => {
                    row.classList.remove('is-checking');
                    vatInProgress = false;
                });
        }, 250);
    }, true);
}
