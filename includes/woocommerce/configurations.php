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


add_action('wp_enqueue_scripts', function() {
    if (function_exists('YITH_WCWL')) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('yith-wcwl-main');

        // force localization (important)
        wp_localize_script('yith-wcwl-main', 'yith_wcwl_l10n', [
                'ajax_url' => admin_url('admin-ajax.php')
        ]);
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
    echo '<div class="container mx-auto !px-0 py-xl">';
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

// Disable WooCommerce order attribution to prevent JS errors
add_action('init', function() {
    if (class_exists('WooCommerce')) {
        add_filter('woocommerce_order_attribution_allow_tracking', '__return_false');

        // Also try to dequeue the script if it's still being loaded
        add_action('wp_enqueue_scripts', function() {
            wp_dequeue_script('wc-order-attribution');
            wp_deregister_script('wc-order-attribution');
        }, 99);
    }
});



// ========================
// AJAX: Update Cart Quantity
// ========================
add_action('wp_ajax_ruined_update_cart_qty', 'ruined_update_cart_qty');
add_action('wp_ajax_nopriv_ruined_update_cart_qty', 'ruined_update_cart_qty');

function ruined_update_cart_qty()
{
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ruined_cart_nonce')) {
        wp_die(__('Security check failed', 'ruined'));
    }

    if (!isset($_POST['cart_item_key'], $_POST['delta'])) {
        wp_send_json_error(__('Missing required parameters', 'ruined'));
        wp_die();
    }

    if (function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    $cart = WC()->cart;

    if (!$cart) {
        wp_send_json_error(__('Cart not found', 'ruined'));
        wp_die();
    }

    $key = sanitize_text_field($_POST['cart_item_key']);
    $delta = intval($_POST['delta']);

    if (!isset($cart->cart_contents[$key])) {
        wp_send_json_error(__('Cart item not found', 'ruined'));
        wp_die();
    }

    $item = $cart->cart_contents[$key];
    $new_qty = max(1, $item['quantity'] + $delta);

    $cart->set_quantity($key, $new_qty, true);

    // Send success response with updated cart data
    wp_send_json_success([
        'message' => __('Cart updated successfully', 'ruined'),
        'new_quantity' => $new_qty,
        'cart_total' => WC()->cart->get_cart_total(),
        'cart_count' => WC()->cart->get_cart_contents_count()
    ]);
}

add_action('template_redirect', function () {

    if (is_account_page() && !is_user_logged_in()) {

        // ✅ Allow lost password page
        if (is_wc_endpoint_url('lost-password')) {
            return;
        }

        wp_redirect(home_url());
        exit;
    }

});


/**
 * Change number of related products output
 */
function ruined_custom_related_products_args($args)
{
    $args['posts_per_page'] = 8; // Ο αριθμός των προϊόντων που θέλεις     // Προαιρετικά, ορίζεις και στήλες
    return $args;
}

add_filter('woocommerce_output_related_products_args', 'ruined_custom_related_products_args', 20);


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

function ruined_render_shop_header()
{
    if (is_shop() || is_product_taxonomy() || is_search() && !is_singular('product')) {
        get_template_part('template-parts/woocommerce/shop-header');
    }
}

add_action('ruined_before_shop_grid', 'ruined_render_shop_header');





// ========================
// SINGLE PRODUCT
// ========================
add_action('after_setup_theme', function () {

    // Disable Woo default gallery scripts
    remove_theme_support('wc-product-gallery-zoom');
    remove_theme_support('wc-product-gallery-lightbox');
    remove_theme_support('wc-product-gallery-slider');

}, 100);


add_action('rv_product_meta_below_gallery', function () {
    get_template_part('includes/woocommerce/product/meta-below-gallery');
});


add_action('rv_custom_summary_layout', function () {
    wc_get_template('includes/woocommerce/product/summary-layout.php');
});

add_action('rv_product_tabs', function () {
    get_template_part('includes/woocommerce/product/product-tabs');
});

add_action('rv_product_video_box', function () {
    get_template_part('includes/woocommerce/product/video-box');
});

add_action('rv_product_product_catalogs', function () {
    get_template_part('includes/woocommerce/product/product-catalogs');
});

add_action('rv_product_contact_banner', function () {
    get_template_part('includes/woocommerce/product/contact_banner');
});


// ❌ Αφαιρεί όλα τα default Woo tabs
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);


// PRODUCT NOTES FIELD
add_action('woocommerce_after_variations_table', function () {
    ?>
    <div class="rv-offer-note-field"
         x-data="{ open: false }">

        <button type="button"
                class="rv-offer-note-toggle"
                @click="open = !open"
                :aria-expanded="open.toString()">

            <span>Σχόλια Προσφοράς</span>
            <div class="rv-accordion-arrow">
                <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.911796 5.62592L5.62484 0.911926L10.3379 5.62592" stroke="black" stroke-width="1.82386"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
        </button>

        <div x-show="open" x-collapse>
            <textarea
                    name="rv_offer_note"
                    rows="4"
                    placeholder="Γράψτε σχόλιο για την προσφορά"></textarea>
        </div>

    </div>
    <?php
});

add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) {

    if (isset($_POST['rv_offer_note']) && $_POST['rv_offer_note'] !== '') {
        $cart_item_data['rv_offer_note'] = sanitize_textarea_field($_POST['rv_offer_note']);
    }

    return $cart_item_data;
}, 10, 2);


add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {

    if (isset($cart_item['rv_offer_note'])) {
        $item_data[] = [
                'key' => 'Σχόλια Προσφοράς',
                'value' => esc_html($cart_item['rv_offer_note'])
        ];
    }

    return $item_data;
}, 10, 2);

add_action('woocommerce_checkout_create_order_line_item',
        function ($item, $cart_item_key, $values) {

            if (isset($values['rv_offer_note'])) {
                $item->add_meta_data(
                        'Σχόλια Προσφοράς',
                        $values['rv_offer_note'],
                        true
                );
            }

        }, 10, 3
);

