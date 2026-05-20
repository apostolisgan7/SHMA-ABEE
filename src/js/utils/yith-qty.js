function buildStepper(input) {
    if (input.dataset.customized) return;
    input.dataset.customized = 'true';

    const min  = parseInt(input.min) || 0;
    const max  = parseInt(input.max) || Infinity;
    const step = parseInt(input.step) || 1;

    const wrapper = document.createElement('div');
    wrapper.className = 'rv-qty';

    const value = document.createElement('div');
    value.className = 'rv-qty-value';
    value.textContent = input.value;

    const controls = document.createElement('div');
    controls.className = 'rv-qty-controls';

    const inc = document.createElement('button');
    inc.type = 'button';
    inc.className = 'rv-qty-btn';
    inc.innerHTML = '<svg width="8" height="8" viewBox="0 0 14 8" fill="none"><path d="M0.911945 6.68422L6.68302 0.911987L12.4541 6.68422" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    const dec = document.createElement('button');
    dec.type = 'button';
    dec.className = 'rv-qty-btn rv-qty-btn--down';
    dec.innerHTML = '<svg width="8" height="8" viewBox="0 0 14 8" fill="none"><path d="M12.4543 0.91197L6.68319 6.6842L0.912109 0.91197" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    controls.appendChild(inc);
    controls.appendChild(dec);
    wrapper.appendChild(value);
    wrapper.appendChild(controls);

    input.parentNode.appendChild(wrapper);

    Object.assign(input.style, {
        position: 'absolute',
        opacity: '0',
        pointerEvents: 'none',
        width: '0',
        height: '0',
    });

    function update(newVal) {
        newVal = Math.max(min, Math.min(max, newVal));
        input.value = newVal;
        value.textContent = newVal;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    inc.addEventListener('click', () => update(parseInt(input.value) + step));
    dec.addEventListener('click', () => update(parseInt(input.value) - step));
}

export function initYithQty() {
    const form = document.getElementById('yith-ywraq-form');
    if (!form) return;

    const run = () => form.querySelectorAll('.quantity input.qty').forEach(buildStepper);

    run();

    new MutationObserver(run).observe(form, { childList: true, subtree: true });
}
