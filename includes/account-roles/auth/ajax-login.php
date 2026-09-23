<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 🚦 Block pending/rejected accounts at the core authentication level.
 *
 * Runs after WP core's own authenticate callbacks (priority 20), so it sees the
 * fully resolved user and applies to EVERY login entry point — the custom AJAX
 * modal, wp-login.php, and anything else that calls wp_signon()/wp_authenticate().
 */
add_filter( 'authenticate', 'sigma_block_unapproved_authentication', 30 );
function sigma_block_unapproved_authentication( $user ) {
    if ( ! $user instanceof WP_User ) {
        return $user;
    }

    $status = get_user_meta( $user->ID, '_sigma_account_status', true );
    $roles  = (array) $user->roles;

    if ( in_array( $status, [ 'pending', 'rejected' ], true ) && array_intersect( [ 'customer', 'company', 'municipality' ], $roles ) ) {
        return new WP_Error(
            'sigma_account_' . $status,
            $status === 'rejected'
                ? __( 'Η αίτηση εγγραφής σας απορρίφθηκε. Επικοινωνήστε μαζί μας για περισσότερες πληροφορίες.', 'ruined' )
                : __( 'Ο λογαριασμός σας εκκρεμεί προς έγκριση από τη διαχείριση.', 'ruined' )
        );
    }

    return $user;
}

add_action( 'wp_ajax_sigma_login', 'sigma_ajax_login' );
add_action( 'wp_ajax_nopriv_sigma_login', 'sigma_ajax_login' );

function sigma_ajax_login() {

    // 🔐 Nonce
    if ( ! check_ajax_referer( 'sigma-login', 'nonce', false ) ) {
        wp_send_json_error([
            'html' => '<ul class="woocommerce-error"><li>' . __( 'Security error.', 'ruined' ) . '</li></ul>'
        ], 403);
    }

    $username = isset( $_POST['username'] ) ? strtolower( trim( $_POST['username'] ) ) : '';
    $rl_key   = sigma_rl_key( 'sigma_login', $username );

    // 🚦 Rate limit: 5 tries / 10 λεπτά / IP + username
    $rl = sigma_rate_limit_check( $rl_key, 5, 600 );

    if ( ! $rl['allowed'] ) {
        wp_send_json_error([
            'html' => '<div class="woocommerce-error" role="alert">' .
                sprintf(
                    __( 'Πολλές προσπάθειες. Δοκίμασε ξανά σε %d δευτ.', 'ruined' ),
                    (int) $rl['retry_after']
                ) .
                '</div>'
        ], 429);
    }

    if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) ) {
        sigma_rate_limit_hit( $rl_key, 600 );

        wc_add_notice( __( 'Συμπλήρωσε email και κωδικό.', 'ruined' ), 'error' );
        ob_start();
        wc_print_notices();
        wp_send_json_error([ 'html' => ob_get_clean() ]);
    }

    $creds = [
        'user_login'    => sanitize_text_field( $_POST['username'] ),
        'user_password' => $_POST['password'],
        'remember'      => true,
    ];

    // Επιχειρούμε το login
    $user = wp_signon( $creds, is_ssl() );

    if ( is_wp_error( $user ) ) {
        sigma_rate_limit_hit( $rl_key, 600 );

        wc_add_notice( $user->get_error_message(), 'error' );
        ob_start();
        wc_print_notices();
        wp_send_json_error([ 'html' => ob_get_clean() ]);
    }

    // Pending/rejected accounts are already rejected above by wp_signon() itself
    // (see sigma_block_unapproved_authentication(), hooked into 'authenticate').

    // ✅ Login OK → redirect
    $redirect = apply_filters(
        'woocommerce_login_redirect',
        wc_get_page_permalink( 'myaccount' ),
        $user
    );

    wp_send_json_success([
        'redirect' => esc_url( $redirect )
    ]);
}