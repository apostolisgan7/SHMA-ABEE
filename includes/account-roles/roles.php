<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

	add_role(
		'customer_b2c',
		'Customer (B2C)',
		['read' => true]
	);

	add_role(
		'company',
		'Company',
		['read' => true]
	);

	add_role(
		'municipality',
		'Municipality',
		['read' => true]
	);

});
