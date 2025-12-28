<?php
if ( ! defined('ABSPATH') ) exit;

add_action('wp_ajax_sigma_check_vat', 'sigma_ajax_check_vat');
add_action('wp_ajax_nopriv_sigma_check_vat', 'sigma_ajax_check_vat');

function sigma_ajax_check_vat() {

	if ( empty($_POST['vat']) ) {
		wp_send_json_error([
			'message' => __('Το ΑΦΜ είναι υποχρεωτικό.', 'ruined')
		]);
	}

	$vat = preg_replace('/\D/', '', $_POST['vat']);

	if ( strlen($vat) !== 9 ) {
		wp_send_json_error([
			'message' => __('Το ελληνικό ΑΦΜ πρέπει να έχει 9 ψηφία.', 'ruined')
		]);
	}

	if ( ! function_exists('sigma_validate_vat_vies') ) {
		wp_send_json_error([
			'message' => __('Σφάλμα συστήματος.', 'ruined')
		]);
	}

	if ( ! sigma_validate_vat_vies('EL', $vat) ) {
		wp_send_json_error([
			'message' => __('Το ΑΦΜ δεν είναι έγκυρο στο VIES.', 'ruined')
		]);
	}

	wp_send_json_success([
		'message' => __('Έγκυρο ΑΦΜ μέσω VIES', 'ruined')
	]);
}
