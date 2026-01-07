import gsap from 'gsap';

export function initAuthModal() {
    const overlay = document.getElementById('sigma-auth-overlay');
    const modal = document.getElementById('sigma-auth-modal');

    if (!overlay || !modal) return;

    /* -----------------------------------------
     * ELEMENTS
     * ----------------------------------------- */
    const vatField = overlay.querySelector('.js-vat-field');
    const triggers = document.querySelectorAll('.js-auth-modal-trigger');
    const closeBtns = overlay.querySelectorAll('.js-auth-close');
    const roleBtns = overlay.querySelectorAll('.sigma-auth-role-btn');
    const rolesInner = overlay.querySelector('.sigma-auth-roles-inner');
    const pill = overlay.querySelector('.sigma-auth-roles-pill');
    const roleInputs = overlay.querySelectorAll('.js-auth-role-input');
    const toggleBtn = overlay.querySelector('.js-auth-toggle');
    const loginPane = overlay.querySelector('.sigma-auth-pane--login');
    const signupPane = overlay.querySelector('.sigma-auth-pane--signup');
    const passwordInput = overlay.querySelector('#register_password');
    const passwordMeter = overlay.querySelector('.password-strength');

    const header = overlay.querySelector('.sigma-auth-header');
    const roles = overlay.querySelector('.sigma-auth-roles');
    const body = overlay.querySelector('.sigma-auth-body');
    const footer = overlay.querySelector('.sigma-auth-footer');

    const loginForm = overlay.querySelector('.js-ajax-login-form');


    function toggle(el, show) {
        if (!el) return;
        el.style.display = show ? '' : 'none';
    }

    function syncFields(role) {
        overlay.querySelector('.js-customer-name-field').style.display = role === 'customer_b2c' ? '' : 'none';

        overlay.querySelector('.js-company-name-field').style.display = role === 'company' ? '' : 'none';

        overlay.querySelector('.js-municipality-name-field').style.display = role === 'municipality' ? '' : 'none';

        overlay.querySelector('.js-vat-field').style.display = role === 'company' || role === 'municipality' ? '' : 'none';
    }


    /* -----------------------------------------
     * SLIDING PILL
     * ----------------------------------------- */
    function movePillToButton(btn, animate = true) {
        if (!pill || !rolesInner || !btn) return;

        const wrapRect = rolesInner.getBoundingClientRect();
        const btnRect = btn.getBoundingClientRect();

        const padding = 4; // ίδιο με το CSS
        const x = btnRect.left - wrapRect.left - padding;
        const width = btnRect.width;


        gsap.to(pill, {
            x,
            width,
            duration: animate ? 0.32 : 0,
            ease: 'power3.out',
            overwrite: true
        });
    }


    function setInitialPillPosition() {
        const activeBtn = overlay.querySelector('.sigma-auth-role-btn.is-active') || roleBtns[0];
        if (activeBtn) movePillToButton(activeBtn, false);
    }


    function getPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    function updatePasswordMeter() {
        if (!passwordInput || !passwordMeter) return;

        const strength = getPasswordStrength(passwordInput.value);
        passwordMeter.className = 'password-strength';

        const label = passwordMeter.querySelector('.password-strength__label');
        if (!label) return;

        if (!passwordInput.value) {
            label.textContent = '';
            return;
        }

        if (strength <= 1) {
            passwordMeter.classList.add('is-weak');
            label.textContent = 'Αδύναμος';
        } else if (strength === 2) {
            passwordMeter.classList.add('is-medium');
            label.textContent = 'Μέτριος';
        } else if (strength === 3) {
            passwordMeter.classList.add('is-good');
            label.textContent = 'Καλός';
        } else {
            passwordMeter.classList.add('is-strong');
            label.textContent = 'Ισχυρός';
        }
    }

    if (passwordInput && passwordMeter) {
        passwordInput.addEventListener('input', updatePasswordMeter);
    }


    /* -----------------------------------------
     * OPEN / CLOSE MODAL
     * ----------------------------------------- */
    gsap.set(overlay, {autoAlpha: 0});
    gsap.set(modal, {y: 20, scale: 0.96, autoAlpha: 0});

    const tl = gsap.timeline({
        paused: true, defaults: {duration: 0.35, ease: 'power2.out'}, onReverseComplete() {
            overlay.classList.remove('is-open');
        }
    });

    tl
        .add(() => {
            overlay.classList.add('is-open');
            requestAnimationFrame(setInitialPillPosition);
        })
        .to(overlay, {autoAlpha: 1, duration: 0.25})
        .to(modal, {
            autoAlpha: 1, scale: 1, y: 0, duration: 0.45, ease: 'back.out(1.4)'
        }, '<')
        .from([header, roles], {y: 18, autoAlpha: 0, stagger: 0.08}, '-=0.25')
        .from(body, {y: 14, autoAlpha: 0}, '-=0.2')
        .from(footer, {y: 12, autoAlpha: 0}, '-=0.18');

    function openModal(e) {
        if (e) e.preventDefault();

        overlay.setAttribute('aria-hidden', 'false');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        tl.play(0);

        updatePasswordMeter();
    }


    function closeModal(e) {
        if (e) e.preventDefault();

        overlay.setAttribute('aria-hidden', 'true');
        tl.reverse();
    }


    triggers.forEach(btn => btn.addEventListener('click', openModal));
    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeModal();
        }
    });

    /* -----------------------------------------
     * ROLE TABS
     * ----------------------------------------- */
    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const role = btn.dataset.role;

            roleBtns.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            roleInputs.forEach(input => (input.value = role));

            syncFields(role);          // ✅ ΕΔΩ
            movePillToButton(btn, true);
        });
    });

    /* -----------------------------------------
 * 🧾 LIVE VAT CHECK (VIES) – blur only
 * ----------------------------------------- */
    const vatInput = overlay.querySelector('input[name="vat"]');

    if (vatInput) {
        let vatTimeout;

        vatInput.addEventListener('blur', () => {
            clearTimeout(vatTimeout);

            vatTimeout = setTimeout(async () => {
                const vat = vatInput.value.trim();
                const row = vatInput.closest('.form-row');

                if (!vat || !row || vatInput.offsetParent === null) return;

                // καθάρισε παλιά μηνύματα
                row.querySelectorAll('.field-error, .field-success').forEach(el => el.remove());

                row.classList.add('is-checking');

                try {
                    const res = await fetch(window.ajaxurl, {
                        method: 'POST', credentials: 'same-origin', headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }, body: new URLSearchParams({
                            action: 'sigma_check_vat', vat, nonce: overlay.querySelector('input[name="nonce"]')?.value
                        })
                    });

                    const data = await res.json();
                    row.classList.remove('is-checking');

                    const msg = document.createElement('div');

                    if (!data.success) {
                        vatInput.setAttribute('aria-invalid', 'true');
                        msg.className = 'field-error';
                    } else {
                        vatInput.setAttribute('aria-invalid', 'false');
                        msg.className = 'field-success';
                    }

                    msg.setAttribute('role', data.success ? 'status' : 'alert');
                    msg.setAttribute('aria-live', data.success ? 'polite' : 'assertive');
                    msg.textContent = data.data.message;

                    row.appendChild(msg);

                    gsap.fromTo(msg, {y: -4, autoAlpha: 0}, {y: 0, autoAlpha: 1, duration: 0.25});

                } catch (err) {
                    row.classList.remove('is-checking');
                    console.error('VAT check error:', err);
                }

            }, 250);
        });
    }


    /* -----------------------------------------
     * LOGIN / SIGNUP TOGGLE
     * ----------------------------------------- */
    if (toggleBtn && loginPane && signupPane) {
        modal.dataset.authMode = 'login';
        loginPane.classList.add('is-active');

        toggleBtn.addEventListener('click', () => {
            const isLogin = modal.dataset.authMode === 'login';
            modal.dataset.authMode = isLogin ? 'signup' : 'login';

            const outPane = isLogin ? loginPane : signupPane;
            const inPane = isLogin ? signupPane : loginPane;

            gsap.to(outPane, {
                autoAlpha: 0, x: isLogin ? -20 : 20, duration: 0.22, onComplete() {
                    outPane.classList.remove('is-active');
                    inPane.classList.add('is-active');

                    gsap.fromTo(inPane, {autoAlpha: 0, x: isLogin ? 20 : -20}, {autoAlpha: 1, x: 0, duration: 0.26});
                }
            });

        });
    }


    /* -----------------------------------------
     * CUSTOM SUBMIT (rv_button_arrow)
     * ----------------------------------------- */
    overlay.addEventListener('click', (e) => {
        const submitBtn = e.target.closest('.sigma-auth-submit');
        if (!submitBtn) return;
        const passwordInput = overlay.querySelector('#register_password');
        if (!passwordInput) return;

        const strength = getPasswordStrength(passwordInput.value);

        if (strength < 3) {
            showError('Ο κωδικός πρέπει να είναι πιο ισχυρός');
            return;
        }

        const form = submitBtn.closest('form');
        if (!form) return;

        e.preventDefault();
        form.requestSubmit();
    });

    /* -----------------------------------------
     * 🔐 AJAX LOGIN
     * ----------------------------------------- */
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // καθάρισε παλιά errors
            loginForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());

            const formData = new FormData(loginForm);
            formData.append('action', 'sigma_login');

            loginForm.classList.add('is-loading');

            try {
                const res = await fetch(window.ajaxurl, {
                    method: 'POST', credentials: 'same-origin', body: formData
                });

                const data = await res.json();

                if (!data.success) {
                    // καθάρισε παλιά errors
                    loginForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());

                    // βρες το email field
                    const emailField = loginForm.querySelector('#username')?.closest('.form-row');

                    if (emailField) {
                        emailField.insertAdjacentHTML('afterend', data.data.html);

                        const errorBox = emailField.nextElementSibling;

                        // animation
                        gsap.fromTo(errorBox, {y: -6, autoAlpha: 0}, {
                            y: 0, autoAlpha: 1, duration: 0.25, ease: 'power2.out'
                        });

                        // focus στο input
                        loginForm.querySelector('#username')?.focus();

                        // shake field
                        gsap.fromTo(emailField, {x: 0}, {x: -5, repeat: 4, yoyo: true, duration: 0.05});
                    }

                    return;
                }


                gsap.to(modal, {
                    scale: 0.96, autoAlpha: 0, duration: 0.35, ease: 'power2.inOut', onComplete() {
                        window.location.href = data.data.redirect;
                    }
                });

            } catch (err) {
                console.error('AJAX Login error:', err);
                loginForm.classList.remove('is-loading');
            }
        });
    }


    /* -----------------------------------------
     * 🔐 AJAX REGISTER
     * ----------------------------------------- */
    const registerForm = overlay.querySelector('.js-ajax-register-form');

    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // clear old errors
            registerForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());

            registerForm.classList.add('is-loading');

            const formData = new FormData(registerForm);
            formData.append('action', 'sigma_register');

            try {
                const res = await fetch(window.ajaxurl, {
                    method: 'POST', credentials: 'same-origin', body: formData
                });

                const data = await res.json();

                registerForm.classList.remove('is-loading');

                if (!data.success) {
                    registerForm.insertAdjacentHTML('afterbegin', data.data.html);
                    const temp = document.createElement('div');
                    temp.innerHTML = data.data.html;

                    temp.querySelectorAll('li').forEach(li => {
                        const msg = li.textContent.toLowerCase();

                        let field = null;

                        if (msg.includes('τηλέφωνο')) {
                            field = registerForm.querySelector('input[name="phone"]');
                        }

                        if (msg.includes('επωνυμία')) {
                            field = registerForm.querySelector('input[name="company_name"]');
                        }

                        if (msg.includes('αφμ')) {
                            field = registerForm.querySelector('input[name="vat"]');
                        }

                        if (!field) return;

                        const row = field.closest('.form-row');

                        // 🔥 ΑΦΑΙΡΕΣΕ παλιό error (αν υπάρχει)
                        row.querySelectorAll('.field-error').forEach(el => el.remove());

                        const error = document.createElement('div');
                        error.className = 'field-error';
                        error.textContent = li.textContent;

                        row.appendChild(error);

                        gsap.fromTo(error, {y: -4, autoAlpha: 0}, {y: 0, autoAlpha: 1, duration: 0.25});

                        field.focus();
                    });

                    const errorBox = registerForm.querySelector('.woocommerce-error');

                    if (errorBox) {
                        gsap.fromTo(errorBox, {y: -6, autoAlpha: 0}, {
                            y: 0, autoAlpha: 1, duration: 0.25, ease: 'power2.out'
                        });

                        errorBox.scrollIntoView({
                            behavior: 'smooth', block: 'center'
                        });
                    }

                    return;
                }
// -----------------------------------------
// ✅ SUCCESS (με VAT feedback)
// -----------------------------------------


                // SUCCESS animation
                gsap.to(modal, {
                    scale: 0.96, autoAlpha: 0, duration: 0.35, ease: 'power2.inOut', onComplete() {
                        window.location.href = data.data.redirect;
                    }
                });

            } catch (err) {
                console.error('AJAX Register error:', err);
                registerForm.classList.remove('is-loading');
            }
        });
    }
    if (passwordInput) {
        const passwordField = passwordInput.closest('.floating-field--password');

        const syncPasswordLabel = () => {
            passwordField.classList.toggle('has-value', passwordInput.value.trim() !== '');
        };

        passwordInput.addEventListener('input', syncPasswordLabel);
        passwordInput.addEventListener('blur', syncPasswordLabel);

        // initial (autocomplete / reopen modal)
        syncPasswordLabel();
    }


}
