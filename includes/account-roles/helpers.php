<?php
if (!defined('ABSPATH')) exit;

function sigma_user_has_role($role) {
	if (!is_user_logged_in()) return false;
	return in_array($role, wp_get_current_user()->roles, true);
}

function sigma_is_b2c() {
	return sigma_user_has_role('customer');
}

function sigma_is_company() {
	return sigma_user_has_role('company');
}

function sigma_is_municipality() {
	return sigma_user_has_role('municipality');
}


// Στο helpers.php
function sigma_verify_recaptcha($token) {
    if (empty($token)) return false;

    $secret_key = '6LcSbFAsAAAAAGhHqTtmRGzSX23baw26AWhSFC-K';
    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
        'body' => [
            'secret'   => $secret_key,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]
    ]);

    $body = json_decode(wp_remote_retrieve_body($response));

    // Στο v3 ελέγχουμε το success ΚΑΙ το score (συνήθως > 0.5)
    return (isset($body->success) && $body->success === true && $body->score >= 0.5);
}
