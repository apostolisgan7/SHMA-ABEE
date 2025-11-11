<?php
/**
 * Template functions and definitions
 *
 * @package Ruined
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add sticky header class to body if enabled in customizer
 */
function ruined_add_sticky_header_class($classes) {
    if (get_theme_mod('ruined_sticky_header', false)) {
        $classes[] = 'has-sticky-header';
    }
    return $classes;
}
add_filter('body_class', 'ruined_add_sticky_header_class');
