<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
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

    /**
     * 🚦 CHECK APPROVAL STATUS
     * Ελέγχουμε αν ο χρήστης χρειάζεται έγκριση πριν προχωρήσουμε
     */
    $status = get_user_meta( $user->ID, '_sigma_account_status', true );
    $roles  = (array) $user->roles;

    // Αν είναι pending/rejected ΚΑΙ είναι ένας από τους 2 ρόλους, τον πετάμε έξω
    if ( in_array( $status, [ 'pending', 'rejected' ], true ) && array_intersect( [ 'company', 'municipality' ], $roles ) ) {
        wp_logout();
        $msg = $status === 'rejected'
            ? __( 'Η αίτηση εγγραφής σας απορρίφθηκε. Επικοινωνήστε μαζί μας για περισσότερες πληροφορίες.', 'ruined' )
            : __( 'Ο λογαριασμός σας εκκρεμεί προς έγκριση από τη διαχείριση.', 'ruined' );
        wp_send_json_error([
            'html' => '<div class="woocommerce-error">' . $msg . '</div>'
        ], 403);
        exit;
    }

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