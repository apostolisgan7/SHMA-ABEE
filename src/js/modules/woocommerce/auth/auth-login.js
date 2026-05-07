import gsap from 'gsap';

export function initLogin(overlay, modal, loginForm, ajaxUrl) {
    if (!loginForm) return;

    loginForm.addEventListener('submit', async e => {
        e.preventDefault();

        loginForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());

        const formData = new FormData(loginForm);
        formData.append('action', 'sigma_login');
        formData.append('nonce', loginForm.querySelector('input[name="nonce"]')?.value || '');

        loginForm.classList.add('is-loading');

        try {
            const res  = await fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData });
            const data = await res.json();

            if (!data.success) {
                loginForm.classList.remove('is-loading');

                const emailField = loginForm.querySelector('#username')?.closest('.form-row');
                if (emailField) {
                    emailField.insertAdjacentHTML('afterend', data.data.html);
                    const errorBox = emailField.nextElementSibling;
                    gsap.fromTo(errorBox, { y: -6, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25, ease: 'power2.out' });
                    loginForm.querySelector('#username')?.focus();
                    gsap.fromTo(emailField, { x: 0 }, { x: -5, repeat: 4, yoyo: true, duration: 0.05 });
                }
                return;
            }

            gsap.to(modal, {
                scale: 0.96,
                autoAlpha: 0,
                duration: 0.35,
                ease: 'power2.inOut',
                onComplete() { window.location.href = data.data.redirect; },
            });
        } catch (err) {
            console.error('AJAX Login error:', err);
            loginForm.classList.remove('is-loading');
        }
    });
}
