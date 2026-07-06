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
