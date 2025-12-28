<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_sigma_register', 'sigma_ajax_register');
add_action('wp_ajax_nopriv_sigma_register', 'sigma_ajax_register');

function sigma_ajax_register() {

	if ( ! check_ajax_referer( 'sigma-register', 'nonce', false ) ) {
		wp_send_json_error([
			'html' => '<ul class="woocommerce-error"><li>Security error</li></ul>'
		]);
	}

	wc_clear_notices();

	// 🔁 Αφήνουμε το Woo να τρέξει ΟΛΟ το validation
	$errors = apply_filters( 'woocommerce_registration_errors', new WP_Error() );

	if ( $errors->has_errors() ) {
		foreach ( $errors->get_error_messages() as $msg ) {
			wc_add_notice( $msg, 'error' );
		}

		ob_start();
		wc_print_notices();
		wp_send_json_error([ 'html' => ob_get_clean() ]);
	}

	// Create user
	$user_id = wc_create_new_customer(
		sanitize_email($_POST['email']),
		'',
		$_POST['password']
	);

	if ( is_wp_error( $user_id ) ) {
		wc_add_notice( $user_id->get_error_message(), 'error');
		ob_start();
		wc_print_notices();
		wp_send_json_error([ 'html' => ob_get_clean() ]);
	}

	// Auto-login
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );

	$vat_valid = false;
	$vat_message = '';

	if ( ! empty( $_POST['vat'] ) ) {
		$vat_valid   = true;
		$vat_message = __( 'Το ΑΦΜ είναι έγκυρο μέσω VIES.', 'ruined' );
	}

	wp_send_json_success([
		'redirect'     => wc_get_page_permalink('myaccount'),
		'vat_valid'    => $vat_valid,
		'vat_message'  => $vat_message,
	]);

}
