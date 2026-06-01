import gsap from 'gsap';

export function initRegister(overlay, modal, registerForm, ajaxUrl) {
    if (!registerForm) return;

    registerForm.addEventListener('submit', async e => {
        e.preventDefault();

        if (registerForm.classList.contains('is-loading')) return;

        registerForm.querySelectorAll('.woocommerce-error, .field-error').forEach(el => el.remove());
        registerForm.classList.add('is-loading');

        const formData = new FormData(registerForm);
        formData.append('action', 'sigma_register');
        formData.append('nonce', registerForm.querySelector('input[name="nonce"]')?.value || '');

        try {
            const res  = await fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData });

            let data;
            const contentType = res.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                data = await res.json();
            } else {
                // Server-level error (e.g. nginx 429) — not JSON from our PHP
                data = {
                    success: false,
                    data: {
                        html: res.status === 429
                            ? '<ul class="woocommerce-error"><li>Πολλές προσπάθειες. Δοκίμασε ξανά σε λίγα λεπτά.</li></ul>'
                            : '<ul class="woocommerce-error"><li>Παρουσιάστηκε σφάλμα. Δοκίμασε ξανά.</li></ul>'
                    }
                };
            }

            registerForm.classList.remove('is-loading');

            if (!data.success) {
                registerForm.insertAdjacentHTML('afterbegin', data.data?.html || '<ul class="woocommerce-error"><li>Παρουσιάστηκε σφάλμα. Δοκίμασε ξανά.</li></ul>');

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
                // company/municipality pending approval — close modal + backdrop, show page-level notice
                gsap.to(overlay, { autoAlpha: 0, duration: 0.35, ease: 'power2.inOut' });
                gsap.to(modal, {
                    scale: 0.96,
                    autoAlpha: 0,
                    duration: 0.35,
                    ease: 'power2.inOut',
                    onComplete() {
                        overlay.setAttribute('aria-hidden', 'true');
                        overlay.classList.remove('is-open');
                        window.__lenis__?.start();
                        document.documentElement.classList.remove('scroll-locked');

                        const notice = document.createElement('div');
                        notice.className = 'sigma-register-notice';
                        notice.innerHTML = data.data.html;
                        document.body.prepend(notice);
                        gsap.fromTo(notice, { y: -20, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.45, ease: 'power2.out' });

                        setTimeout(() => {
                            gsap.to(notice, { autoAlpha: 0, duration: 0.4, onComplete: () => notice.remove() });
                        }, 7000);
                    },
                });
            }
        } catch (err) {
            console.error('AJAX Register error:', err);
            registerForm.classList.remove('is-loading');
            registerForm.insertAdjacentHTML('afterbegin', '<ul class="woocommerce-error"><li>Παρουσιάστηκε σφάλμα σύνδεσης. Δοκίμασε ξανά.</li></ul>');
        }
    });
}
