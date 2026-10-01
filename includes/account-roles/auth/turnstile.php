<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * --------------------------------------------------
 * Cloudflare Turnstile for the AJAX login / register modal
 * --------------------------------------------------
 * Keys live in wp-config.php (never in the theme / git):
 *
 *   define( 'SIGMA_TURNSTILE_SITE_KEY',   '...' );
 *   define( 'SIGMA_TURNSTILE_SECRET_KEY', '...' );
 *
 * Without both constants Turnstile is fully disabled (no widget, no check).
 * The widget is rendered lazily by src/js/modules/woocommerce/auth/auth-turnstile.js.
 */

function sigma_turnstile_enabled() {
    return defined( 'SIGMA_TURNSTILE_SITE_KEY' ) && SIGMA_TURNSTILE_SITE_KEY
        && defined( 'SIGMA_TURNSTILE_SECRET_KEY' ) && SIGMA_TURNSTILE_SECRET_KEY;
}

/**
 * Widget placeholder, printed inside the modal forms.
 */
function sigma_turnstile_widget() {
    if ( ! sigma_turnstile_enabled() ) {
        return;
    }
    ?>
    <div class="sigma-turnstile" data-sitekey="<?php echo esc_attr( SIGMA_TURNSTILE_SITE_KEY ); ?>"></div>
    <?php
}

/**
 * Verify the token sent by the browser.
 *
 * Fails open on a network error talking to Cloudflare, so an outage there
 * never locks everyone out (login is still rate limited).
 *
 * @return string|null Error message, or null when verified / disabled.
 */
function sigma_turnstile_error() {
    if ( ! sigma_turnstile_enabled() ) {
        return null;
    }

    $token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
    if ( $token === '' ) {
        return __( 'Η επαλήθευση ασφαλείας δεν ολοκληρώθηκε. Δοκίμασε ξανά.', 'ruined' );
    }

    $response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
        'timeout' => 10,
        'body'    => [
            'secret'   => SIGMA_TURNSTILE_SECRET_KEY,
            'response' => $token,
            'remoteip' => sigma_get_client_ip(),
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[sigma turnstile] siteverify unreachable: ' . $response->get_error_message() );
        return null;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['success'] ) ) {
        return __( 'Η επαλήθευση ασφαλείας απέτυχε. Δοκίμασε ξανά.', 'ruined' );
    }

    return null;
}
