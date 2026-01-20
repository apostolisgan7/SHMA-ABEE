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
	</script>
	<?php
});


wp_enqueue_script(
    'mobile-menu',
    get_theme_file_uri('/mobile-menu.js'),
    array(),
    null,
    true
);



add_filter('wc_get_template', function($template, $template_name, $args, $template_path, $default_path) {
    if ($template_name === 'cart/mini-cart.php') {
        error_log('Loading mini-cart template from: ' . $template);
    }
    return $template;
}, 10, 5);


add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=6LcSbFAsAAAAAPDqcEYBbJhjnu3kDFzkyftOx5ut', array(), null, true);
});
