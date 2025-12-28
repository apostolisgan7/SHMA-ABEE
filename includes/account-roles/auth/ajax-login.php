<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_sigma_login', 'sigma_ajax_login');
add_action('wp_ajax_nopriv_sigma_login', 'sigma_ajax_login');

function sigma_ajax_login() {

	if (!check_ajax_referer('sigma-login', 'nonce', false)) {
		wp_send_json_error([
			'html' => '<ul class="woocommerce-error"><li>Security error.</li></ul>'
		]);
	}

	if (empty($_POST['username']) || empty($_POST['password'])) {
		wc_add_notice(__('Συμπλήρωσε email και κωδικό.', 'ruined'), 'error');
		ob_start();
		wc_print_notices();
		wp_send_json_error(['html' => ob_get_clean()]);
	}

	$creds = [
		'user_login'    => sanitize_text_field($_POST['username']),
		'user_password' => $_POST['password'],
		'remember'      => true,
	];

	$user = wp_signon($creds, is_ssl());

	if (is_wp_error($user)) {
		wc_add_notice($user->get_error_message(), 'error');
		ob_start();
		wc_print_notices();
		wp_send_json_error(['html' => ob_get_clean()]);
	}

	// ✅ LOGIN OK
	$redirect = apply_filters(
		'woocommerce_login_redirect',
		wc_get_page_permalink('myaccount'),
		$user
	);

	wp_send_json_success([
		'redirect' => esc_url($redirect)
	]);
}
