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
    $rl     = sigma_rate_limit_check( $rl_key, 10, 600 );

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
    // Για company/municipality: απενεργοποιούμε το WC welcome email (θα στείλουμε custom pending email)
    $is_pending_type = in_array( $type, ['company', 'municipality'], true );
    if ( $is_pending_type ) {
        add_filter( 'woocommerce_email_enabled_customer_new_account', '__return_false' );
    }

    $user_id = wc_create_new_customer( $email, '', $password );

    if ( $is_pending_type ) {
        remove_filter( 'woocommerce_email_enabled_customer_new_account', '__return_false' );
    }

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
        $admin_email  = get_option( 'admin_email' );
        $site_name    = get_bloginfo( 'name' );
        $entity_name  = $type === 'company'
            ? ( isset( $company_name ) ? $company_name : '—' )
            : ( isset( $municipality_name ) ? $municipality_name : '—' );
        $type_label   = $type === 'company' ? 'Εταιρεία' : 'Δήμος';
        $approve_url  = admin_url( 'user-edit.php?user_id=' . $user_id );

        $subject = '[' . $site_name . '] Νέα εγγραφή προς έγκριση — ' . $entity_name;

        $message = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">';
        $message .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:32px 0;">';
        $message .= '<tr><td align="center">';
        $message .= '<table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;">';

        // Header
        $message .= '<tr><td style="background:#1a1a1a;padding:24px 32px;">';
        $message .= '<p style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">' . esc_html( $site_name ) . '</p>';
        $message .= '<p style="margin:4px 0 0;color:#aaaaaa;font-size:13px;">Νέος λογαριασμός προς έγκριση</p>';
        $message .= '</td></tr>';

        // Body
        $message .= '<tr><td style="padding:32px;">';
        $message .= '<p style="margin:0 0 24px;font-size:15px;color:#333333;">Ένας νέος χρήστης ζήτησε εγγραφή και περιμένει έγκριση.</p>';

        // Details table
        $message .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e8e8e8;border-radius:4px;">';
        $rows = [
            [ 'Τύπος',      $type_label ],
            [ 'Επωνυμία',   $entity_name ],
            [ 'Email',      $email ],
            [ 'Τηλέφωνο',   isset( $phone ) ? $phone : '—' ],
            [ 'ΑΦΜ',        isset( $vat ) ? $vat : '—' ],
        ];
        foreach ( $rows as $i => $row ) {
            $bg = $i % 2 === 0 ? '#fafafa' : '#ffffff';
            $message .= '<tr style="background:' . $bg . ';">';
            $message .= '<td style="padding:10px 16px;font-size:13px;color:#888888;width:120px;border-bottom:1px solid #e8e8e8;">' . esc_html( $row[0] ) . '</td>';
            $message .= '<td style="padding:10px 16px;font-size:13px;color:#222222;border-bottom:1px solid #e8e8e8;"><strong>' . esc_html( $row[1] ) . '</strong></td>';
            $message .= '</tr>';
        }
        $message .= '</table>';

        // CTA button
        $message .= '<div style="margin:28px 0 0;text-align:center;">';
        $message .= '<a href="' . esc_url( $approve_url ) . '" style="display:inline-block;background:#1a1a1a;color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:4px;font-size:14px;font-weight:bold;">Διαχείριση χρήστη &rarr;</a>';
        $message .= '</div>';

        $message .= '</td></tr>';

        // Footer
        $message .= '<tr><td style="background:#f4f4f4;padding:16px 32px;border-top:1px solid #e8e8e8;">';
        $message .= '<p style="margin:0;font-size:12px;color:#aaaaaa;">Αυτό το email στάλθηκε αυτόματα από το ' . esc_html( $site_name ) . '.</p>';
        $message .= '</td></tr>';

        $message .= '</table></td></tr></table></body></html>';

        $set_html_type = function() { return 'text/html'; };
        add_filter( 'wp_mail_content_type', $set_html_type );
        wp_mail( $admin_email, $subject, $message );

        // Email στον χρήστη: "Λάβαμε την αίτησή σας"
        $user_subject = sprintf( __( 'Λάβαμε την αίτησή σας — %s', 'ruined' ), $site_name );
        $user_message  = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">';
        $user_message .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:32px 0;">';
        $user_message .= '<tr><td align="center">';
        $user_message .= '<table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;">';
        $user_message .= '<tr><td style="background:#1a1a1a;padding:24px 32px;">';
        $user_message .= '<p style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">' . esc_html( $site_name ) . '</p>';
        $user_message .= '<p style="margin:4px 0 0;color:#aaaaaa;font-size:13px;">Αίτηση εγγραφής</p>';
        $user_message .= '</td></tr>';
        $user_message .= '<tr><td style="padding:32px;">';
        $user_message .= '<p style="margin:0 0 16px;font-size:15px;color:#333333;">Γεια σας <strong>' . esc_html( $entity_name ) . '</strong>,</p>';
        $user_message .= '<p style="margin:0 0 16px;font-size:15px;color:#333333;">Λάβαμε την αίτησή σας για εγγραφή στην πλατφόρμα μας.</p>';
        $user_message .= '<p style="margin:0 0 24px;font-size:15px;color:#333333;">Η ομάδα μας θα την εξετάσει σύντομα και θα σας ειδοποιήσουμε μέσω email μόλις ο λογαριασμός σας ενεργοποιηθεί.</p>';
        $user_message .= '<div style="background:#f8f8f8;border-left:3px solid #1a1a1a;padding:14px 18px;border-radius:0 4px 4px 0;font-size:13px;color:#555555;">';
        $user_message .= 'Μέχρι τότε ο λογαριασμός σας βρίσκεται σε αναμονή έγκρισης.';
        $user_message .= '</div>';
        $user_message .= '</td></tr>';
        $user_message .= '<tr><td style="background:#f4f4f4;padding:16px 32px;border-top:1px solid #e8e8e8;">';
        $user_message .= '<p style="margin:0;font-size:12px;color:#aaaaaa;">' . esc_html( $site_name ) . ' &mdash; Αυτόματη ειδοποίηση, παρακαλώ μην απαντάτε σε αυτό το email.</p>';
        $user_message .= '</td></tr>';
        $user_message .= '</table></td></tr></table></body></html>';

        wp_mail( $email, $user_subject, $user_message );
        remove_filter( 'wp_mail_content_type', $set_html_type );

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