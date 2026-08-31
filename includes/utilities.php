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
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host   = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^a-zA-Z0-9.\-:]/', '', wp_unslash($_SERVER['HTTP_HOST'])) : '';

    if ($full) {
        $path = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        return esc_url_raw($scheme . '://' . $host . $path);
    }
    $path = isset($_SERVER['REDIRECT_URL']) ? wp_unslash($_SERVER['REDIRECT_URL']) : '';
    return esc_url_raw($scheme . '://' . $host . $path);
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
 * Get inline social icon SVG markup based on the domain of a URL.
 * Supports Facebook, YouTube, LinkedIn and Instagram.
 *
 * @param string $url
 * @return string SVG markup, or empty string if the domain isn't recognized.
 */
function shma_get_social_icon($url) {
    $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));

    if (!$host) {
        return '';
    }

    $icons = [
        'facebook.com'  => '<svg width="9" height="18" viewBox="0 0 9 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7.37151 2.98802H9V0.126027C8.21153 0.0408392 7.41929 -0.00121885 6.62656 2.68777e-05C4.27045 2.68777e-05 2.65929 1.49402 2.65929 4.23002V6.58802H0V9.79201H2.65929V18H5.84697V9.79201H8.49759L8.89605 6.58802H5.84697V4.54502C5.84697 3.60002 6.08951 2.98802 7.37151 2.98802Z" fill="black"/>
</svg>
',
        'youtube.com'   => '<svg width="23" height="16" viewBox="0 0 23 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M22.0088 5.45168C22.0583 4.02034 21.7452 2.5997 21.0988 1.32168C20.6602 0.797282 20.0515 0.443393 19.3788 0.321683C16.5963 0.0692085 13.8023 -0.0342726 11.0088 0.0116833C8.22546 -0.0363584 5.44152 0.0637835 2.66882 0.311683C2.12064 0.4114 1.61333 0.668525 1.20882 1.05168C0.308816 1.88168 0.208816 3.30168 0.108816 4.50168C-0.036272 6.65925 -0.036272 8.82412 0.108816 10.9817C0.137746 11.6571 0.23831 12.3275 0.408816 12.9817C0.529391 13.4867 0.773339 13.954 1.11882 14.3417C1.52608 14.7451 2.0452 15.0169 2.60882 15.1217C4.76473 15.3878 6.93703 15.4981 9.10882 15.4517C12.6088 15.5017 15.6788 15.4517 19.3088 15.1717C19.8863 15.0733 20.42 14.8012 20.8388 14.3917C21.1188 14.1116 21.3279 13.7688 21.4488 13.3917C21.8064 12.2943 21.9821 11.1458 21.9688 9.99168C22.0088 9.43168 22.0088 6.05168 22.0088 5.45168ZM8.74882 10.5917V4.40168L14.6688 7.51168C13.0088 8.43168 10.8188 9.47168 8.74882 10.5917Z" fill="black"/>
</svg>
',
        'linkedin.com'  => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M2.40342 4.46565H2.37442C2.06736 4.48484 1.75965 4.44043 1.47055 4.3352C1.18145 4.22997 0.917192 4.06619 0.694317 3.85411C0.471442 3.64203 0.294751 3.38622 0.17531 3.1027C0.0558687 2.81918 -0.00375146 2.51405 0.000182767 2.20642C0.004117 1.89879 0.0715209 1.59529 0.198174 1.31491C0.324827 1.03454 0.508001 0.783328 0.736227 0.577018C0.964453 0.370708 1.23282 0.213737 1.52451 0.115937C1.81621 0.0181357 2.12495 -0.0183896 2.43142 0.00864766C2.73848 -0.0128027 3.04666 0.0294043 3.33664 0.132623C3.62663 0.235843 3.89217 0.397849 4.11662 0.608487C4.34107 0.819126 4.51959 1.07386 4.64099 1.35671C4.76239 1.63957 4.82406 1.94445 4.82212 2.25225C4.82019 2.56006 4.7547 2.86414 4.62975 3.14545C4.50481 3.42676 4.3231 3.67923 4.09602 3.88703C3.86894 4.09483 3.60139 4.25349 3.31013 4.35306C3.01887 4.45262 2.71018 4.49095 2.40342 4.46565ZM0.417416 7.46565H4.41742V19.4656H0.417416V7.46565ZM14.9174 7.46565C14.2434 7.46747 13.5785 7.62159 12.9725 7.91649C12.3664 8.21138 11.8348 8.63943 11.4174 9.16865V7.46565H7.41742V19.4656H11.4174V13.9656C11.4174 13.4352 11.6281 12.9265 12.0032 12.5514C12.3783 12.1764 12.887 11.9656 13.4174 11.9656C13.9478 11.9656 14.4566 12.1764 14.8316 12.5514C15.2067 12.9265 15.4174 13.4352 15.4174 13.9656V19.4656H19.4174V11.9656C19.4174 10.7722 18.9433 9.62758 18.0994 8.78367C17.2555 7.93975 16.1109 7.46565 14.9174 7.46565Z" fill="black"/>
</svg>
',
        'instagram.com' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M10.1 0.0501938C12.8197 0.0501938 13.1614 0.0496696 14.2231 0.109764H14.2221C15.0582 0.1272 15.8851 0.287715 16.6674 0.583397L16.9174 0.683006C17.4947 0.931481 18.0185 1.29219 18.4565 1.74551H18.4555C18.9702 2.24967 19.3697 2.85938 19.6264 3.53262H19.6273C19.9142 4.31657 20.0705 5.14242 20.0902 5.97695L20.1244 6.68691C20.1498 7.35829 20.1498 8.06033 20.1498 10.1C20.1498 11.4602 20.1345 12.2256 20.1195 12.7836C20.1045 13.3418 20.0902 13.691 20.0902 14.2201V14.2211C20.0707 15.0563 19.9144 15.8828 19.6273 16.6674H19.6264C19.3724 17.3418 18.9723 17.9512 18.4555 18.4535L18.4565 18.4545C18.0185 18.9078 17.4947 19.2685 16.9174 19.517L16.6674 19.6166C15.8851 19.9123 15.0582 20.0718 14.2221 20.0893C13.161 20.1493 12.8191 20.1498 10.1 20.1498C7.38031 20.1498 7.03863 20.1503 5.97696 20.0902V20.0893C5.24555 20.0739 4.52115 19.95 3.82755 19.7211L3.53262 19.6166C2.8578 19.3675 2.24743 18.9707 1.74551 18.4555V18.4564C1.2922 18.0185 0.931489 17.4947 0.683014 16.9174L0.583405 16.6674C0.287838 15.8854 0.127312 15.0588 0.109772 14.223L0.075592 13.5131C0.0502478 12.8417 0.0502014 12.1397 0.0502014 10.1C0.0502014 7.3803 0.0496772 7.03862 0.109772 5.97695C0.127312 5.14117 0.287838 4.3146 0.583405 3.53262L0.683014 3.28262C0.896016 2.78772 1.19139 2.33263 1.55704 1.93691L1.74551 1.74355C2.24734 1.22853 2.85802 0.832392 3.53262 0.583397L3.82755 0.478905C4.52116 0.249973 5.24555 0.125114 5.97696 0.109764C7.03863 0.0496696 7.38031 0.0501938 10.1 0.0501938ZM10.1 1.94961C8.78544 1.94961 8.03542 1.96489 7.48965 1.97988C6.9444 1.99486 6.60077 2.01015 6.10001 2.01015C5.45869 2.01523 4.8225 2.12772 4.21915 2.34512C3.8053 2.52529 3.43599 2.79496 3.13712 3.1332L3.13321 3.13808L3.13223 3.13711C2.79499 3.43312 2.52897 3.80224 2.35587 4.21621L2.35684 4.21719C2.13188 4.81954 2.01464 5.45703 2.01016 6.1V6.10293C1.95027 7.10114 1.94962 7.46962 1.94962 10.1C1.94962 11.4146 1.9649 12.1646 1.97989 12.7104C1.99485 13.2549 2.01012 13.5983 2.01016 14.098L2.02286 14.3402C2.05935 14.823 2.15944 15.2988 2.32071 15.7553L2.40665 15.9809L2.40762 15.9828H2.40665C2.53989 16.3573 2.74757 16.701 3.01602 16.9926L3.13516 17.1137L3.13614 17.1156C3.44239 17.4331 3.81086 17.6837 4.21817 17.8529C4.8367 18.0615 5.48643 18.162 6.13907 18.1498H6.14297C7.14119 18.2097 7.50967 18.2104 10.14 18.2104C12.7695 18.2103 13.1387 18.1997 14.1371 18.1498H14.1391C14.7682 18.1426 15.3918 18.0287 15.9828 17.8129L16.14 17.7484C16.5041 17.5879 16.8353 17.3593 17.1147 17.0746L17.1156 17.0736C17.4439 16.7574 17.6959 16.3707 17.8529 15.9428C18.0687 15.3518 18.1826 14.7281 18.1898 14.099V14.0971C18.2398 13.0987 18.2504 12.7295 18.2504 10.1C18.2504 7.4705 18.2398 7.10129 18.1898 6.10293V6.1C18.1871 5.45763 18.0732 4.82062 17.8529 4.21719V4.21621C17.6894 3.80664 17.4374 3.43775 17.1156 3.13613L17.1127 3.13418C16.8306 2.8267 16.4886 2.58104 16.1078 2.41348L15.9428 2.34707C15.3518 2.13126 14.7281 2.01737 14.099 2.01015H14.0971C13.0989 1.95026 12.7304 1.94961 10.1 1.94961ZM10.1 4.92031C10.7814 4.91767 11.4571 5.04975 12.0873 5.30898C12.7174 5.56824 13.2897 5.94977 13.7719 6.43105C14.2542 6.91246 14.6374 7.48395 14.8979 8.11367C15.1583 8.74338 15.2918 9.41855 15.2904 10.1C15.2904 11.1241 14.9861 12.1253 14.4174 12.977C13.8487 13.8285 13.04 14.4928 12.0941 14.8852C11.1484 15.2773 10.1073 15.3801 9.10294 15.1811C8.09848 14.982 7.17537 14.4894 6.45059 13.766C5.7259 13.0426 5.23166 12.1206 5.03067 11.1166C4.8297 10.1125 4.93119 9.07106 5.32169 8.12441C5.71221 7.17787 6.3746 6.36857 7.22501 5.79824C8.07552 5.22784 9.07593 4.92231 10.1 4.92031ZM11.3549 7.06973C10.7557 6.82158 10.0965 6.75671 9.46036 6.8832C8.8241 7.00976 8.23939 7.32195 7.78067 7.78066C7.32195 8.23938 7.00977 8.82409 6.88321 9.46035C6.75672 10.0965 6.82159 10.7557 7.06973 11.3549C7.31799 11.9542 7.73835 12.4671 8.27774 12.8275C8.8171 13.1879 9.45134 13.3803 10.1 13.3803C10.5306 13.3803 10.957 13.295 11.3549 13.1303C11.7528 12.9654 12.1148 12.7239 12.4193 12.4193C12.7239 12.1148 12.9654 11.7528 13.1303 11.3549C13.295 10.957 13.3803 10.5306 13.3803 10.1C13.3803 9.45134 13.1879 8.81709 12.8275 8.27773C12.4671 7.73834 11.9542 7.31798 11.3549 7.06973ZM15.4398 3.51015C15.7714 3.51015 16.0892 3.64195 16.3236 3.87637C16.5581 4.11079 16.6898 4.42863 16.6898 4.76015C16.6898 5.00733 16.6162 5.24897 16.4789 5.45449C16.3416 5.6599 16.1466 5.81987 15.9184 5.91445C15.69 6.00906 15.4382 6.03397 15.1957 5.98574C14.9534 5.93745 14.7308 5.81865 14.5561 5.64394C14.3814 5.46924 14.2626 5.24659 14.2143 5.0043C14.166 4.76182 14.1909 4.51005 14.2856 4.28164C14.3801 4.05337 14.5401 3.85841 14.7455 3.72109C14.951 3.58377 15.1927 3.51019 15.4398 3.51015Z" fill="black" stroke="black" stroke-width="0.1"/>
</svg>
',
    ];

    foreach ($icons as $domain => $svg) {
        if (strpos($host, $domain) !== false) {
            return $svg;
        }
    }

    return '';
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
            extract($args, EXTR_SKIP);
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
    if (isset($_COOKIE['dark_mode']) && sanitize_text_field(wp_unslash($_COOKIE['dark_mode'])) === '1') {
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

