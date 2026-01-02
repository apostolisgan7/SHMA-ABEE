<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * --------------------------------------------------
 * Rate limiting helpers (transients)
 * --------------------------------------------------
 */
function sigma_get_client_ip() {
	// Αν έχεις Cloudflare/Proxy μπορεί να θες HTTP_CF_CONNECTING_IP κλπ.
	return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function sigma_rl_key( $prefix, $extra = '' ) {
	$ip = sigma_get_client_ip();
	return $prefix . '_' . md5( $ip . '|' . $extra );
}

/**
 * @return array{allowed:bool, retry_after:int}
 */
function sigma_rate_limit_check( $key, $limit = 5, $window = 600 ) {
	$data = get_transient( $key );

	if ( ! is_array($data) ) {
		$data = [
			'count' => 0,
			'start' => time(),
		];
	}

	$now = time();
	$elapsed = $now - (int) $data['start'];

	// αν πέρασε το window, reset
	if ( $elapsed >= $window ) {
		$data = [ 'count' => 0, 'start' => $now ];
		set_transient( $key, $data, $window );
		return [ 'allowed' => true, 'retry_after' => 0 ];
	}

	if ( (int)$data['count'] >= $limit ) {
		$retry_after = max(0, $window - $elapsed);
		return [ 'allowed' => false, 'retry_after' => $retry_after ];
	}

	// allow
	return [ 'allowed' => true, 'retry_after' => 0 ];
}

function sigma_rate_limit_hit( $key, $window = 600 ) {
	$data = get_transient( $key );

	if ( ! is_array($data) ) {
		$data = [ 'count' => 0, 'start' => time() ];
	}

	$data['count'] = (int)$data['count'] + 1;

	$ttl = max(1, $window - (time() - (int)$data['start']));
	set_transient( $key, $data, $ttl );
}

/**
 * --------------------------------------------------
 * VIES caching wrapper
 * --------------------------------------------------
 * Cache και για valid και για invalid για να μη βαράς συνέχεια το VIES.
 */
function sigma_vies_cached_check_el_vat( $vat_digits ) {

	$vat_digits = preg_replace('/\D/', '', (string)$vat_digits);

	// cache key
	$t_key = 'sigma_vies_el_' . $vat_digits;
	$cached = get_transient( $t_key );

	if ( is_array($cached) && isset($cached['valid']) ) {
		return $cached; // ['valid'=>bool,'message'=>string]
	}

	// Κάνε το πραγματικό check
	$valid = function_exists('sigma_validate_vat_vies')
		? (bool) sigma_validate_vat_vies('EL', $vat_digits)
		: false;

	$result = [
		'valid'   => $valid,
		'message' => $valid
			? __('Έγκυρο ΑΦΜ μέσω VIES.', 'ruined')
			: __('Το ΑΦΜ δεν είναι έγκυρο στο VIES.', 'ruined')
	];

	// TTL: valid 6 ώρες, invalid 1 ώρα (για να επιτρέπεις διορθώσεις)
	$ttl = $valid ? 6 * HOUR_IN_SECONDS : 1 * HOUR_IN_SECONDS;
	set_transient( $t_key, $result, $ttl );

	return $result;
}


/**
 * --------------------------------------------------
 * AJAX: sigma_check_vat (blur check)
 * --------------------------------------------------
 */
add_action('wp_ajax_sigma_check_vat', 'sigma_ajax_check_vat');
add_action('wp_ajax_nopriv_sigma_check_vat', 'sigma_ajax_check_vat');

function sigma_ajax_check_vat() {

	// ✅ nonce (χρησιμοποίησε το ίδιο hidden nonce που έχεις στο register form)
	if ( ! check_ajax_referer( 'sigma-register', 'nonce', false ) ) {
		wp_send_json_error([
			'message' => __('Security error.', 'ruined')
		], 403);
	}

	// ✅ rate limit (VAT checks) — 10 checks / 5 λεπτά / IP
	$rl_key = sigma_rl_key('sigma_vat_check');
	$rl = sigma_rate_limit_check( $rl_key, 10, 300 );

	if ( ! $rl['allowed'] ) {
		wp_send_json_error([
			'message' => sprintf(
				__('Πολλές προσπάθειες. Δοκίμασε ξανά σε %d δευτ.', 'ruined'),
				(int)$rl['retry_after']
			),
			'retry_after' => (int)$rl['retry_after']
		], 429);
	}

	$vat = isset($_POST['vat']) ? (string) $_POST['vat'] : '';
	$vat_digits = preg_replace('/\D/', '', $vat);

	if ( $vat_digits === '' ) {
		sigma_rate_limit_hit( $rl_key, 300 );
		wp_send_json_error([
			'message' => __('Το ΑΦΜ είναι υποχρεωτικό.', 'ruined')
		], 400);
	}

	// Ελλάδα → 9 ψηφία
	if ( strlen($vat_digits) !== 9 ) {
		sigma_rate_limit_hit( $rl_key, 300 );
		wp_send_json_error([
			'message' => __('Το ελληνικό ΑΦΜ πρέπει να έχει 9 ψηφία.', 'ruined')
		], 400);
	}

	if ( ! function_exists('sigma_validate_vat_vies') ) {
		sigma_rate_limit_hit( $rl_key, 300 );
		wp_send_json_error([
			'message' => __('Σφάλμα συστήματος.', 'ruined')
		], 500);
	}

	// ✅ cached check
	$check = sigma_vies_cached_check_el_vat( $vat_digits );

	// count attempt only when actually trying (valid length)
	sigma_rate_limit_hit( $rl_key, 300 );

	if ( ! $check['valid'] ) {
		wp_send_json_error([
			'message' => $check['message']
		], 200);
	}

	wp_send_json_success([
		'message' => $check['message']
	], 200);
}
