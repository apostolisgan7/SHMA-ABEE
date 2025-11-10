<?php
/**
 * Ruined Theme functions and definitions
 *
 * @package Ruined
 */

if (!defined('RUINED_VERSION')) {
    define('RUINED_VERSION', '1.0.0');
}

/**
 * Add theme support for custom logo
 */
function ruined_theme_support() {
    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    ));
}
add_action('after_setup_theme', 'ruined_theme_support');

// --- Theme Includes ---
$includes = [
    'includes/theme-setup.php',       // Theme setup and features
    'includes/vite.php',              // Vite asset loading
    'includes/admin.php',             // Admin area customizations
    'includes/shortcodes.php',        // Custom shortcodes
    'includes/utilities.php',         // Utility functions
    'includes/acf.php',               // Advanced Custom Fields configuration
    'includes/blocks.php',            // Custom Gutenberg blocks
];

// Ensure WooCommerce templates are loaded from plugin
add_filter('woocommerce_locate_template', 'ruined_woocommerce_locate_template', 10, 3);
function ruined_woocommerce_locate_template($template, $template_name, $template_path) {
    // Use default WooCommerce templates from plugin
    $default_path = WP_PLUGIN_DIR . '/woocommerce/templates/';
    if (file_exists($default_path . $template_name)) {
        return $default_path . $template_name;
    }
    return $template;
}

foreach ($includes as $file) {
	$filepath = get_template_directory() . '/' . $file;
	if (file_exists($filepath)) {
		require_once $filepath;
	}
}


add_action('after_setup_theme', function() {
	add_theme_support('woocommerce');
});

