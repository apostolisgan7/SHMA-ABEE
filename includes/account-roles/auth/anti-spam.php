<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * --------------------------------------------------
 * Registration anti-spam: honeypot + min fill time + success rate limit
 * --------------------------------------------------
 * Covers both the AJAX modal (sigma_register) and the native WC registration
 * form. Checkout account creation is deliberately left alone so orders are
 * never blocked.
 */

const SIGMA_REG_MIN_SECONDS  = 3;    // Faster than this = bot
const SIGMA_REG_MAX_SUCCESS  = 3;    // Successful registrations per IP...
const SIGMA_REG_SUCCESS_WIN  = 3600; // ...per hour

/**
 * Hidden fields, printed inside every registration form.
 * The honeypot is pushed off-screen (not display:none, which some bots skip).
 */
add_action( 'woocommerce_register_form', 'sigma_render_registration_trap_fields' );
function sigma_render_registration_trap_fields() {
    $ts    = time();
    $hp_id = wp_unique_id( 'sigma_hp_' );
    ?>
    <div class="sigma-hp" aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">
        <label for="<?php echo esc_attr( $hp_id ); ?>">Αφήστε αυτό το πεδίο κενό</label>
        <input type="text" id="<?php echo esc_attr( $hp_id ); ?>" name="sigma_hp_field" value="" tabindex="-1" autocomplete="off">
    </div>
    <input type="hidden" name="sigma_form_ts" value="<?php echo esc_attr( $ts . '|' . wp_hash( 'sigma_reg_ts|' . $ts ) ); ?>">
    <?php
}

/**
 * Honeypot + timing check.
 *
 * @return string|null Error message, or null when the request looks human.
 */
function sigma_registration_trap_error() {
    // Honeypot filled → bot.
    if ( ! empty( $_POST['sigma_hp_field'] ) ) {
        return __( 'Security error.', 'ruined' );
    }

    // Signed timestamp missing/forged → bot (or a form posted without loading the page).
    $raw   = isset( $_POST['sigma_form_ts'] ) ? sanitize_text_field( wp_unslash( $_POST['sigma_form_ts'] ) ) : '';
    $parts = explode( '|', $raw );
    if ( count( $parts ) !== 2 || ! ctype_digit( $parts[0] ) || ! hash_equals( wp_hash( 'sigma_reg_ts|' . $parts[0] ), $parts[1] ) ) {
        return __( 'Security error.', 'ruined' );
    }

    // Submitted too fast → bot.
    if ( time() - (int) $parts[0] < SIGMA_REG_MIN_SECONDS ) {
        return __( 'Η φόρμα στάλθηκε πολύ γρήγορα. Δοκίμασε ξανά σε λίγα δευτερόλεπτα.', 'ruined' );
    }

    return null;
}

/**
 * Successful-registration limit per IP.
 *
 * @return string|null Error message, or null when allowed.
 */
function sigma_registration_success_limit_error() {
    $rl = sigma_rate_limit_check( sigma_rl_key( 'sigma_register_ok' ), SIGMA_REG_MAX_SUCCESS, SIGMA_REG_SUCCESS_WIN );

    if ( ! $rl['allowed'] ) {
        return sprintf(
            __( 'Πολλές εγγραφές από αυτή τη σύνδεση. Δοκίμασε ξανά σε %d λεπτά.', 'ruined' ),
            (int) ceil( $rl['retry_after'] / 60 )
        );
    }

    return null;
}

/**
 * True only for our registration forms (AJAX modal or native WC form),
 * never for checkout account creation.
 */
function sigma_is_registration_form_request() {
    if ( wp_doing_ajax() ) {
        return isset( $_POST['action'] ) && $_POST['action'] === 'sigma_register';
    }

    return isset( $_POST['register'] ) && ! is_checkout();
}

/**
 * Native WC form (and a second safety net for the AJAX path, since
 * wc_create_new_customer() runs this filter too).
 */
add_filter( 'woocommerce_registration_errors', 'sigma_registration_spam_errors', 5 );
function sigma_registration_spam_errors( $errors ) {
    if ( ! sigma_is_registration_form_request() ) {
        return $errors;
    }

    $msg = sigma_registration_trap_error() ?? sigma_registration_success_limit_error();
    if ( $msg ) {
        $errors->add( 'sigma_spam', $msg );
    }

    return $errors;
}

/**
 * Count every successful registration made through our forms.
 */
add_action( 'woocommerce_created_customer', 'sigma_registration_count_success' );
function sigma_registration_count_success() {
    if ( sigma_is_registration_form_request() ) {
        sigma_rate_limit_hit( sigma_rl_key( 'sigma_register_ok' ), SIGMA_REG_SUCCESS_WIN );
    }
}
