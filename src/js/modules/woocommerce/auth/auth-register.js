import gsap from 'gsap';

export function initRegister(overlay, modal, registerForm, ajaxUrl) {
    if (!registerForm) return;

    registerForm.addEventListener('submit', async e => {
        e.preventDefault();

        registerForm.querySelectorAll('.woocommerce-error, .field-error').forEach(el => el.remove());
        registerForm.classList.add('is-loading');

        const formData = new FormData(registerForm);
        formData.append('action', 'sigma_register');
        formData.append('nonce', registerForm.querySelector('input[name="nonce"]')?.value || '');

        try {
            const res  = await fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData });
            const data = await res.json();

            registerForm.classList.remove('is-loading');

            if (!data.success) {
                registerForm.insertAdjacentHTML('afterbegin', data.data.html);

                const temp = document.createElement('div');
                temp.innerHTML = data.data.html;

                temp.querySelectorAll('li').forEach(li => {
                    const msg = (li.textContent || '').toLowerCase();
                    let field = null;
                    if (msg.includes('τηλέφωνο'))  field = registerForm.querySelector('input[name="phone"]');
                    if (msg.includes('επωνυμία'))   field = registerForm.querySelector('input[name="company_name"]');
                    if (msg.includes('αφμ'))        field = registerForm.querySelector('input[name="vat"]');

                    if (field) {
                        const row = field.closest('.form-row');
                        row?.querySelectorAll('.field-error').forEach(el => el.remove());
                        const error = document.createElement('div');
                        error.className   = 'field-error';
                        error.textContent = li.textContent;
                        row?.appendChild(error);
                        gsap.fromTo(error, { y: -4, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25 });
                    }
                });

                registerForm.querySelector('.woocommerce-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (data.data.redirect) {
                gsap.to(modal, {
                    scale: 0.96,
                    autoAlpha: 0,
                    duration: 0.35,
                    ease: 'power2.inOut',
                    onComplete() { window.location.href = data.data.redirect; },
                });
            } else {
                // company/municipality pending approval — show success message
                registerForm.innerHTML = data.data.html;
                modal.scrollTop = 0;
            }
        } catch (err) {
            console.error('AJAX Register error:', err);
            registerForm.classList.remove('is-loading');
        }
    });
}
