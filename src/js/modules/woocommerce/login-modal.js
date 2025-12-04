import gsap from 'gsap';

export function initAuthModal() {
    const overlay = document.getElementById('sigma-auth-overlay');
    const modal   = document.getElementById('sigma-auth-modal');

    if (!overlay || !modal) return;

    const triggers   = document.querySelectorAll('.js-auth-modal-trigger');
    const closeBtns  = overlay.querySelectorAll('.js-auth-close');
    const roleBtns   = overlay.querySelectorAll('.sigma-auth-role-btn');
    const rolesInner = overlay.querySelector('.sigma-auth-roles-inner');
    const pill       = overlay.querySelector('.sigma-auth-roles-pill');
    const roleInputs = overlay.querySelectorAll('.js-auth-role-input');
    const toggleBtn  = overlay.querySelector('.js-auth-toggle');
    const loginPane  = overlay.querySelector('.sigma-auth-pane--login');
    const signupPane = overlay.querySelector('.sigma-auth-pane--signup');

    const header = overlay.querySelector('.sigma-auth-header');
    const roles  = overlay.querySelector('.sigma-auth-roles');
    const body   = overlay.querySelector('.sigma-auth-body');
    const footer = overlay.querySelector('.sigma-auth-footer');

    // ---------- SLIDING PILL HELPERS ----------

    function movePillToButton(btn, animate = true) {
        if (!pill || !rolesInner || !btn) return;

        const btnRect  = btn.getBoundingClientRect();
        const wrapRect = rolesInner.getBoundingClientRect();

        let width = btnRect.width;
        let x     = btnRect.left - wrapRect.left;

        // fallback αν για κάποιο λόγο είναι 0
        if (!width && roleBtns.length) {
            width = wrapRect.width / roleBtns.length;
        }
        if (!Number.isFinite(x)) {
            x = 0;
        }

        gsap.to(pill, {
            x,
            width,
            duration: animate ? 0.32 : 0,
            ease: 'power3.out'
        });
    }

    function setInitialPillPosition() {
        if (!roleBtns.length) return;

        const activeBtn =
            overlay.querySelector('.sigma-auth-role-btn.is-active') || roleBtns[0];

        movePillToButton(activeBtn, false);
    }

    // ---------- OPEN/CLOSE ANIMATION ----------

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
            // δίνουμε ένα frame να “στρωθεί” το layout και μετά μετράμε
            requestAnimationFrame(() => {
                setInitialPillPosition();
            });
        }, 0)
        .to(overlay, { autoAlpha: 1, duration: 0.25 })
        .to(
            modal,
            { autoAlpha: 1, scale: 1, y: 0, duration: 0.45, ease: 'back.out(1.4)' },
            '<'
        )
        .from([header, roles], { y: 18, autoAlpha: 0, stagger: 0.08 }, '-=0.25')
        .from(body, { y: 14, autoAlpha: 0 }, '-=0.2')
        .from(footer, { y: 12, autoAlpha: 0 }, '-=0.18');

    function openModal(e) {
        if (e) e.preventDefault();
        if (tl.reversed()) tl.play();
        else tl.play(0);
    }

    function closeModal(e) {
        if (e) e.preventDefault();
        tl.reverse();
    }

    triggers.forEach((btn) => btn.addEventListener('click', openModal));
    closeBtns.forEach((btn) => btn.addEventListener('click', closeModal));

    overlay.addEventListener('click', (e) => {
        if (e.target.classList.contains('js-auth-close')) {
            closeModal(e);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeModal(e);
        }
    });

    // ---------- CLICK ΣΤΑ TABS ----------

    if (roleBtns.length) {
        roleBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const newRole = btn.getAttribute('data-role');

                roleBtns.forEach((b) => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                if (roleInputs.length) {
                    roleInputs.forEach((input) => {
                        input.value = newRole;
                    });
                }

                movePillToButton(btn, true);

                gsap.fromTo(
                    btn,
                    { scale: 0.96 },
                    { scale: 1, duration: 0.16, ease: 'power1.out' }
                );
            });
        });
    }

    // ---------- LOGIN / SIGNUP SWITCH ----------

    if (toggleBtn && loginPane && signupPane) {
        modal.setAttribute('data-auth-mode', 'login');
        loginPane.classList.add('is-active');

        toggleBtn.addEventListener('click', () => {
            const currentMode = modal.getAttribute('data-auth-mode') || 'login';
            const nextMode    = currentMode === 'login' ? 'signup' : 'login';

            modal.setAttribute('data-auth-mode', nextMode);

            const outPane = currentMode === 'login' ? loginPane : signupPane;
            const inPane  = currentMode === 'login' ? signupPane : loginPane;

            gsap.to(outPane, {
                autoAlpha: 0,
                x: currentMode === 'login' ? -20 : 20,
                duration: 0.22,
                ease: 'power1.in',
                onComplete() {
                    outPane.classList.remove('is-active');
                    gsap.set(outPane, { clearProps: 'all' });

                    inPane.classList.add('is-active');
                    gsap.fromTo(
                        inPane,
                        { autoAlpha: 0, x: currentMode === 'login' ? 20 : -20 },
                        { autoAlpha: 1, x: 0, duration: 0.26, ease: 'power2.out' }
                    );
                }
            });
        });
    }

    // ---------- SUBMIT με το custom rv_button_arrow ----------

    overlay.addEventListener('click', (e) => {
        const submitLink = e.target.closest('.sigma-auth-submit');
        if (!submitLink) return;

        const form = submitLink.closest('form');
        if (!form) return;

        e.preventDefault();

        // πιο safe από απλό form.submit()
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    });

}
