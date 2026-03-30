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

document.querySelectorAll('.floating-field input').forEach(input => {
    const parent = input.closest('.floating-field');

    const checkValue = () => {
        if (input.value.trim() !== '') {
            parent.classList.add('has-value');
        } else {
            parent.classList.remove('has-value');
        }
    };

    input.addEventListener('input', checkValue);
    input.addEventListener('blur', checkValue);

    // init
    checkValue();
});
