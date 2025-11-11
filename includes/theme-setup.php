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
