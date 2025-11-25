<?php
/**
 * Theme setup and support - Minimal configuration
 */
add_action('after_setup_theme', 'ruined_theme_setup');
add_action('init', 'ruined_add_post_thumbnail_support', 0); // Changed priority to 0 to run earlier
add_action('current_screen', 'ruined_ensure_featured_image_support'); // Changed to current_screen hook

function ruined_theme_setup() {
    // Let WordPress manage the document title
    add_theme_support('title-tag');
    
    // Add support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add support for custom logo
    add_theme_support('custom-logo', [
        'height'      => 190,
        'width'       => 190,
        'flex-width'  => true,
        'flex-height' => true,
    ]);
    
    // WooCommerce support (if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
    }
    
    // Register navigation menus
    register_nav_menus([
        'primary'      => __('Primary Menu', 'ruined'),
        'catalog-menu' => __('Catalog Menu', 'ruined'),
        'mobile-menu'  => __('Mobile Menu', 'ruined'),
        'social-menu' => __('Social Menu', 'ruined'),
        'support-menu'  => __('Support / Contact Menu', 'ruined'),
        'bottom-main-menu' => __('Main Menu Bottom', 'ruined'),
    ]);
}

/**
 * Ensure post thumbnail support is enabled for all public post types
 */
function ruined_add_post_thumbnail_support() {
    $post_types = get_post_types(['public' => true], 'names');
    
    foreach ($post_types as $post_type) {
        if (!post_type_supports($post_type, 'thumbnail')) {
            add_post_type_support($post_type, 'thumbnail');
        }
    }
}

/**
 * Ensure featured image support is enabled for posts and pages
 */
function ruined_ensure_featured_image_support() {
    // Only run in admin and on post edit screens
    if (!is_admin()) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->base, ['post', 'edit'])) {
        return;
    }
    
    // Get the post type
    $post_type = $screen->post_type;
    
    // If it's a post or page, ensure thumbnail support is enabled
    if (in_array($post_type, ['post', 'page'])) {
        if (!post_type_supports($post_type, 'thumbnail')) {
            add_post_type_support($post_type, 'thumbnail');
        }
        
        // Force the meta box to be shown
        add_meta_box(
            'postimagediv',
            __('Featured Image'),
            'post_thumbnail_meta_box',
            $post_type,
            'side',
            'low'
        );
    }
}



function ruined_register_page_templates($templates) {
    // Define the directory to scan for template files
    $theme_directory = get_stylesheet_directory();
    $template_files = [];

    // Scan the theme directory for PHP files that might be templates
    $files = glob($theme_directory . '/*.php');

    foreach ($files as $file) {
        // Skip if not a PHP file
        if (!preg_match('/\.php$/', $file)) {
            continue;
        }

        // Skip specific files that shouldn't be templates
        $skip_files = ['functions.php', 'header.php', 'footer.php', 'sidebar.php', 'index.php', 'style.css'];
        if (in_array(basename($file), $skip_files)) {
            continue;
        }

        // Get file content to check for template header
        $file_content = file_get_contents($file);

        // Check if the file has a template name header
        if (preg_match('/Template\s*Name:\s*(.*)$/mi', $file_content, $matches)) {
            $template_name = trim($matches[1]);
            $template_file = basename($file);
            $templates[$template_file] = $template_name;
        }
    }

    return $templates;
}
add_filter('theme_page_templates', 'ruined_register_page_templates');
