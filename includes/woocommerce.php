<?php
/**
 * WooCommerce integration for Ruined theme
 */

// ========================
// Theme Support & Setup
// ========================
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

// ========================
// Scripts & Styles
// ========================
add_action('wp_enqueue_scripts', function () {
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Always load cart fragments
    wp_enqueue_script('wc-cart-fragments');

    // Load single product scripts only when needed
    if (is_product()) {
        wp_enqueue_script('wc-add-to-cart');
    }
}, 99);

// ========================
// Layout & Wrappers
// ========================
// Remove default WooCommerce wrappers
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// Add theme wrappers
add_action('woocommerce_before_main_content', function () {
    echo '<div class="container mx-auto px-0 py-xl">';
}, 10);

add_action('woocommerce_after_main_content', function () {
    echo '</div>';
}, 10);

// ========================
// Cart & Fragments
// ========================
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    // Update cart count in header
    ob_start();
    $cart_count = WC()->cart->get_cart_contents_count();
    if ($cart_count > 0) {
        echo '<span class="count">' . esc_html($cart_count) . '</span>';
    }
    $fragments['.header-cart .count'] = ob_get_clean();

    // Update mini cart content
    ob_start();
    echo '<div class="offcanvas-cart__body">';
    woocommerce_mini_cart();
    echo '</div>';
    $fragments['.offcanvas-cart__body'] = ob_get_clean();

    return $fragments;
});

// Initialize cart fragments in footer
add_action('wp_footer', function () {
    if (is_cart() || is_checkout() || is_account_page()) return;
    ?>
    <script>
        jQuery(function ($) {
            $(document.body).on('wc_fragments_refreshed', function () {
                if (typeof initOffcanvasCart === 'function') {
                    initOffcanvasCart();
                }
            });
        });
    </script>
    <?php
});

// Hide default WooCommerce notices
add_filter('wc_add_to_cart_message_html', '__return_empty_string');

/**
 * Add grid/list view toggle to the shop page.
 */


/**
 * Add body class for the current shop view.
 */
function ruined_shop_view_body_class($classes)
{
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
add_action('init', function () {

    // Remove breadcrumbs
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // Remove Shop title
    remove_action('woocommerce_before_shop_loop', 'woocommerce_page_title', 20);

    // Remove archive descriptions (category text)
    remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
    remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);

    // Remove empty header wrapper
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper_end', 10);
});

//add_filter( 'woocommerce_show_page_title', '__return_false' );


// ========================
// AJAX: Update Cart Quantity
// ========================
add_action('wp_ajax_ruined_update_cart_qty', 'ruined_update_cart_qty');
add_action('wp_ajax_nopriv_ruined_update_cart_qty', 'ruined_update_cart_qty');

function ruined_update_cart_qty()
{

    if (!isset($_POST['cart_item_key'], $_POST['delta'])) {
        wp_die();
    }

    if (function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    $cart = WC()->cart;

    if (!$cart) {
        wp_die();
    }

    $key = sanitize_text_field($_POST['cart_item_key']);
    $delta = intval($_POST['delta']);

    if (!isset($cart->cart_contents[$key])) {
        wp_die();
    }

    $item = $cart->cart_contents[$key];
    $new_qty = max(1, $item['quantity'] + $delta);

    $cart->set_quantity($key, $new_qty, true);

    wp_die();
}


// ========================
// Remove default archive elements
// ========================
add_action('init', function () {

    // Remove default title
    add_filter('woocommerce_show_page_title', '__return_false');

    // Remove default result count & ordering
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
});

function ruined_render_shop_header() {
    get_template_part('template-parts/woocommerce/shop-header');
}

add_action('ruined_before_shop_grid', 'ruined_render_shop_header');


