<?php
/**
 * Core utility functions for the Ruined theme
 *
 * @package Ruined
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Development Utilities
 */

/**
 * Print formatted data for debugging
 *
 * @param mixed $data Data to print
 * @param bool $die Whether to terminate script execution
 */
function ruined_debug($data, $die = false) {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    echo '<pre style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd; border-radius: 4px; overflow: auto; font-size: 13px; line-height: 1.5; margin: 20px 0;">';
    is_array($data) || is_object($data) ? print_r($data) : var_dump($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * Log data to WordPress debug log
 *
 * @param mixed $data Data to log
 * @param string $prefix Optional prefix for the log entry
 */
function ruined_log($data, $prefix = '') {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $prefix = $prefix ? $prefix . ': ' : '';
        if (is_array($data) || is_object($data)) {
            error_log($prefix . print_r($data, true));
        } else {
            error_log($prefix . $data);
        }
    }
}

/**
 * String Utilities
 */

/**
 * Limit words in a string
 *
 * @param string $string Input string
 * @param int $word_limit Number of words to return
 * @param string $more Text to append if string is truncated
 * @return string
 */
function ruined_limit_words($string, $word_limit, $more = '...') {
    $words = preg_split('/\s+/', $string);
    if (count($words) > $word_limit) {
        return implode(' ', array_slice($words, 0, $word_limit)) . $more;
    }
    return $string;
}

/**
 * Array Utilities
 */

/**
 * Get a value from an array with an optional default
 *
 * @param array  $array   Array to get value from
 * @param string $key     Key to look for
 * @param mixed  $default Default value if key doesn't exist
 * @return mixed
 */
function ruined_array_get($array, $key, $default = null) {
    return $array[$key] ?? $default;
}

/**
 * Check if a file is an image
 *
 * @param string $file_path
 * @return bool
 */
function ruined_is_image($file_path) {
    $ext = strtolower(ruined_get_file_extension($file_path));
    $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    return in_array($ext, $image_exts, true);
}

/**
 * URL Utilities
 */

/**
 * Get the current URL
 *
 * @param bool $full Whether to include query string
 * @return string
 */
function ruined_get_current_url($full = true) {
    if ($full) {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REDIRECT_URL]";
}

/**
 * Check if the current page is a specific post type
 *
 * @param string $post_type
 * @return bool
 */
function ruined_is_post_type($post_type) {
    global $post;
    return (is_single() && $post->post_type === $post_type) || is_post_type_archive($post_type);
}

/**
 * Check if the current page is a blog-related page
 *
 * @return bool
 */
function ruined_is_blog() {
    // Exclude WooCommerce pages from blog detection
    if (function_exists('is_woocommerce') && is_woocommerce()) {
        return false;
    }
    if (function_exists('is_shop') && is_shop()) {
        return false;
    }
    if (function_exists('is_product_category') && is_product_category()) {
        return false;
    }
    if (function_exists('is_product_tag') && is_product_tag()) {
        return false;
    }
    if (function_exists('is_product') && is_product()) {
        return false;
    }
    
    return (is_home() || is_archive() || is_single()) && 'post' === get_post_type();
}

/**
 * Theme-Specific Utilities
 */

/**
 * Get theme asset URL
 *
 * @param string $path Path to the asset relative to the theme directory
 * @return string
 */
function ruined_asset($path = '') {
    return get_template_directory_uri() . '/assets/' . ltrim($path, '/');
}

/**
 * Get theme image URL with optional size parameter
 *
 * @param string $filename Image filename
 * @param string $size Image size (directory name)
 * @return string
 */
function ruined_image($filename, $size = 'full') {
    return get_template_directory_uri() . '/assets/images/' . $size . '/' . $filename;
}

/**
 * Get SVG icon
 *
 * @param string $icon_name Name of the icon (without .svg extension)
 * @param array $attrs Optional attributes to add to the SVG
 * @return string SVG markup
 */
function ruined_icon($icon_name, $attrs = []) {
    $file_path = get_template_directory() . '/assets/icons/' . $icon_name . '.svg';

    if (!file_exists($file_path)) {
        return '';
    }

    $svg = file_get_contents($file_path);

    if (!empty($attrs)) {
        $dom = new DOMDocument();
        $dom->loadXML($svg);
        $svg_element = $dom->getElementsByTagName('svg')->item(0);

        foreach ($attrs as $key => $value) {
            $svg_element->setAttribute($key, $value);
        }

        return $dom->saveXML($svg_element);
    }

    return $svg;
}

/**
 * Format phone number for tel: links
 *
 * @param string $phone
 * @return string
 */
function ruined_format_phone($phone) {
    return preg_replace('/[^0-9+]/', '', $phone);
}

/**
 * Performance Utilities
 */

/**
 * Get the first image from post content
 *
 * @param int $post_id
 * @return string|false
 */
function ruined_get_first_image($post_id = null) {
    global $post;
    $post_id = $post_id ?: $post->ID;
    $content = get_post_field('post_content', $post_id);

    if (preg_match('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $content, $matches)) {
        return $matches[1];
    }

    return false;
}

/**
 * Get the featured image URL with fallback to first image in content
 *
 * @param string $size
 * @param int $post_id
 * @return string|false
 */
function ruined_get_post_image($size = 'full', $post_id = null) {
    $post_id = $post_id ?: get_the_ID();

    if (has_post_thumbnail($post_id)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
        return $image ? $image[0] : false;
    }

    return ruined_get_first_image($post_id);
}

/**
 * Security Utilities
 */

/**
 * Sanitize output
 *
 * @param string $string
 * @return string
 */
function ruined_esc($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize HTML output
 *
 * @param string $html
 * @return string
 */
function ruined_kses($html) {
    $allowed_html = wp_kses_allowed_html('post');
    return wp_kses($html, $allowed_html);
}

/**
 * Conditional Functions
 */

/**
 * Check if WooCommerce is active
 *
 * @return bool
 */
function ruined_is_woocommerce_active() {
    return class_exists('WooCommerce');
}

/**
 * Check if a plugin is active
 *
 * @param string $plugin
 * @return bool
 */
function ruined_is_plugin_active($plugin) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    return is_plugin_active($plugin);
}

/**
 * Check if the current request is an AJAX request
 *
 * @return bool
 */
function ruined_is_ajax() {
    return defined('DOING_AJAX') && DOING_AJAX;
}

/**
 * Check if the current request is a REST API request
 *
 * @return bool
 */
function ruined_is_rest() {
    return defined('REST_REQUEST') && REST_REQUEST;
}

/**
 * Check if the current request is a WP-CLI request
 *
 * @return bool
 */
function ruined_is_wp_cli() {
    return defined('WP_CLI') && WP_CLI;
}

/**
 * Check if the current user is a specific role
 *
 * @param string $role
 * @return bool
 */
function ruined_current_user_has_role($role) {
    $user = wp_get_current_user();
    return in_array($role, (array) $user->roles, true);
}

/**
 * Template Functions
 */

/**
 * Get template part with variables
 *
 * @param string $slug
 * @param string $name
 * @param array $args
 * @return void
 */
function ruined_get_template_part($slug, $name = null, $args = []) {
    $template = "";
    $file = "";

    // Look in yourtheme/slug-name.php and yourtheme/ruined/slug-name.php
    if ($name) {
        $file = locate_template(["{$slug}-{$name}.php", "ruined/{$slug}-{$name}.php"]);
    }

    // Get default slug-name.php
    if (!$file && $name && file_exists(get_template_directory() . "/ruined/{$slug}-{$name}.php")) {
        $file = get_template_directory() . "/ruined/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/ruined/slug.php
    if (!$file) {
        $file = locate_template(["{$slug}.php", "ruined/{$slug}.php"]);
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $file = apply_filters('ruined_get_template_part', $file, $slug, $name);

    if ($file) {
        // Extract args to use in the template
        if (!empty($args) && is_array($args)) {
            extract($args);
        }

        include $file;
    }
}

/**
 * Get the contents of a template part as a string
 *
 * @param string $slug
 * @param string $name
 * @param array $args
 * @return string
 */
function ruined_get_template_part_as_string($slug, $name = null, $args = []) {
    ob_start();
    ruined_get_template_part($slug, $name, $args);
    return ob_get_clean();
}

/**
 * Get the current template name
 *
 * @return string
 */
function ruined_get_current_template() {
    $template = get_page_template_slug();

    if ($template) {
        return $template;
    }

    if (is_404()) {
        return '404.php';
    }

    if (is_search()) {
        return 'search.php';
    }

    if (is_archive()) {
        if (is_category()) {
            return 'category.php';
        } elseif (is_tag()) {
            return 'tag.php';
        } elseif (is_author()) {
            return 'author.php';
        } elseif (is_date()) {
            return 'date.php';
        } elseif (is_post_type_archive()) {
            return 'archive-' . get_post_type() . '.php';
        } else {
            return 'archive.php';
        }
    }

    if (is_singular()) {
        return 'single.php';
    }

    if (is_home()) {
        return 'home.php';
    }

    if (is_front_page()) {
        return 'front-page.php';
    }

    return 'index.php';
}

/**
 * Check if dark mode is active
 * 
 * @return bool
 */
function ruined_is_dark_mode() {
    // Check for dark mode class on body (set by your theme's dark mode toggle)
    if (is_admin_bar_showing() && is_admin()) {
        return false;
    }
    
    // Check for dark mode cookie
    if (isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === '1') {
        return true;
    }
    
    // Check for system preference as fallback
    if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && 
        isset($_SERVER['HTTP_SEC_FETCH_MODE']) && 
        isset($_SERVER['HTTP_SEC_FETCH_DEST']) && 
        isset($_SERVER['HTTP_SEC_CH_PREFERS_COLOR_SCHEME'])) {
        return $_SERVER['HTTP_SEC_CH_PREFERS_COLOR_SCHEME'] === 'dark';
    }
    
    // Default to light mode
    return false;
}

/**
 * Deprecated Functions
 * Kept for backward compatibility
 */

/**
 * @deprecated 1.0.0 Use ruined_debug() instead
 */
function print_pre($data, $die = false) {
    _deprecated_function(__FUNCTION__, '1.0.0', 'ruined_debug');
    ruined_debug($data, $die);
}

