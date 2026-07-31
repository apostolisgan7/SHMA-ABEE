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
    return $fragments;
});

// Pass cart mode to JS (yith = quote plugin active, wc = normal cart)
add_action('wp_head', function () {
    if (!class_exists('WooCommerce')) return;
    $mode = class_exists('YITH_Request_Quote') ? 'yith' : 'wc';
    echo '<script>window.ruined_cart_mode = ' . json_encode($mode) . ';</script>' . "\n";
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
add_filter('woocommerce_order_attribution_allow_tracking', '__return_false');

add_action('wp_enqueue_scripts', function () {
    wp_dequeue_script('wc-order-attribution');
    wp_deregister_script('wc-order-attribution');
}, 99);




// ========================
// AJAX: Update Cart Quantity
// ========================
add_action('wp_ajax_ruined_update_cart_qty', 'ruined_update_cart_qty');
add_action('wp_ajax_nopriv_ruined_update_cart_qty', 'ruined_update_cart_qty');

function ruined_update_cart_qty()
{
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ruined_cart_nonce')) {
        wp_die(__('Ο έλεγχος ασφαλείας απέτυχε', 'ruined'));
    }

    if (!isset($_POST['cart_item_key']) || (!isset($_POST['delta']) && !isset($_POST['qty']))) {
        wp_send_json_error(__('Λείπουν απαραίτητες παράμετροι', 'ruined'));
        wp_die();
    }

    if (function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    $cart = WC()->cart;

    if (!$cart) {
        wp_send_json_error(__('Το καλάθι δεν βρέθηκε', 'ruined'));
        wp_die();
    }

    $key = sanitize_text_field($_POST['cart_item_key']);

    if (!isset($cart->cart_contents[$key])) {
        wp_send_json_error(__('Το προϊόν δεν βρέθηκε στο καλάθι', 'ruined'));
        wp_die();
    }

    $item = $cart->cart_contents[$key];
    if (isset($_POST['qty'])) {
        $new_qty = max(1, intval($_POST['qty']));
    } else {
        $new_qty = max(1, $item['quantity'] + intval($_POST['delta']));
    }

    $cart->set_quantity($key, $new_qty, true);

    // Send success response with updated cart data
    wp_send_json_success([
        'message' => __('Το καλάθι ενημερώθηκε επιτυχώς', 'ruined'),
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
// YITH Request a Quote compatibility
// ========================
// Inject quote button inside the WC add-to-cart area (same position as the button it replaces).
// Only runs in "standalone" mode — in "near add-to-cart" mode YITH hooks here itself.
add_action( 'woocommerce_after_add_to_cart_button', function () {
    if ( ! function_exists( 'yith_ywraq_render_button' ) ) return;
    if ( 'yes' === get_option( 'ywraq_show_button_near_add_to_cart', 'no' ) ) return;
    yith_ywraq_render_button();
}, 15 );

// Discontinued products are synced from SoftOne as WooCommerce out-of-stock,
// so hide the quote button for those too — regardless of which of the two
// modes above actually renders it, since both call yith_ywraq_render_button().
add_filter( 'yith_ywraq_before_print_button', function ( $show, $product ) {
    if ( $product instanceof WC_Product && 'outofstock' === $product->get_stock_status() ) {
        return false;
    }
    return $show;
}, 10, 2 );

// The "Hide prices" YITH setting (ywraq_hide_price) already hides price/subtotal
// on the customer-facing quote request emails, but the plugin always shows price
// on the admin notification email regardless of that setting. Force it to comply too.
add_filter( 'ywraq_hide_prices_email_admin', '__return_true' );

// ========================
// RAQ OFFCANVAS MINI LIST
// ========================

// Force YITH to start its session for our AJAX action.
// This must run before wp_loaded (where YITH calls start_session).
if ( defined( 'DOING_AJAX' ) && DOING_AJAX
    && isset( $_REQUEST['action'] )
    && in_array( $_REQUEST['action'], [ 'rv_raq_mini_list', 'rv_ywraq_check_exists' ], true ) ) {
    add_filter( 'ywraq_force_start_session', '__return_true' );
}

// ========================
// RAQ "already in quote" live check
// ========================
// Product pages are served from full-page cache (SG Optimizer), so the
// "already in quote" state baked into the HTML at cache time is only correct
// for whoever's session generated that cache entry — every other visitor
// sees a stale state. This lets JS re-check the real per-session state after
// the page loads and correct the DOM if needed.
add_action( 'wp_ajax_rv_ywraq_check_exists', 'rv_ywraq_check_exists' );
add_action( 'wp_ajax_nopriv_rv_ywraq_check_exists', 'rv_ywraq_check_exists' );
function rv_ywraq_check_exists() {
    if ( ! function_exists( 'YITH_Request_Quote' ) ) {
        wp_send_json_error();
    }

    // No nonce check: the nonce on the page is itself baked into the cached
    // HTML (tied to whoever's session generated that cache entry), so it
    // won't match any other visitor's session. This endpoint is read-only —
    // it doesn't change any state — so that's fine to skip.
    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

    if ( ! $product_id ) {
        wp_send_json_error();
    }

    wp_send_json_success( [
        'exists'     => (bool) YITH_Request_Quote()->exists( $product_id ),
        // Variable products don't key their "already in quote" state off
        // $product_id (no variation is selected yet at render time) — YITH's
        // own JS instead matches the *selected* variation id against this
        // comma-separated list of every variation id currently in the quote.
        'variations' => implode( ',', YITH_Request_Quote()->get_variations_list() ),
        'message'    => ywraq_get_label( 'already_in_quote' ),
    ] );
}

function rv_get_raq_count() {
    if ( ! class_exists( 'YITH_Request_Quote' ) ) return 0;
    return (int) YITH_Request_Quote()->get_raq_product_number();
}

function rv_raq_mini_list_html() {
    if ( ! class_exists( 'YITH_Request_Quote' ) ) return '';

    $raq   = YITH_Request_Quote();
    $items = $raq->get_raq_for_session();

    ob_start();

    if ( empty( $items ) ) : ?>
        <div class="mini-cart__empty">
            <?php
            $svg = get_template_directory() . '/src/img/icons/empty-cart.svg';
            if ( file_exists( $svg ) ) echo file_get_contents( $svg );
            ?>
            <p>Η λίστα προσφοράς σας είναι κενή.</p>
        </div>
    <?php else : ?>
        <ul class="mini-cart" data-lenis-prevent>
            <?php foreach ( $items as $key => $item ) :
                $product_id   = (int) $item['product_id'];
                $variation_id = ! empty( $item['variation_id'] ) ? (int) $item['variation_id'] : 0;
                $qty          = (int) $item['quantity'];

                $product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
                $parent  = wc_get_product( $product_id );
                if ( ! $product || ! $parent ) continue;

                $image = $parent->get_image( 'thumbnail' );
                $title = $parent->get_name();
                $sku   = $product->get_sku() ?: $parent->get_sku();
                $link  = $parent->get_permalink();
                $nonce = wp_create_nonce( 'yith_ywraq_action' );

                $variation_label = '';
                if ( $variation_id && ! empty( $item['variations'] ) ) {
                    $parts = [];
                    foreach ( $item['variations'] as $attr_name => $attr_value ) {
                        if ( $attr_value === '' ) continue;
                        $label  = wc_attribute_label( str_replace( 'attribute_', '', $attr_name ) );
                        $parts[] = $label . ': ' . $attr_value;
                    }
                    $variation_label = implode( ' / ', $parts );
                }
            ?>
                <li class="mini-cart__item">
                    <div class="mini-cart__image">
                        <a href="<?php echo esc_url( $link ); ?>"><?php echo $image; ?></a>
                    </div>
                    <div class="mini-cart__content">
                        <?php if ( $sku ) : ?>
                            <span class="mini-cart__sku">SKU: <?php echo esc_html( $sku ); ?></span>
                        <?php endif; ?>
<!--                        --><?php //if ( $variation_label ) : ?>
<!--                            <span class="mini-cart__variation">--><?php //echo esc_html( $variation_label ); ?><!--</span>-->
<!--                        --><?php //endif; ?>
                        <p class="mini-cart__title">
                            <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
                        </p>
                        <div class="mini-cart__meta">

                            <div class="mini-cart__qty">
                                <button class="qty-minus raq-qty-btn" data-key="<?php echo esc_attr( $key ); ?>" aria-label="Μείωση">−</button>
                                <span class="qty-value"><?php echo esc_html( $qty ); ?></span>
                                <button class="qty-plus raq-qty-btn" data-key="<?php echo esc_attr( $key ); ?>" aria-label="Αύξηση">+</button>
                            </div>
                        </div>
                    </div>
                    <button class="mini-cart__remove yith-ywraq-item-remove"
                            data-remove-item="<?php echo esc_attr( $key ); ?>"
                            data-product_id="<?php echo esc_attr( $product_id ); ?>"
                            data-wp_nonce="<?php echo esc_attr( $nonce ); ?>"
                            aria-label="Αφαίρεση">✕</button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="mini-cart__footer">
            <a href="<?php echo esc_url( $raq->get_raq_page_url() ); ?>" class="button button--checkout">
                Προβολή Λίστας Προσφοράς
            </a>
        </div>
    <?php endif;

    return ob_get_clean();
}

add_action( 'wp_ajax_rv_raq_mini_list', 'rv_ajax_raq_mini_list' );
add_action( 'wp_ajax_nopriv_rv_raq_mini_list', 'rv_ajax_raq_mini_list' );
function rv_ajax_raq_mini_list() {
    if ( class_exists( 'YITH_Request_Quote' ) ) {
        YITH_Request_Quote()->get_raq_for_session();
    }

    wp_send_json_success( [
        'html'  => rv_raq_mini_list_html(),
        'count' => rv_get_raq_count(),
    ] );
}


// ========================
// WC OFFCANVAS MINI LIST (fallback when YITH is inactive)
// ========================
function rv_wc_mini_list_html() {
    if ( ! class_exists( 'WooCommerce' ) ) return '';
    ob_start();
    woocommerce_mini_cart();
    return ob_get_clean();
}

add_action( 'wp_ajax_rv_wc_mini_list', 'rv_ajax_wc_mini_list' );
add_action( 'wp_ajax_nopriv_rv_wc_mini_list', 'rv_ajax_wc_mini_list' );
function rv_ajax_wc_mini_list() {
    if ( function_exists( 'wc_load_cart' ) ) {
        wc_load_cart();
    }
    wp_send_json_success( [
        'html'  => rv_wc_mini_list_html(),
        'count' => WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
    ] );
}

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

// Variations can carry their own SoftOne-synced display name (_erp_name),
// different from the parent product title. WooCommerce doesn't send custom
// variation meta to the front-end by default, so expose it here — the
// single-product title JS (summary.js) swaps to it on found_variation.
add_filter('woocommerce_available_variation', function ($data, $product, $variation) {
    $erp_name = get_post_meta($variation->get_id(), '_erp_name', true);
    if ($erp_name !== '') {
        $data['erp_name'] = $erp_name;
    }
    return $data;
}, 10, 3);


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

add_action('rv_after_products_section', function () {
    if ( ! is_product_category() ) return;
    get_template_part('includes/woocommerce/archive/category-catalogs');
});


// ❌ Αφαιρεί όλα τα default Woo tabs
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);


// PRODUCT NOTES FIELD — variable: after variations table, simple: before add-to-cart button
function rv_render_offer_note_field() { ?>
    <div class="rv-offer-note-field" x-data="{ open: false }">
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
            <textarea name="rv_offer_note" rows="4" placeholder="Γράψτε σχόλιο για την προσφορά"></textarea>
        </div>
    </div>
<?php }

add_action('woocommerce_after_variations_table', 'rv_render_offer_note_field');

// Simple: offer note πριν το quantity, μετά ανοίγει wrapper για qty+button
add_action('woocommerce_before_add_to_cart_quantity', function () {
    global $product;
    if ($product && $product->is_type('variable')) return;
    rv_render_offer_note_field();
    echo '<div class="rv-cart-row">';
}, 5);

// Close rv-cart-row AFTER YITH's quote button (YITH uses priority 10)
add_action('woocommerce_after_add_to_cart_button', function () {
    global $product;
    if ($product && $product->is_type('variable')) return;
    echo '</div>';
}, 20);

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

// Ελληνικά labels για το WooCommerce default stock (variations, add-to-cart)
add_filter('woocommerce_get_availability', function ($availability, $product) {
    switch ($product->get_stock_status()) {
        case 'instock':
            $availability['availability'] = __('Διαθέσιμο', 'ruined');
            break;
        case 'outofstock':
            $availability['availability'] = __('Μη διαθέσιμο', 'ruined');
            break;
        case 'onbackorder':
            $availability['availability'] = __('Κατόπιν παραγγελίας', 'ruined');
            break;
    }
    return $availability;
}, 10, 2);

// Το site δεν εμφανίζει τιμές ή βαθμολογίες προϊόντων, άρα αφαιρούμε τις αντίστοιχες επιλογές ταξινόμησης
add_filter('woocommerce_catalog_orderby', function ($options) {
    unset($options['price'], $options['price-desc'], $options['rating']);
    return $options;
});

add_filter('woocommerce_default_catalog_orderby_options', function ($options) {
    unset($options['price'], $options['price-desc'], $options['rating']);
    return $options;
});

// ========================
// Custom σειρά κατηγοριών στο Shop (πεδίο "Σειρά Εμφάνισης στο Shop" ανά product_cat)
// ========================
add_action('product_cat_add_form_fields', function () {
    ?>
    <div class="form-field">
        <label for="rv_cat_order"><?php esc_html_e('Σειρά Εμφάνισης στο Shop', 'ruined'); ?></label>
        <input type="number" name="rv_cat_order" id="rv_cat_order" value="0" step="1">
        <p class="description"><?php esc_html_e('Μικρότερος αριθμός εμφανίζεται πρώτα στη σελίδα Shop (προεπιλεγμένη ταξινόμηση).', 'ruined'); ?></p>
    </div>
    <?php
});

add_action('product_cat_edit_form_fields', function ($term) {
    $order = get_term_meta($term->term_id, 'rv_cat_order', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="rv_cat_order"><?php esc_html_e('Σειρά Εμφάνισης στο Shop', 'ruined'); ?></label></th>
        <td>
            <input type="number" name="rv_cat_order" id="rv_cat_order" value="<?php echo esc_attr($order !== '' ? $order : 0); ?>" step="1">
            <p class="description"><?php esc_html_e('Μικρότερος αριθμός εμφανίζεται πρώτα στη σελίδα Shop (προεπιλεγμένη ταξινόμηση).', 'ruined'); ?></p>
        </td>
    </tr>
    <?php
});

foreach (['created_product_cat', 'edited_product_cat'] as $rv_cat_order_hook) {
    add_action($rv_cat_order_hook, function ($term_id) {
        if (isset($_POST['rv_cat_order'])) {
            update_term_meta($term_id, 'rv_cat_order', (int) $_POST['rv_cat_order']);
        }
    });
}

// Στη Shop σελίδα (όλα τα προϊόντα), με την προεπιλεγμένη ταξινόμηση, ομαδοποίηση κατά κατηγορία βάσει της παραπάνω σειράς
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !is_shop()) return;

    $orderby = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : '';
    if ($orderby && $orderby !== 'menu_order') return;

    $query->set('rv_order_by_category', true);
}, 20);

add_filter('posts_clauses', function ($clauses, $query) {
    if (is_admin() || !$query->get('rv_order_by_category')) return $clauses;

    global $wpdb;

    $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} rv_tr ON ({$wpdb->posts}.ID = rv_tr.object_id)
        LEFT JOIN {$wpdb->term_taxonomy} rv_tt ON (rv_tr.term_taxonomy_id = rv_tt.term_taxonomy_id AND rv_tt.taxonomy = 'product_cat')
        LEFT JOIN {$wpdb->termmeta} rv_tm ON (rv_tm.term_id = rv_tt.term_id AND rv_tm.meta_key = 'rv_cat_order')";

    $clauses['groupby'] = "{$wpdb->posts}.ID";
    $clauses['orderby'] = "MIN(COALESCE(rv_tm.meta_value + 0, 9999)) ASC, {$wpdb->posts}.menu_order ASC, {$wpdb->posts}.post_title ASC";

    return $clauses;
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

// ========================
// My Account — copy billing address to shipping (only if shipping is empty)
// ========================
add_action( 'woocommerce_customer_save_address', 'ruined_copy_billing_to_empty_shipping_address', 20, 3 );
function ruined_copy_billing_to_empty_shipping_address( $user_id, $address_type, $address = array() ) {
    // Run only when the customer saves the billing address.
    if ( 'billing' !== $address_type || ! $user_id ) {
        return;
    }

    $customer = new WC_Customer( $user_id );

    // Do not overwrite an existing shipping address.
    $shipping_already_exists =
        ! empty( $customer->get_shipping_address_1() ) ||
        ! empty( $customer->get_shipping_city() ) ||
        ! empty( $customer->get_shipping_postcode() );

    if ( $shipping_already_exists ) {
        return;
    }

    $fields = array(
        'first_name',
        'last_name',
        'company',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'country',
        'phone',
    );

    foreach ( $fields as $field ) {
        $billing_getter  = 'get_billing_' . $field;
        $shipping_setter = 'set_shipping_' . $field;

        if ( is_callable( array( $customer, $billing_getter ) ) && is_callable( array( $customer, $shipping_setter ) ) ) {
            $customer->{$shipping_setter}( $customer->{$billing_getter}() );
        }
    }

    $customer->save();
}

// ========================
// YITH Ajax Product Filter — scope sidebar category blocks to current category
// ========================
// This preset has one product_cat filter-tax block per top-level category.
// YITH's own relevance check can't tell them apart: when computing term
// counts *within* a taxonomy it deliberately ignores that taxonomy's own
// active filter (so sibling terms in the same block don't vanish once you
// pick one) — but that means every top-level block reports products
// everywhere, since it never knows which block corresponds to the category
// you're actually browsing. Restrict relevance ourselves using the real
// category hierarchy.
add_filter('yith_wcan_is_filter_relevant', function ($is_relevant, $filter) {
    if (
        ! $is_relevant
        || 'tax' !== $filter->get_type()
        || 'product_cat' !== $filter->get_taxonomy()
        || $filter->use_all_terms()
        || ! is_product_category()
    ) {
        return $is_relevant;
    }

    $current_term = get_queried_object();
    if (! $current_term instanceof WP_Term) {
        return $is_relevant;
    }

    $current_cat_ids = array_merge(
        [$current_term->term_id],
        get_ancestors($current_term->term_id, 'product_cat')
    );

    return (bool) array_intersect($current_cat_ids, array_keys($filter->get_terms_options()));
}, 10, 2);

