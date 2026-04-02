<?php
/**
 * Template Part: Product Item Card
 * Ανανεωμένο με Best Practices (wp_get_attachment_image & srcset)
 */
if (!defined('ABSPATH')) exit;

$post_obj = get_query_var('product_post');
if (!$post_obj instanceof WP_Post) return;

$product = wc_get_product($post_obj->ID);
$permalink = get_permalink($post_obj);
$title = get_the_title($post_obj->ID);

/** * 1. ΚΥΡΙΑ ΕΙΚΟΝΑ
 * Χρήση wp_get_attachment_image για αυτόματο srcset & lazy loading
 */
$img_id = get_post_thumbnail_id($post_obj->ID);
if ($img_id) {
    $main_image_html = wp_get_attachment_image($img_id, 'medium_large', false, [
            'class' => 'rv-product-card__img rv-product-card__img--main',
            'alt' => $title
    ]);
} else {
    // Placeholder αν δεν υπάρχει εικόνα
    $placeholder_url = function_exists('wc_placeholder_img_src')
            ? wc_placeholder_img_src('medium_large')
            : (wc()->plugin_url() . '/assets/images/placeholder.png');
    $main_image_html = sprintf('<img src="%s" class="rv-product-card__img rv-product-card__img--main" alt="%s">', esc_url($placeholder_url), esc_attr($title));
}

/** * 2. GALLERY ΕΙΚΟΝΑ (HOVER)
 */
$hover_image_html = '';
$gallery_ids = $product ? $product->get_gallery_image_ids() : [];
if (!empty($gallery_ids)) {
    $hover_image_html = wp_get_attachment_image($gallery_ids[0], 'medium_large', false, [
            'class' => 'rv-product-card__img rv-product-card__img--hover',
            'alt' => $title
    ]);
}

/** 3. ΛΟΙΠΑ ΣΤΟΙΧΕΙΑ */
$cat_name = '';
$cats = wp_get_post_terms($post_obj->ID, 'product_cat', ['fields' => 'names']);
if (is_array($cats) && !empty($cats)) $cat_name = array_shift($cats);

$price_html = $product ? $product->get_price_html() : '';
$in_stock = $product ? $product->is_in_stock() : false;

// CTA Logic
$show_add_to_cart = $product && $product->is_purchasable() && $in_stock;
$add_to_cart_text = $show_add_to_cart ? __('Προσθήκη', 'ruined') : __('Περισσότερα', 'ruined');

if (function_exists('YITH_WCWL') && class_exists('YITH_WCWL_Frontend')) {
    $wishlist = YITH_WCWL();
    if (method_exists($wishlist, 'get_add_to_wishlist_button')) {
        echo $wishlist->get_add_to_wishlist_button($post_obj->ID);
    }
}
?>

<li class="rv-product-card">

    <a class="rv-product-card__link"
       href="<?php echo esc_url($permalink); ?>"
       aria-label="<?php echo esc_attr($title); ?>">

        <div class="rv-product-card__media">

            <?php echo $main_image_html; ?>

            <?php if ($hover_image_html) : ?>
                <?php echo $hover_image_html; ?>
            <?php endif; ?>
            <div class="rv-product-card__wishlist">
                <?php
                echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $post_obj->ID . '"]');
                ?>
            </div>
            <?php if ($in_stock) : ?>
                <span class="rv-product-card__stock">
                    <i></i><?php esc_html_e('Διαθέσιμο', 'ruined'); ?>
                </span>
            <?php endif; ?>

        </div>

        <div class="rv-product-card__meta">

            <?php if ($cat_name) : ?>
                <div class="rv-product-card__tag"><?php echo esc_html($cat_name); ?></div>
            <?php endif; ?>

            <h3 class="rv-product-card__title">
                <?php
                $title_display = esc_html($title);
                $last_space_position = strrpos($title_display, ' ');
                if ($last_space_position !== false) {
                    $title_display = substr_replace($title_display, '&nbsp;', $last_space_position, 1);
                }
                echo $title_display;
                ?>
            </h3>

            <?php if ($price_html) : ?>
                <div class="rv-product-card__price"><?php echo wp_kses_post($price_html); ?></div>
            <?php endif; ?>

            <?php if ($in_stock) : ?>
                <span class="rv-product-card__stock list_stock">
                    <i></i><?php esc_html_e('Διαθέσιμο', 'ruined'); ?>
                </span>
            <?php endif; ?>

        </div>
    </a>

    <a href="<?php echo esc_url($permalink); ?>"
       class="rv-product-card__btn"
       aria-label="<?php echo esc_attr($add_to_cart_text); ?>">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" role="img" aria-hidden="true">
            <rect x="1" y="1" width="30" height="30" rx="6"></rect>
            <path d="M13 9l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </a>

</li>