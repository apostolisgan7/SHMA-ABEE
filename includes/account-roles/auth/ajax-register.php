<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_sigma_register', 'sigma_ajax_register' );
add_action( 'wp_ajax_nopriv_sigma_register', 'sigma_ajax_register' );

function sigma_ajax_register() {

	// 🔐 Nonce
	if ( ! check_ajax_referer( 'sigma-register', 'nonce', false ) ) {
		wp_send_json_error([
			'html' => '<ul class="woocommerce-error"><li>Security error.</li></ul>'
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
        $errors->add('invalid_type', 'Invalid customer type.');
    }

// Κοινά
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    if ( empty($phone) ) {
        $errors->add('phone_required', 'Το τηλέφωνο είναι υποχρεωτικό.');
    } elseif (!preg_match('/^[+]?[\d\s\-\(\)]{7,15}$/', $phone)) {
        $errors->add('phone_format', 'Μη έγκυρη μορφή τηλεφώνου.');
    }

// B2C
    if ( $type === 'customer' ) {
        $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
        if ( empty($customer_name) ) {
            $errors->add('name_required', 'Το ονοματεπώνυμο είναι υποχρεωτικό.');
        }
    }

// Company / Municipality
    if ( in_array($type, ['company', 'municipality'], true) ) {

        if ( $type === 'company' ) {
            $company_name = isset($_POST['company_name']) ? sanitize_text_field($_POST['company_name']) : '';
            if ( empty($company_name) ) {
                $errors->add('company_required', 'Η επωνυμία εταιρείας είναι υποχρεωτική.');
            }
        }

        if ( $type === 'municipality' ) {
            $municipality_name = isset($_POST['municipality_name']) ? sanitize_text_field($_POST['municipality_name']) : '';
            if ( empty($municipality_name) ) {
                $errors->add('municipality_required', 'Το όνομα δήμου είναι υποχρεωτικό.');
            }
        }

        $vat = isset($_POST['vat']) ? sanitize_text_field($_POST['vat']) : '';
        if (empty($vat)) {
            $errors->add('vat_required', 'Το ΑΦΜ είναι υποχρεωτικό.');
        } else {

            $vat = preg_replace('/\D/', '', $vat);

            // 9 digits always
            if (strlen($vat) !== 9) {
                $errors->add('vat_format', 'Το ελληνικό ΑΦΜ πρέπει να έχει 9 ψηφία.');
            }

            // ✅ VIES ONLY for companies
            if ($type === 'company') {

                if (! sigma_validate_vat_vies('EL', $vat)) {
                    $errors->add(
                        'vat_invalid',
                        'Το ΑΦΜ δεν είναι έγκυρο στο VIES (απαιτείται μόνο για εταιρείες).'
                    );
                }
            }
        }
    }

// ⛔ Αν υπάρχουν errors → κόψτο εδώ
    if ( $errors->has_errors() ) {
        sigma_rate_limit_hit( $rl_key, 600 );

        foreach ( $errors->get_error_messages() as $msg ) {
            wc_add_notice( $msg, 'error' );
        }

        ob_start();
        wc_print_notices();

        wp_send_json_error([
            'html' => ob_get_clean()
        ], 400);
    }



	wc_clear_notices();

	// 🔁 WooCommerce validation (email, password, custom fields, VAT κλπ)
	$errors = apply_filters( 'woocommerce_registration_errors', new WP_Error() );

	if ( $errors->has_errors() ) {
		sigma_rate_limit_hit( $rl_key, 600 );

		foreach ( $errors->get_error_messages() as $msg ) {
			wc_add_notice( $msg, 'error' );
		}

		ob_start();
		wc_print_notices();
		wp_send_json_error([ 'html' => ob_get_clean() ]);
	}

	// 👤 Create user
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || !is_email($email)) {
        $errors->add('invalid_email', 'Παρακαλώ δώστε ένα έγκυρο email.');
    }
    
    if (empty($password) || strlen($password) < 8) {
        $errors->add('weak_password', 'Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.');
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

	$user_id = wc_create_new_customer( $email, '', $password );

	if ( is_wp_error( $user_id ) ) {
		sigma_rate_limit_hit( $rl_key, 600 );

		wc_add_notice( $user_id->get_error_message(), 'error' );
		ob_start();
		wc_print_notices();
		wp_send_json_error([ 'html' => ob_get_clean() ]);
	}

	// 🔐 Auto-login
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );

// ✅ VAT feedback
    $vat_valid   = false;
    $vat_message = '';

    if ( ! empty($_POST['vat']) ) {

        $vat_digits = preg_replace('/\D/', '', $_POST['vat']);

        // Company → VIES
        if ( $type === 'company' ) {

            $check = sigma_vies_cached_check_el_vat($vat_digits);

            $vat_valid   = (bool) $check['valid'];
            $vat_message = $check['message'];

        }

        // Municipality → No VIES
        if ( $type === 'municipality' ) {

            $vat_valid   = true;
            $vat_message = 'Το ΑΦΜ καταχωρήθηκε (δεν απαιτείται VIES για Δήμους).';

        }
    }


	wp_send_json_success([
		'redirect'    => wc_get_page_permalink( 'myaccount' ),
		'vat_valid'   => $vat_valid,
		'vat_message' => $vat_message,
	]);
}
