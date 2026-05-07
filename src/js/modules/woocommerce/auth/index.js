import { initPassword }   from './auth-password.js';
import { initTabs }       from './auth-tabs.js';
import { initModal }      from './auth-modal.js';
import { initToggle }     from './auth-toggle.js';
import { initVat }        from './auth-vat.js';
import { initValidation } from './auth-validation.js';
import { initLogin }      from './auth-login.js';
import { initRegister }   from './auth-register.js';

export function initAuthModal() {
    const overlay = document.getElementById('sigma-auth-overlay');
    const modal   = document.getElementById('sigma-auth-modal');
    if (!overlay || !modal) return;

    if (overlay.dataset.authInit === '1') return;
    overlay.dataset.authInit = '1';

    const ajaxUrl =
        window.ajaxurl ||
        window.sigmaAuth?.ajaxurl ||
        window.ruined?.ajaxurl ||
        '/wp-admin/admin-ajax.php';

    const loginForm    = overlay.querySelector('.js-ajax-login-form');
    const registerForm = overlay.querySelector('.js-ajax-register-form');

    const password = initPassword(overlay);
    const tabs     = initTabs(overlay, password);

    initModal(overlay, modal, { ...tabs, ...password });
    initToggle(overlay, modal, password);
    initVat(overlay, ajaxUrl);
    initValidation(overlay, password);
    initLogin(overlay, modal, loginForm, ajaxUrl);
    initRegister(overlay, modal, registerForm, ajaxUrl);
}
