<?php
/**
 * Theme setup and support - Minimal configuration
 */
add_action('after_setup_theme', 'ruined_theme_setup');
add_action('init', 'ruined_add_post_thumbnail_support', 999);
add_action('admin_init', 'ruined_ensure_featured_image_support');

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
 * Forcefully ensure featured image support is enabled for posts and pages
 */
function ruined_ensure_featured_image_support() {
    // List of post types that should support featured images
    $post_types = ['post', 'page'];
    
    foreach ($post_types as $post_type) {
        // Add thumbnail support if it doesn't exist
        if (!post_type_supports($post_type, 'thumbnail')) {
            add_post_type_support($post_type, 'thumbnail');
        }
    }
    
    // Make sure the featured image meta box is shown
    $hidden_meta_boxes = get_user_meta(get_current_user_id(), 'metaboxhidden_post', true);
    if (is_array($hidden_meta_boxes)) {
        $hidden_meta_boxes = array_diff($hidden_meta_boxes, ['postimagediv']);
        update_user_meta(get_current_user_id(), 'metaboxhidden_post', $hidden_meta_boxes);
    }
}
