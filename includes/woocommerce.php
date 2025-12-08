<?php

// --- WooCommerce Support ---

// Enable AJAX add to cart on single product pages
add_action('wp_enqueue_scripts', function() {
    // Only proceed if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Ensure cart fragments script is enqueued
    wp_enqueue_script('wc-cart-fragments');
    
    // Check if we're on a single product page
    if (function_exists('is_product') && is_product()) {
        wp_enqueue_script('wc-add-to-cart');
    }
    
    // Add AJAX URL and nonce for our JavaScript
    wp_localize_script('wc-cart-fragments', 'wc_cart_fragments_params', array(
        'ajax_url' => WC()->ajax_url(),
        'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
        'cart_hash' => WC()->cart->get_cart_hash(),
    ));
});

// Add custom wrapper for WooCommerce pages
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', function() {
    echo '<div class="container mx-auto px-0 py-xl">';
}, 10);

add_action('woocommerce_after_main_content', function() {
    echo '</div>';
}, 10);

// Ensure cart fragments are refreshed after AJAX add to cart
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        return $fragments;
    }

    try {
        // Update cart count
        ob_start();
        $cart_count = WC()->cart->get_cart_contents_count();
        ?>
        <span class="cart-contents-count" data-cart-count="<?php echo esc_attr($cart_count); ?>">
            <?php echo $cart_count; ?>
        </span>
        <?php
        $fragments['.cart-contents-count'] = ob_get_clean();
        
        // Update mini cart
        ob_start();
        woocommerce_mini_cart();
        $fragments['.offcanvas-cart__body'] = '<div class="offcanvas-cart__body">' . ob_get_clean() . '</div>';
        
        // Add cart hash to force cache updates
        $fragments['cart_hash'] = WC()->cart->get_cart_hash();
        
        // Add any notices to fragments
        $notice_types = ['success', 'error', 'notice'];
        
        foreach ($notice_types as $type) {
            $messages = wc_get_notices($type);
            
            if (!empty($messages)) {
                ob_start();
                foreach ($messages as $message) {
                    // Clean up the message HTML
                    $message = str_replace('button wc-forward', 'button wc-forward ' . $type, $message);
                    $fragments['.woocommerce-' . $type] = '<div class="woocommerce-message">' . $message . '</div>';
                }
                wc_clear_notices();
                break; // Only show one type of message at a time
            }
        }
        
    } catch (Exception $e) {
        error_log('WooCommerce fragments error: ' . $e->getMessage());
    }
    
    return $fragments;
});

// Hide default WooCommerce notices since we have our own
add_filter('wc_add_to_cart_message_html', '__return_empty_string');

/**
 * Add grid/list view toggle to the shop page.
 */
function ruined_add_shop_view_toggle() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        $current_view = isset($_COOKIE['shop_view']) ? $_COOKIE['shop_view'] : 'grid';
        echo '<div class="shop-view-toggle">';
        echo '<button id="grid-view" class="' . ($current_view === 'grid' ? 'active' : '') . '" aria-label="' . __('Grid View', 'ruined') . '">' . ruined_icon('grid') . '</button>';
        echo '<button id="list-view" class="' . ($current_view === 'list' ? 'active' : '') . '" aria-label="' . __('List View', 'ruined') . '">' . ruined_icon('list') . '</button>';
        echo '</div>';
    }
}
add_action('woocommerce_before_shop_loop', 'ruined_add_shop_view_toggle', 25);

/**
 * Add body class for the current shop view.
 */
function ruined_shop_view_body_class($classes) {
    // Only proceed if WooCommerce is active and functions exist
    if (class_exists('WooCommerce') && function_exists('is_shop') && function_exists('is_product_category') && function_exists('is_product_tag')) {
        if (is_shop() || is_product_category() || is_product_tag()) {
            $current_view = isset($_COOKIE['shop_view']) ? $_COOKIE['shop_view'] : 'grid';
            $classes[] = 'shop-view-' . esc_attr($current_view);
        }
    }
    return $classes;
}
add_filter('body_class', 'ruined_shop_view_body_class');


/**
 * Remove WooCommerce default breadcrumbs + header
 */
add_action( 'init', function() {

    // Remove breadcrumbs
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

    // Remove Shop title
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_page_title', 20 );

    // Remove archive descriptions (category text)
    remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
    remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );

    // Remove empty header wrapper
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper_end', 10 );
});

//add_filter( 'woocommerce_show_page_title', '__return_false' );

