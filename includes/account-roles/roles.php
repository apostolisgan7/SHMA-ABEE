<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

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
