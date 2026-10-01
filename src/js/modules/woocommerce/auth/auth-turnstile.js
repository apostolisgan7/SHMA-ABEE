// Cloudflare Turnstile for the auth modal forms.
// The widget placeholder (.sigma-turnstile) is only printed when the keys are
// set in wp-config.php — without it every function here is a no-op.

const SCRIPT_SRC = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
const TOKEN_TIMEOUT = 30000;

let scriptPromise = null;

function loadScript() {
    if (window.turnstile) return Promise.resolve();
    if (scriptPromise) return scriptPromise;

    scriptPromise = new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = SCRIPT_SRC;
        s.async = true;
        s.onload = () => resolve();
        s.onerror = () => { scriptPromise = null; reject(new Error('Turnstile failed to load')); };
        document.head.appendChild(s);
    });

    return scriptPromise;
}

/**
 * @returns {{ getToken: () => Promise<string>, reset: () => void } | null}
 */
export function initTurnstile(form) {
    const box = form?.querySelector('.sigma-turnstile');
    if (!box) return null;

    let widgetId = null;
    let token    = null;
    let waiters  = [];

    const flush = value => {
        waiters.forEach(fn => fn(value));
        waiters = [];
    };

    async function ensure() {
        await loadScript();
        if (widgetId !== null) return;

        widgetId = window.turnstile.render(box, {
            sitekey: box.dataset.sitekey,
            appearance: 'interaction-only', // invisible unless Cloudflare needs a click
            callback: t => { token = t; flush(t); },
            'error-callback': () => { token = null; flush(''); },
            'expired-callback': () => { token = null; },
        });
    }

    // Start the challenge as soon as the user starts filling in the form,
    // so the token is usually ready by the time they submit.
    form.addEventListener('focusin', () => { ensure().catch(() => {}); }, { once: true });

    return {
        async getToken() {
            try {
                await ensure();
            } catch {
                return '';
            }
            if (token) return token;

            return new Promise(resolve => {
                waiters.push(resolve);
                setTimeout(() => resolve(''), TOKEN_TIMEOUT);
            });
        },

        // Tokens are single-use: get a fresh one after every submit.
        reset() {
            token = null;
            if (widgetId !== null) window.turnstile?.reset(widgetId);
        },
    };
}
