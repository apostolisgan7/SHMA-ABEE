<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_ajax_sigma_register', 'sigma_ajax_register' );
add_action( 'wp_ajax_nopriv_sigma_register', 'sigma_ajax_register' );

function sigma_ajax_register() {

    // 🔐 Nonce Check
    if ( ! check_ajax_referer( 'sigma-register', 'nonce', false ) ) {
        wp_send_json_error([
            'html' => '<ul class="woocommerce-error"><li>' . __( 'Security error.', 'ruined' ) . '</li></ul>'
        ], 403);
    }

    // 🚦 Rate limit: 5 tries / 10 λεπτά / IP
    $rl_key = sigma_rl_key( 'sigma_register' );
    $rl     = sigma_rate_limit_check( $rl_key, 5, 600 );

    if ( ! $rl['allowed'] ) {
        wp_send_json_error([
            'html' => '<ul class="woocommerce-error"><li>' .
                sprintf(
                    __( 'Πολλές προσπάθειες. Δοκίμασε ξανά σε %d δευτ.', 'ruined' ),
                    (int) $rl['retry_after']
                ) .
                '</li></ul>'
        ], 429);
    }

    $errors = new WP_Error();

    // Sanitize and validate customer type
    $type = isset($_POST['sigma_customer_type']) ? sanitize_text_field($_POST['sigma_customer_type']) : 'customer';
    $allowed_types = ['customer', 'company', 'municipality'];

    if (!in_array($type, $allowed_types, true)) {
        $errors->add('invalid_type', __( 'Invalid customer type.', 'ruined' ));
    }

    // Κοινά πεδία (Phone)
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    if ( empty($phone) ) {
        $errors->add('phone_required', __( 'Το τηλέφωνο είναι υποχρεωτικό.', 'ruined' ));
    } elseif (!preg_match('/^[+]?[\d\s\-\(\)]{7,15}$/', $phone)) {
        $errors->add('phone_format', __( 'Μη έγκυρη μορφή τηλεφώνου.', 'ruined' ));
    }

    // Validation ανά τύπο (B2C / B2B)
    if ( $type === 'customer' ) {
        $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
        if ( empty($customer_name) ) {
            $errors->add('name_required', __( 'Το ονοματεπώνυμο είναι υποχρεωτικό.', 'ruined' ));
        }
    }

    if ( in_array($type, ['company', 'municipality'], true) ) {
        if ( $type === 'company' ) {
            $company_name = isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '';
            if ( empty($company_name) ) {
                $errors->add('company_required', __( 'Η επωνυμία εταιρείας είναι υποχρεωτική.', 'ruined' ));
            }
        }

        if ( $type === 'municipality' ) {
            $municipality_name = isset($_POST['municipality_name']) ? sanitize_text_field($_POST['municipality_name']) : '';
            if ( empty($municipality_name) ) {
                $errors->add('municipality_required', __( 'Το όνομα δήμου είναι υποχρεωτικό.', 'ruined' ));
            }
        }

        $vat = isset($_POST['vat']) ? sanitize_text_field($_POST['vat']) : '';
        if (empty($vat)) {
            $errors->add('vat_required', __( 'Το ΑΦΜ είναι υποχρεωτικό.', 'ruined' ));
        } else {
            $vat = preg_replace('/\D/', '', $vat);
            if (strlen($vat) !== 9) {
                $errors->add('vat_format', __( 'Το ελληνικό ΑΦΜ πρέπει να έχει 9 ψηφία.', 'ruined' ));
            }
            if ($type === 'company' && ! sigma_validate_vat_vies('EL', $vat)) {
                $errors->add('vat_invalid', __( 'Το ΑΦΜ δεν είναι έγκυρο στο VIES.', 'ruined' ));
            }
        }
    }

    // ⛔ Αν υπάρχουν errors στο validation
    if ( $errors->has_errors() ) {
        sigma_rate_limit_hit( $rl_key, 600 );
        foreach ( $errors->get_error_messages() as $msg ) {
            wc_add_notice( $msg, 'error' );
        }
        ob_start();
        wc_print_notices();
        wp_send_json_error([ 'html' => ob_get_clean() ], 400);
    }

    wc_clear_notices();

    // 👤 Account Credentials Validation
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || !is_email($email)) {
        $errors->add('invalid_email', __( 'Παρακαλώ δώστε ένα έγκυρο email.', 'ruined' ));
    }
    if (empty($password) || strlen($password) < 8) {
        $errors->add('weak_password', __( 'Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.', 'ruined' ));
    }

    if ($errors->has_errors()) {
        sigma_rate_limit_hit( $rl_key, 600 );
        foreach ( $errors->get_error_messages() as $msg ) {
            wc_add_notice( $msg, 'error' );
        }
        ob_start();
        wc_print_notices();
        wp_send_json_error([ 'html' => ob_get_clean() ]);
    }

    // 🛠 Δημιουργία Χρήστη μέσω WooCommerce
    $user_id = wc_create_new_customer( $email, '', $password );

    if ( is_wp_error( $user_id ) ) {
        sigma_rate_limit_hit( $rl_key, 600 );
        wc_add_notice( $user_id->get_error_message(), 'error' );
        ob_start();
        wc_print_notices();
        wp_send_json_error([ 'html' => ob_get_clean() ]);
    }

    /**
     * -----------------------------------------------------------------
     * 🚀 PENDING APPROVAL LOGIC
     * -----------------------------------------------------------------
     */
    if ( in_array($type, ['company', 'municipality'], true) ) {

        // 1. Ορισμός κατάστασης σε "pending"
        update_user_meta($user_id, '_sigma_account_status', 'pending');

        // 2. Ειδοποίηση στον Διαχειριστή (Email)
        $admin_email = get_option('admin_email');
        $subject = __( 'Νέα εγγραφή προς έγκριση', 'ruined' );
        $message = sprintf( __( 'Νέος λογαριασμός (%s) ως "%s" περιμένει έγκριση.', 'ruined' ), $email, $type ) . "\r\n\r\n";
        $message .= __( 'Διαχείριση χρήστη:', 'ruined' ) . ' ' . admin_url('user-edit.php?user_id=' . $user_id);

        wp_mail($admin_email, $subject, $message);

        // 3. Επιστροφή JSON Success αλλά ΧΩΡΙΣ redirect (σταματάμε το flow εδώ)
        wp_send_json_success([
            'redirect'    => false,
            'html'        => '<div class="woocommerce-message">' . __( 'Η εγγραφή ολοκληρώθηκε! Ο λογαριασμός σας τελεί υπό έγκριση από τη διαχείριση. Θα λάβετε email μόλις ενεργοποιηθεί.', 'ruined' ) . '</div>',
            'vat_valid'   => true, // Το θεωρούμε true για το UI αφού πέρασε το validation
            'vat_message' => __( 'Το ΑΦΜ καταχωρήθηκε επιτυχώς.', 'ruined' ),
        ]);
        exit;
    }

    /**
     * -----------------------------------------------------------------
     * 🔐 AUTO-LOGIN (Μόνο για απλούς Customers)
     * -----------------------------------------------------------------
     */
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );

    // ✅ VAT feedback για Customers (αν υπάρχει)
    $vat_valid   = false;
    $vat_message = '';
    if ( ! empty($_POST['vat']) ) {
        $vat_valid = true;
        $vat_message = __( 'Το ΑΦΜ καταχωρήθηκε.', 'ruined' );
    }

    wp_send_json_success([
        'redirect'    => wc_get_page_permalink( 'myaccount' ),
        'vat_valid'   => $vat_valid,
        'vat_message' => $vat_message,
    ]);
}