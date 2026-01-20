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

