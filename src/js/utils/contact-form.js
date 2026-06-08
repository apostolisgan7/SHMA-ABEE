document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.rv-cf7__submit button br').forEach(br => br.remove());
});


document.addEventListener('DOMContentLoaded', function () {

    function markFields(event) {
        if (!event.target) return;

        const fields = event.target.querySelectorAll(
            'input[type="text"], input[type="email"], input[type="tel"], textarea'
        );

        fields.forEach(el => {
            el.classList.remove('is-valid');

            if (
                !el.classList.contains('wpcf7-not-valid') &&
                el.value.trim() !== ''
            ) {
                el.classList.add('is-valid');
            }
        });
    }

    document.addEventListener('wpcf7invalid', markFields);
    document.addEventListener('wpcf7submit', markFields);

});

function initFloatingLabel(input, parentSelector) {
    const parent = input.closest(parentSelector);
    if (!parent) return;

    const checkValue = () => {
        parent.classList.toggle('has-value', input.value.trim() !== '');
    };

    input.addEventListener('input', checkValue);
    input.addEventListener('blur', checkValue);
    checkValue();
}

document.querySelectorAll('.floating-field input').forEach(input => {
    initFloatingLabel(input, '.floating-field');
});

document.querySelectorAll('.yith-ywraq-mail-form-wrapper .form-row input, .yith-ywraq-mail-form-wrapper .form-row textarea').forEach(input => {
    initFloatingLabel(input, '.form-row');
});

// Edit Address — floating labels
document.addEventListener('DOMContentLoaded', () => {
    const addressForm = document.querySelector('.woocommerce-address-fields');
    if (!addressForm) return;

    // Text inputs
    addressForm.querySelectorAll('.form-row input.input-text').forEach(input => {
        initFloatingLabel(input, '.form-row');
    });

    // Selects (native select fires change even when Select2 is active)
    addressForm.querySelectorAll('.form-row select').forEach(select => {
        const row = select.closest('.form-row');
        if (!row) return;

        const check = () => row.classList.toggle('has-value', select.value.trim() !== '');
        select.addEventListener('change', check);
        check(); // Initial state (e.g. Greece pre-selected)
    });
});
