<?php
if (!defined('ABSPATH')) exit;

/**
 * --------------------------------------------------
 * Rate limiting helpers (transients)
 * --------------------------------------------------
 */
function sigma_get_client_ip() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function sigma_rl_key($prefix, $extra = '') {
    $ip = sigma_get_client_ip();
    return $prefix . '_' . md5($ip . '|' . $extra);
}

/**
 * @return array{allowed:bool, retry_after:int}
 */
function sigma_rate_limit_check($key, $limit = 5, $window = 600) {

    $data = get_transient($key);

    if (!is_array($data)) {
        $data = [
            'count' => 0,
            'start' => time(),
        ];
    }

    $now     = time();
    $elapsed = $now - (int)$data['start'];

    // reset window
    if ($elapsed >= $window) {
        $data = ['count' => 0, 'start' => $now];
        set_transient($key, $data, $window);

        return ['allowed' => true, 'retry_after' => 0];
    }

    if ((int)$data['count'] >= $limit) {
        $retry_after = max(0, $window - $elapsed);
        return ['allowed' => false, 'retry_after' => $retry_after];
    }

    return ['allowed' => true, 'retry_after' => 0];
}

function sigma_rate_limit_hit($key, $window = 600) {

    $data = get_transient($key);

    if (!is_array($data)) {
        $data = ['count' => 0, 'start' => time()];
    }

    $data['count']++;

    $ttl = max(1, $window - (time() - (int)$data['start']));
    set_transient($key, $data, $ttl);
}

/**
 * --------------------------------------------------
 * VIES caching wrapper (Companies only)
 * --------------------------------------------------
 */
function sigma_vies_cached_check_el_vat($vat_digits) {

    $vat_digits = preg_replace('/\D/', '', (string)$vat_digits);

    $t_key   = 'sigma_vies_el_' . $vat_digits;
    $cached  = get_transient($t_key);

    if (is_array($cached) && isset($cached['valid'])) {
        return $cached;
    }

    $valid = function_exists('sigma_validate_vat_vies')
        ? (bool) sigma_validate_vat_vies('EL', $vat_digits)
        : false;

    $result = [
        'valid'   => $valid,
        'message' => $valid
            ? __('Έγκυρο ΑΦΜ μέσω VIES.', 'ruined')
            : __('Το ΑΦΜ δεν είναι έγκυρο στο VIES.', 'ruined')
    ];

    // TTL: valid 6h, invalid 1h
    $ttl = $valid ? 6 * HOUR_IN_SECONDS : 1 * HOUR_IN_SECONDS;
    set_transient($t_key, $result, $ttl);

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

    // ✅ Nonce
    if (!check_ajax_referer('sigma-register', 'nonce', false)) {
        wp_send_json_error([
            'message' => __('Security error.', 'ruined')
        ], 403);
    }

    // ✅ Rate limit (10 checks / 5 min)
    $rl_key = sigma_rl_key('sigma_vat_check');
    $rl     = sigma_rate_limit_check($rl_key, 10, 300);

    if (!$rl['allowed']) {
        wp_send_json_error([
            'message' => sprintf(
                __('Πολλές προσπάθειες. Δοκίμασε ξανά σε %d δευτ.', 'ruined'),
                (int)$rl['retry_after']
            ),
        ], 429);
    }

    // Inputs
    $vat        = isset($_POST['vat']) ? (string) $_POST['vat'] : '';
    $vat_digits = preg_replace('/\D/', '', $vat);

    $role = isset($_POST['role'])
        ? sanitize_text_field($_POST['role'])
        : 'customer';

    /**
     * --------------------------------------------------
     * 1) Empty VAT
     * --------------------------------------------------
     */
    if ($vat_digits === '') {
        sigma_rate_limit_hit($rl_key, 300);

        wp_send_json_error([
            'message' => __('Το ΑΦΜ είναι υποχρεωτικό.', 'ruined')
        ], 400);
    }

    /**
     * --------------------------------------------------
     * 2) Ελλάδα → πάντα 9 ψηφία (όλοι)
     * --------------------------------------------------
     */
    if (strlen($vat_digits) !== 9) {
        sigma_rate_limit_hit($rl_key, 300);

        wp_send_json_error([
            'message' => __('Το ελληνικό ΑΦΜ πρέπει να έχει 9 ψηφία.', 'ruined')
        ], 400);
    }

    /**
     * --------------------------------------------------
     * 3) Δήμος → no VIES (μόνο format check)
     * --------------------------------------------------
     */
    if ($role === 'municipality') {

        sigma_rate_limit_hit($rl_key, 300);

        wp_send_json_success([
            'message' => __('Οι Δήμοι δεν επιβεβαιώνονται μέσω VIES. Το ΑΦΜ καταχωρήθηκε.', 'ruined')
        ], 200);
    }

    /**
     * --------------------------------------------------
     * 4) Customer → skip VAT
     * --------------------------------------------------
     */
    if ($role === 'customer') {
        wp_send_json_success([
            'message' => __('OK', 'ruined')
        ], 200);
    }

    /**
     * --------------------------------------------------
     * 5) Company → VIES Required
     * --------------------------------------------------
     */
    if (!function_exists('sigma_validate_vat_vies')) {
        sigma_rate_limit_hit($rl_key, 300);

        wp_send_json_error([
            'message' => __('Σφάλμα συστήματος.', 'ruined')
        ], 500);
    }

    $check = sigma_vies_cached_check_el_vat($vat_digits);

    sigma_rate_limit_hit($rl_key, 300);

    if (!$check['valid']) {
        wp_send_json_error([
            'message' => $check['message']
        ], 200);
    }

    wp_send_json_success([
        'message' => $check['message']
    ], 200);
}
