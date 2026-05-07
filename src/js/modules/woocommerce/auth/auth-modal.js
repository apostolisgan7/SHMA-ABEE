import gsap from 'gsap';

export function initModal(overlay, modal, { setInitialPillPosition, refreshPasswordElsAndBind, updatePasswordMeter }) {
    const triggers  = document.querySelectorAll('.js-auth-modal-trigger');
    const closeBtns = overlay.querySelectorAll('.js-auth-close');
    const header    = overlay.querySelector('.sigma-auth-header');
    const roles     = overlay.querySelector('.sigma-auth-roles');
    const body      = overlay.querySelector('.sigma-auth-body');
    const footer    = overlay.querySelector('.sigma-auth-footer');

    gsap.set(overlay, { autoAlpha: 0 });
    gsap.set(modal, { y: 20, scale: 0.96, autoAlpha: 0 });

    const tl = gsap.timeline({
        paused: true,
        defaults: { duration: 0.35, ease: 'power2.out' },
        onReverseComplete() { overlay.classList.remove('is-open'); },
    });

    tl
        .add(() => {
            overlay.classList.add('is-open');
            requestAnimationFrame(setInitialPillPosition);
        })
        .to(overlay, { autoAlpha: 1, duration: 0.25 })
        .to(modal, { autoAlpha: 1, scale: 1, y: 0, duration: 0.45, ease: 'back.out(1.4)' }, '<')
        .from([header, roles], { y: 18, autoAlpha: 0, stagger: 0.08 }, '-=0.25')
        .from(body,   { y: 14, autoAlpha: 0 }, '-=0.2')
        .from(footer, { y: 12, autoAlpha: 0 }, '-=0.18');

    function openModal(e) {
        if (e) e.preventDefault();
        overlay.setAttribute('aria-hidden', 'false');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        tl.play(0);
        setTimeout(() => {
            refreshPasswordElsAndBind();
            updatePasswordMeter();
        }, 150);
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

    return { openModal, closeModal };
}
