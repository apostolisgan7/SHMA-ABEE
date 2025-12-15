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
