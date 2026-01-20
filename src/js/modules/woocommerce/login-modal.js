import gsap from 'gsap';

export function initAuthModal() {
    const overlay = document.getElementById('sigma-auth-overlay');
    const modal = document.getElementById('sigma-auth-modal');
    if (!overlay || !modal) return;

    // avoid double-init
    if (overlay.dataset.authInit === '1') return;
    overlay.dataset.authInit = '1';

    // ajax url (frontend safe)
    const ajaxUrl =
        window.ajaxurl ||
        window.sigmaAuth?.ajaxurl ||
        window.ruined?.ajaxurl ||
        '/wp-admin/admin-ajax.php';

    /* -----------------------------------------
     * ELEMENTS (static)
     * ----------------------------------------- */
    const triggers = document.querySelectorAll('.js-auth-modal-trigger');
    const closeBtns = overlay.querySelectorAll('.js-auth-close');

    const roleBtns = overlay.querySelectorAll('.sigma-auth-role-btn');
    const rolesInner = overlay.querySelector('.sigma-auth-roles-inner');
    const pill = overlay.querySelector('.sigma-auth-roles-pill');
    const roleInputs = overlay.querySelectorAll('.js-auth-role-input');

    const toggleBtn = overlay.querySelector('.js-auth-toggle');
    const loginPane = overlay.querySelector('.sigma-auth-pane--login');
    const signupPane = overlay.querySelector('.sigma-auth-pane--signup');

    const header = overlay.querySelector('.sigma-auth-header');
    const roles = overlay.querySelector('.sigma-auth-roles');
    const body = overlay.querySelector('.sigma-auth-body');
    const footer = overlay.querySelector('.sigma-auth-footer');

    const loginForm = overlay.querySelector('.js-ajax-login-form');
    const registerForm = overlay.querySelector('.js-ajax-register-form');

    /* -----------------------------------------
     * HELPERS
     * ----------------------------------------- */
    function showInlineError(msg, where = registerForm || loginForm || modal) {
        if (!where) return;
        where.querySelectorAll('.woocommerce-error.sigma-inline, .field-error.sigma-inline')
            .forEach(el => el.remove());

        const box = document.createElement('div');
        box.className = 'woocommerce-error sigma-inline';
        box.setAttribute('role', 'alert');
        box.innerHTML = `<li>${msg}</li>`;

        where.insertAdjacentElement('afterbegin', box);
        gsap.fromTo(box, { y: -6, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25, ease: 'power2.out' });
    }

    function syncFields(role) {
        const customer = overlay.querySelector('.js-customer-name-field');
        const company = overlay.querySelector('.js-company-name-field');
        const muni = overlay.querySelector('.js-municipality-name-field');
        const vat = overlay.querySelector('.js-vat-field');

        if (customer) customer.style.display = role === 'customer' ? '' : 'none';
        if (company) company.style.display = role === 'company' ? '' : 'none';
        if (muni) muni.style.display = role === 'municipality' ? '' : 'none';
        if (vat) vat.style.display = role === 'company' || role === 'municipality' ? '' : 'none';
    }

    /* -----------------------------------------
     * SLIDING PILL
     * ----------------------------------------- */
    function movePillToButton(btn, animate = true) {
        if (!pill || !rolesInner || !btn) return;

        const wrapRect = rolesInner.getBoundingClientRect();
        const btnRect = btn.getBoundingClientRect();
        const padding = 4;

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

    // initial role sync
    {
        const activeBtn = overlay.querySelector('.sigma-auth-role-btn.is-active') || roleBtns[0];
        const role = activeBtn?.dataset?.role;
        if (role) syncFields(role);
    }

    /* -----------------------------------------
     * PASSWORD STRENGTH (robust)
     * ----------------------------------------- */
    let passwordInput = null;
    let passwordMeter = null;

    function getPasswordStrength(password) {
        let score = 0;
        if (!password) return 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score; // 0-4
    }

    function ensurePasswordMeter() {
        if (!passwordInput) return;

        // ψάξε ΜΟΝΟ το meter που αντιστοιχεί στο input
        const fieldWrap = passwordInput.closest('.floating-field--password');
        if (!fieldWrap) return;

        let meter = fieldWrap.nextElementSibling;

        if (meter && meter.classList.contains('password-strength')) {
            passwordMeter = meter;
            return;
        }

        // αν δεν υπάρχει, φτιάξ’ το
        passwordMeter = document.createElement('div');
        passwordMeter.className = 'password-strength';
        passwordMeter.innerHTML = `
        <div class="password-strength__bars" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
        </div>
        <div class="password-strength__label" aria-live="polite"></div>
    `;

        fieldWrap.insertAdjacentElement('afterend', passwordMeter);
    }


    function updatePasswordMeter() {
        if (!passwordInput) return;
        ensurePasswordMeter();
        if (!passwordMeter) return;

        const label = passwordMeter.querySelector('.password-strength__label');
        const bars = passwordMeter.querySelectorAll('.password-strength__bars span');
        if (!label || !bars.length) return;

        const val = passwordInput.value || '';
        const strength = getPasswordStrength(val);

        // reset
        passwordMeter.className = 'password-strength';
        bars.forEach(b => (b.className = ''));

        if (!val) {
            label.textContent = '';
            return;
        }

        // fill bars
        bars.forEach((bar, idx) => {
            if (idx < strength) bar.classList.add('is-on');
        });

        // label + class
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

    function refreshPasswordElsAndBind() {
        passwordInput = overlay.querySelector('.sigma-auth-pane--signup.is-active input[name="password"]');

        if (!passwordInput) return;

        // bind once
        if (passwordInput.dataset.meterBound === '1') return;
        passwordInput.dataset.meterBound = '1';

        passwordInput.addEventListener('input', updatePasswordMeter);
        passwordInput.addEventListener('focus', updatePasswordMeter);
    }

    /* -----------------------------------------
     * OPEN / CLOSE MODAL
     * ----------------------------------------- */
    gsap.set(overlay, { autoAlpha: 0 });
    gsap.set(modal, { y: 20, scale: 0.96, autoAlpha: 0 });

    const tl = gsap.timeline({
        paused: true,
        defaults: { duration: 0.35, ease: 'power2.out' },
        onReverseComplete() {
            overlay.classList.remove('is-open');
        }
    });

    tl
        .add(() => {
            overlay.classList.add('is-open');
            requestAnimationFrame(setInitialPillPosition);
        })
        .to(overlay, { autoAlpha: 1, duration: 0.25 })
        .to(modal, { autoAlpha: 1, scale: 1, y: 0, duration: 0.45, ease: 'back.out(1.4)' }, '<')
        .from([header, roles], { y: 18, autoAlpha: 0, stagger: 0.08 }, '-=0.25')
        .from(body, { y: 14, autoAlpha: 0 }, '-=0.2')
        .from(footer, { y: 12, autoAlpha: 0 }, '-=0.18');

    function openModal(e) {
        if (e) e.preventDefault();

        overlay.setAttribute('aria-hidden', 'false');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        tl.play(0);

        // ensure password meter works even if signup pane was hidden
        setTimeout(() => {
            refreshPasswordElsAndBind();
            updatePasswordMeter();
        }, 80);
    }

    function closeModal(e) {
        if (e) e.preventDefault();
        overlay.setAttribute('aria-hidden', 'true');
        tl.reverse();
    }

    triggers.forEach(btn => btn.addEventListener('click', openModal));
    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeModal();
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

            syncFields(role);
            movePillToButton(btn, true);

            // 🔥 ADD THESE
            setTimeout(() => {
                refreshPasswordElsAndBind();
                updatePasswordMeter();
            }, 50);
        });
    });


    /* -----------------------------------------
     * LOGIN / SIGNUP TOGGLE
     * ----------------------------------------- */
    if (toggleBtn && loginPane && signupPane) {
        modal.dataset.authMode = modal.dataset.authMode || 'login';
        if (modal.dataset.authMode === 'login') loginPane.classList.add('is-active');
        else signupPane.classList.add('is-active');

        toggleBtn.addEventListener('click', () => {
            const isLogin = modal.dataset.authMode === 'login';
            modal.dataset.authMode = isLogin ? 'signup' : 'login';

            const outPane = isLogin ? loginPane : signupPane;
            const inPane = isLogin ? signupPane : loginPane;

            gsap.to(outPane, {
                autoAlpha: 0,
                x: isLogin ? -20 : 20,
                duration: 0.22,
                onComplete() {
                    outPane.classList.remove('is-active');
                    inPane.classList.add('is-active');

                    gsap.fromTo(inPane, { autoAlpha: 0, x: isLogin ? 20 : -20 }, { autoAlpha: 1, x: 0, duration: 0.26 });

                    // when signup becomes visible, bind password elements
                    if (!isLogin) {
                        refreshPasswordElsAndBind();
                        updatePasswordMeter();
                    }
                }
            });
        });
    }

    /* -----------------------------------------
     * 🧾 LIVE VAT CHECK (blur only)
     * ----------------------------------------- */
    let vatTimer = null;
    let vatInProgress = false;

    overlay.addEventListener(
        'blur',
        e => {
            const vatInput = e.target.closest('input[name="vat"]');
            if (!vatInput) return;

            clearTimeout(vatTimer);

            vatTimer = setTimeout(() => {
                if (vatInProgress) return;
                vatInProgress = true;

                const row = vatInput.closest('.form-row');
                const form = vatInput.closest('form');
                const vat = vatInput.value.trim();

                // hidden / empty / wrong context
                if (!vat || !row || vatInput.offsetParent === null) {
                    vatInProgress = false;
                    return;
                }

                const nonce = form?.querySelector('input[name="nonce"]')?.value;
                if (!nonce) {
                    vatInProgress = false;
                    console.warn('VAT nonce missing');
                    return;
                }

                row.querySelectorAll('.field-error, .field-success').forEach(el => el.remove());
                row.classList.add('is-checking');

                fetch(ajaxUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'sigma_check_vat',
                        vat,
                        nonce
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        row.classList.remove('is-checking');
                        vatInProgress = false;

                        const ok = !!data?.success;
                        const msg = document.createElement('div');

                        msg.className = ok ? 'field-success' : 'field-error';
                        msg.textContent =
                            data?.data?.message || (ok ? 'OK' : 'Σφάλμα ελέγχου ΑΦΜ');

                        vatInput.setAttribute('aria-invalid', ok ? 'false' : 'true');
                        row.appendChild(msg);

                        gsap.fromTo(
                            msg,
                            { y: -4, autoAlpha: 0 },
                            { y: 0, autoAlpha: 1, duration: 0.25 }
                        );
                    })
                    .catch(err => {
                        row.classList.remove('is-checking');
                        vatInProgress = false;
                        console.error('VAT error:', err);
                    });
            }, 250);
        },
        true
    );



    /* -----------------------------------------
     * CUSTOM SUBMIT (password strength gate)
     * ----------------------------------------- */
    overlay.addEventListener('click', e => {
        const submitBtn = e.target.closest('.sigma-auth-submit');
        if (!submitBtn) return;

        // μόνο στο signup
        const activeSignup = signupPane?.classList.contains('is-active');
        if (!activeSignup) return;

        const form = submitBtn.closest('form');
        if (!form) return;

        e.preventDefault();

        // 🧹 καθάρισε παλιά errors
        form.querySelectorAll('.field-error').forEach(el => el.remove());
        form.querySelectorAll('.is-error').forEach(el => el.classList.remove('is-error'));

        let hasError = false;

        /* -----------------------------------------
         * 1️⃣ REQUIRED FIELDS VALIDATION
         * ----------------------------------------- */
        const requiredFields = form.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            // αγνόησε hidden fields
            if (field.offsetParent === null) return;

            if (!field.value.trim()) {
                hasError = true;

                field.classList.add('is-error');

                const row = field.closest('.form-row');
                if (row && !row.querySelector('.field-error')) {
                    const err = document.createElement('div');
                    err.className = 'field-error';
                    err.textContent = 'Το πεδίο είναι υποχρεωτικό.';
                    row.appendChild(err);
                }
            }
        });

        /* -----------------------------------------
         * 2️⃣ PASSWORD STRENGTH
         * ----------------------------------------- */
        const pass = form.querySelector('input[name="password"]');
        if (pass) {
            const strength = getPasswordStrength(pass.value);
            if (strength < 3) {
                hasError = true;

                pass.classList.add('is-error');

                const row = pass.closest('.form-row');
                if (row && !row.querySelector('.field-error')) {
                    const err = document.createElement('div');
                    err.className = 'field-error';
                    fishingError(err);
                    err.textContent = 'Ο κωδικός πρέπει να είναι πιο ισχυρός.';
                    row.appendChild(err);
                }
            }
        }

        /* -----------------------------------------
         * ⛔ STOP αν υπάρχουν errors
         * ----------------------------------------- */
        if (hasError) {
            showInlineError('Συμπλήρωσε σωστά όλα τα υποχρεωτικά πεδία.', form);
            return;
        }

        /* -----------------------------------------
         * ✅ OK → AJAX SUBMIT
         * ----------------------------------------- */
        form.requestSubmit();
    });


    /* -----------------------------------------
     * 🔐 AJAX LOGIN
     * ----------------------------------------- */
    if (loginForm) {
        loginForm.addEventListener('submit', async e => {
            e.preventDefault();

            loginForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());

            const formData = new FormData(loginForm);
            formData.append('action', 'sigma_login');

            loginForm.classList.add('is-loading');

            try {
                const res = await fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData });
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
                    onComplete() {
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
    if (registerForm) {
        registerForm.addEventListener('submit', async e => {
            e.preventDefault();

            registerForm.querySelectorAll('.woocommerce-error').forEach(el => el.remove());
            registerForm.classList.add('is-loading');

            // --- RECAPTCHA INTEGRATION START ---
            let recaptchaToken = '';
            if (typeof grecaptcha !== 'undefined') {
                try {
                    // Περιμένουμε το token από την Google
                    recaptchaToken = await grecaptcha.execute('6LcSbFAsAAAAAPDqcEYBbJhjnu3kDFzkyftOx5ut', { action: 'register' });
                } catch (error) {
                    console.error('reCAPTCHA error:', error);
                }
            }
            // --- RECAPTCHA INTEGRATION END ---

            const formData = new FormData(registerForm);
            formData.append('action', 'sigma_register');

            // Προσθέτουμε το token στο formData για να το δει η PHP
            if (recaptchaToken) {
                formData.append('g-recaptcha-response', recaptchaToken);
            }

            try {
                const res = await fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData });
                const data = await res.json();

                registerForm.classList.remove('is-loading');

                if (!data.success) {
                    registerForm.insertAdjacentHTML('afterbegin', data.data.html);

                    // ... το υπόλοιπο error handling σου (temp.querySelectorAll('li') κλπ)
                    const temp = document.createElement('div');
                    temp.innerHTML = data.data.html;

                    temp.querySelectorAll('li').forEach(li => {
                        const msg = (li.textContent || '').toLowerCase();
                        let field = null;

                        if (msg.includes('τηλέφωνο')) field = registerForm.querySelector('input[name="phone"]');
                        if (msg.includes('επωνυμία')) field = registerForm.querySelector('input[name="company_name"]');
                        if (msg.includes('αφμ')) field = registerForm.querySelector('input[name="vat"]');

                        if (!field) return;

                        const row = field.closest('.form-row');
                        row?.querySelectorAll('.field-error').forEach(el => el.remove());

                        const error = document.createElement('div');
                        error.className = 'field-error';
                        error.textContent = li.textContent;

                        row?.appendChild(error);
                        gsap.fromTo(error, { y: -4, autoAlpha: 0 }, { y: 0, autoAlpha: 1, duration: 0.25 });
                    });

                    registerForm.querySelector('.woocommerce-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // Success redirect
                gsap.to(modal, {
                    scale: 0.96,
                    autoAlpha: 0,
                    duration: 0.35,
                    ease: 'power2.inOut',
                    onComplete() {
                        window.location.href = data.data.redirect;
                    }
                });

            } catch (err) {
                console.error('AJAX Register error:', err);
                registerForm.classList.remove('is-loading');
            }
        });
    }
}
