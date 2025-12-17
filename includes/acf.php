<?php
/**
 * Advanced Custom Fields (ACF) Configuration
 */

// 1. Set ACF JSON save directory
add_filter('acf/settings/save_json', function() {
    // Path to save ACF JSON files
    $path = get_stylesheet_directory() . '/includes/acf-json';
    
    // Create directory if it doesn't exist
    if (!file_exists($path)) {
        wp_mkdir_p($path);
    }
    
    return $path;
});

// 2. Set ACF JSON load directory
add_filter('acf/settings/load_json', function($paths) {
    // Remove default path
    unset($paths[0]);
    
    // Add our custom path
    $paths[] = get_stylesheet_directory() . '/includes/acf-json';
    
    return $paths;
});

// 3. Auto-sync ACF fields on save (works in all environments)
add_filter('acf/settings/save_json/type=acf-field-group', function() {
    return get_stylesheet_directory() . '/includes/acf-json';
});

// 4. Auto-load ACF JSON files
add_action('acf/init', function() {
    // This ensures JSON files are loaded on every page load
    if (function_exists('acf_get_setting')) {
        $paths = apply_filters('acf/settings/load_json', array());
        if (is_array($paths)) {
            foreach ($paths as $path) {
                if (is_dir($path)) {
                    acf_update_setting('acfe/php_save', $path . '/');
                }
            }
        }
    }
});


