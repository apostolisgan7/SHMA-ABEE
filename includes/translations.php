<?php
/**
 * Site-wide string translations (English -> Greek).
 *
 * Temporary override until WPML is set up for full multilingual support.
 * Add any English string here (from WooCommerce, YITH, Rank Math, WP core,
 * etc.) and it gets swapped for the Greek version everywhere on the site.
 */

defined('ABSPATH') || exit;

function ruined_translation_strings() {
    return [
        'Add to cart'      => 'Προσθήκη στο καλάθι',
        'View cart'        => 'Προβολή καλαθιού',
        'Checkout'         => 'Ολοκλήρωση παραγγελίας',
        'Continue shopping' => 'Συνέχεια αγορών',
        'Related products' => 'Σχετικά προϊόντα',

        // WP core login error strings (surfaced by wp_signon() in the AJAX login handler)
        'Unknown email address. Check again or try your username.' => 'Άγνωστη διεύθυνση email. Ελέγξτε ξανά ή δοκιμάστε το όνομα χρήστη σας.',
        '<strong>Error:</strong> The password you entered for the email address %s is incorrect.' => '<strong>Σφάλμα:</strong> Ο κωδικός πρόσβασης που εισάγατε για τη διεύθυνση email %s είναι λανθασμένος.',
        '<strong>Error:</strong> The username <strong>%s</strong> is not registered on this site. If you are unsure of your username, try your email address instead.' => '<strong>Σφάλμα:</strong> Το όνομα χρήστη <strong>%s</strong> δεν είναι καταχωρημένο σε αυτόν τον ιστότοπο. Αν δεν είστε σίγουροι για το όνομα χρήστη σας, δοκιμάστε τη διεύθυνση email σας.',
        '<strong>Error:</strong> The password you entered for the username %s is incorrect.' => '<strong>Σφάλμα:</strong> Ο κωδικός πρόσβασης που εισάγατε για το όνομα χρήστη %s είναι λανθασμένος.',
        '<strong>Error:</strong> The username field is empty.' => '<strong>Σφάλμα:</strong> Το πεδίο ονόματος χρήστη είναι κενό.',
        '<strong>Error:</strong> The email field is empty.' => '<strong>Σφάλμα:</strong> Το πεδίο email είναι κενό.',
        '<strong>Error:</strong> The password field is empty.' => '<strong>Σφάλμα:</strong> Το πεδίο κωδικού πρόσβασης είναι κενό.',
        'Lost your password?' => 'Ξεχάσατε τον κωδικό σας;',
    ];
}

add_filter('gettext', 'ruined_translate_gettext', 20, 3);
function ruined_translate_gettext($translated, $original, $domain) {
    $strings = ruined_translation_strings();
    return $strings[$original] ?? $translated;
}

add_filter('gettext_with_context', 'ruined_translate_gettext_with_context', 20, 4);
function ruined_translate_gettext_with_context($translated, $original, $context, $domain) {
    $strings = ruined_translation_strings();
    return $strings[$original] ?? $translated;
}

add_filter('ngettext', 'ruined_translate_ngettext', 20, 5);
function ruined_translate_ngettext($translated, $single, $plural, $number, $domain) {
    $strings   = ruined_translation_strings();
    $original  = (1 == $number) ? $single : $plural;
    return $strings[$original] ?? $translated;
}

/**
 * A few YITH Request-a-Quote messages aren't translatable strings — they're
 * saved as plain option values (only their *default* went through __() once,
 * on first save), so the gettext filters above never see them. Force the
 * Greek text regardless of what's stored in the option.
 */
add_filter('pre_option_ywraq_show_already_in_quote', function () {
    return 'Αυτό το προϊόν βρίσκεται ήδη στη λίστα αίτησης προσφοράς σας.';
});
add_filter('pre_option_ywraq_show_product_added', function () {
    return 'Το προϊόν προστέθηκε στη λίστα';
});
