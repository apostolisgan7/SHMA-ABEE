export function initPassword(overlay) {
    let passwordInput = null;
    let passwordMeter = null;

    function getPasswordStrength(password) {
        let score = 0;
        if (!password) return 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return score;
    }

    function ensurePasswordMeter() {
        if (!passwordInput) return;
        const fieldWrap = passwordInput.closest('.floating-field--password');
        if (!fieldWrap) return;

        const meter = fieldWrap.nextElementSibling;
        if (meter && meter.classList.contains('password-strength')) {
            passwordMeter = meter;
            return;
        }

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

        passwordMeter.className = 'password-strength';
        bars.forEach(b => (b.className = ''));

        if (!val) { label.textContent = ''; return; }

        bars.forEach((bar, idx) => { if (idx < strength) bar.classList.add('is-on'); });

        if (strength <= 1)      { passwordMeter.classList.add('is-weak');   label.textContent = 'Αδύναμος'; }
        else if (strength === 2) { passwordMeter.classList.add('is-medium'); label.textContent = 'Μέτριος'; }
        else if (strength === 3) { passwordMeter.classList.add('is-good');   label.textContent = 'Καλός'; }
        else                     { passwordMeter.classList.add('is-strong'); label.textContent = 'Ισχυρός'; }
    }

    function refreshPasswordElsAndBind() {
        passwordInput = overlay.querySelector('.sigma-auth-pane--signup input[name="password"]');
        if (!passwordInput) return;
        if (passwordInput.dataset.meterBound === '1') return;
        passwordInput.dataset.meterBound = '1';
        passwordInput.addEventListener('input', updatePasswordMeter);
        passwordInput.addEventListener('focus', updatePasswordMeter);
    }

    return { getPasswordStrength, refreshPasswordElsAndBind, updatePasswordMeter };
}
