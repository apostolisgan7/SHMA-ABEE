import gsap from 'gsap';

function showInlineError(msg, where) {
    if (!where) return;
    where.querySelectorAll('.woocommerce-error.sigma-inline, .field-error.sigma-inline').forEach(el => el.remove());

    const box = document.createElement('div');
    box.className = 'woocommerce-error sigma-inline';
    box.setAttribute('role', 'alert');
    box.innerHTML = `<li>${msg}</li>`;

    where.insertAdjacentElement('afterbegin', box);
    gsap.fromTo(box, { y: -6, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25, ease: 'power2.out' });
}

export function initValidation(overlay, { getPasswordStrength }) {
    overlay.addEventListener('click', e => {
        const submitBtn = e.target.closest('.sigma-auth-submit');
        if (!submitBtn) return;

        const form = submitBtn.closest('form');
        if (!form) return;

        e.preventDefault();

        const isRegister = form.classList.contains('js-ajax-register-form');
        if (!isRegister) { form.requestSubmit(); return; }

        form.querySelectorAll('.field-error').forEach(el => el.remove());
        form.querySelectorAll('.is-error').forEach(el => el.classList.remove('is-error'));

        let hasError = false;

        form.querySelectorAll('[required]').forEach(field => {
            if (field.offsetParent === null) return;
            if (!field.value.trim()) {
                hasError = true;
                field.classList.add('is-error');
                const row = field.closest('.form-row');
                if (row && !row.querySelector('.field-error')) {
                    const err = document.createElement('div');
                    err.className   = 'field-error';
                    err.textContent = 'Το πεδίο είναι υποχρεωτικό.';
                    row.appendChild(err);
                }
            }
        });

        const pass = form.querySelector('input[name="password"]');
        if (pass && getPasswordStrength(pass.value) < 3) {
            hasError = true;
            pass.classList.add('is-error');
            const row = pass.closest('.form-row');
            if (row && !row.querySelector('.field-error')) {
                const err = document.createElement('div');
                err.className   = 'field-error';
                err.textContent = 'Ο κωδικός πρέπει να είναι πιο ισχυρός.';
                row.appendChild(err);
            }
        }

        if (hasError) {
            showInlineError('Συμπλήρωσε σωστά όλα τα υποχρεωτικά πεδία.', form);
            return;
        }

        form.requestSubmit();
    });
}
