<?php

// --- Theme Setup ---
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Basic WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

    // Register Nav Menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'ruined'),
    ]);
});

// Add WooCommerce body classes
add_filter('body_class', function($classes) {
    if (class_exists('WooCommerce')) {
        $classes[] = 'woocommerce';
        $classes[] = 'woocommerce-page';
        
        // Add shop view class
        if (is_shop() || is_product_category() || is_product_tag()) {
            $classes[] = 'woocommerce-shop';
        }
    }
    return $classes;
});
