import gsap from 'gsap';

export function initLogin(overlay, modal, loginForm, ajaxUrl) {
    if (!loginForm) return;

    function showError(emailField, html) {
        emailField.nextElementSibling?.classList?.contains('woocommerce-error') && emailField.nextElementSibling.remove();
        emailField.insertAdjacentHTML('afterend', html);
        emailField.classList.add('has-error');

        const errorBox = emailField.nextElementSibling;
        gsap.fromTo(errorBox, { y: -6, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25, ease: 'power2.out' });
        gsap.fromTo(emailField, { x: 0 }, { x: -5, repeat: 4, yoyo: true, duration: 0.05 });

        const input = emailField.querySelector('#username');
        if (input) {
            input.focus();
            input.addEventListener('input', () => emailField.classList.remove('has-error'), { once: true });
        }
    }

    loginForm.addEventListener('submit', async e => {
        e.preventDefault();

        loginForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());
        loginForm.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));

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
                if (emailField) showError(emailField, data.data.html);
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
            const emailField = loginForm.querySelector('#username')?.closest('.form-row');
            if (emailField) showError(emailField, '<ul class="woocommerce-error" role="alert"><li>Παρουσιάστηκε σφάλμα. Παρακαλώ προσπαθήστε ξανά.</li></ul>');
        }
    });
}
