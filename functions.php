<?php
/**
 * Ruined Theme functions and definitions
 *
 * @package Ruined
 */

if (!defined('RUINED_VERSION')) {
    define('RUINED_VERSION', '1.0.0');
}

// Register shop sidebar
add_action('widgets_init', function() {
    register_sidebar(array(
        'name'          => esc_html__('Shop Sidebar', 'ruined'),
        'id'            => 'sidebar-shop',
        'description'   => esc_html__('Add widgets here to appear in your shop sidebar.', 'ruined'),
        'before_widget' => '<section id="%1$s" class="widget %2$s mb-8">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title text-lg font-semibold mb-4">',
        'after_title'   => '</h3>',
    ));
});

// --- Theme Includes ---
$includes = [
    'includes/theme-setup.php',       // Theme setup and features
    'includes/vite.php',              // Vite asset loading
    'includes/admin.php',             // Admin area customizations
    'includes/shortcodes.php',        // Custom shortcodes
    'includes/utilities.php',         // Utility functions
    'includes/acf.php',               // Advanced Custom Fields configuration
    'includes/account-roles/login_forms.php',       // Custom Forms for login
    'includes/account-roles/account-roles.php',       // Custom Forms for login
    'includes/blocks.php',            // Custom Gutenberg blocks
    'includes/woocommerce/configurations.php',       // WooCommerce customizations
    'includes/woocommerce/load-more.php',       // WooCommerce load more
    'pages-hero.php',
];


// Load theme files
foreach ($includes as $file) {
    $filepath = get_template_directory() . '/' . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

add_action('wp_head', function () {
	?>
	<script>
        window.ajaxurl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
        window.ruined_nonce = "<?php echo wp_create_nonce('ruined_cart_nonce'); ?>";
        window.sigma_login_nonce = "<?php echo wp_create_nonce('sigma-login'); ?>";
        window.sigma_register_nonce = "<?php echo wp_create_nonce('sigma-register'); ?>";
	</script>
	<?php
});



add_filter('wc_get_template', function($template, $template_name, $args, $template_path, $default_path) {
    if ($template_name === 'cart/mini-cart.php') {
        error_log('Loading mini-cart template from: ' . $template);
    }
    return $template;
}, 10, 5);

// Dequeue conflicting YITH Wishlist scripts for better performance
add_action('wp_enqueue_scripts', function() {
    // Only dequeue if YITH plugin is active to avoid errors
    if (class_exists('YITH_WCWL_Frontend')) {
        // Dequeue ALL YITH scripts to prevent conflicts
        wp_dequeue_script('yith-wcwl-main');
        wp_dequeue_script('yith-wcwl-ajax');
        wp_dequeue_script('yith-wcwl-add-to-wishlist');
        wp_dequeue_script('yith-wcwl-jquery-ui-dialog');
        wp_dequeue_script('yith-wcwl-frontend');
        
        // Dequeue styles that may conflict with our theme's styling
        wp_dequeue_style('yith-wcwl-main');
        wp_dequeue_style('yith-wcwl-font-awesome');
        wp_dequeue_style('yith-wcwl-jquery-ui');
        wp_dequeue_style('yith-wcwl-frontend');
        
        // Also dequeue any lodash conflicts
        wp_dequeue_script('lodash');
        wp_dequeue_script('lodash-js');
    }
}, 99);

