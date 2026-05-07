import gsap from 'gsap';

export function initToggle(overlay, modal, { refreshPasswordElsAndBind, updatePasswordMeter }) {
    const toggleBtn  = overlay.querySelector('.js-auth-toggle');
    const loginPane  = overlay.querySelector('.sigma-auth-pane--login');
    const signupPane = overlay.querySelector('.sigma-auth-pane--signup');

    if (!toggleBtn || !loginPane || !signupPane) return;

    modal.dataset.authMode = modal.dataset.authMode || 'login';
    if (modal.dataset.authMode === 'login') loginPane.classList.add('is-active');
    else signupPane.classList.add('is-active');

    toggleBtn.addEventListener('click', () => {
        const isLogin = modal.dataset.authMode === 'login';
        modal.dataset.authMode = isLogin ? 'signup' : 'login';

        const outPane = isLogin ? loginPane : signupPane;
        const inPane  = isLogin ? signupPane : loginPane;

        gsap.to(outPane, {
            autoAlpha: 0,
            x: isLogin ? -20 : 20,
            duration: 0.22,
            onComplete() {
                outPane.classList.remove('is-active');
                inPane.classList.add('is-active');
                gsap.fromTo(inPane, { autoAlpha: 0, x: isLogin ? 20 : -20 }, { autoAlpha: 1, x: 0, duration: 0.26 });
                if (!isLogin) {
                    refreshPasswordElsAndBind();
                    updatePasswordMeter();
                }
            },
        });
    });
}
