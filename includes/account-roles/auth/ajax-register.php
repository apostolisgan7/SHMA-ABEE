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
	$user_id = wc_create_new_customer(
		sanitize_email( $_POST['email'] ?? '' ),
		'',
		$_POST['password'] ?? ''
	);

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

	// ✅ VAT feedback (από cached VIES)
	$vat_valid   = false;
	$vat_message = '';

	if ( ! empty( $_POST['vat'] ) ) {
		$vat_digits = preg_replace('/\D/', '', $_POST['vat']);
		$check      = sigma_vies_cached_check_el_vat( $vat_digits );

		$vat_valid   = (bool) $check['valid'];
		$vat_message = $check['message'];
	}

	wp_send_json_success([
		'redirect'    => wc_get_page_permalink( 'myaccount' ),
		'vat_valid'   => $vat_valid,
		'vat_message' => $vat_message,
	]);
}
