<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>

    <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
        <div class="rv-breadcrumbs"><?php rank_math_the_breadcrumbs(); ?></div>
    <?php endif; ?>
    <div class="single_top_wrapper">
        <div class="left-area-wrapper">
            <div class="product-gallery-area">
                <?php
                // ΜΟΝΟ εικόνες / swiper
                do_action('woocommerce_before_single_product_summary');
                ?>
            </div>

            <div class="product-meta-below-gallery">
                <?php do_action('rv_product_meta_below_gallery'); ?>
            </div>
        </div>

        <div class="summary entry-summary">
            <?php do_action('rv_custom_summary_layout'); ?>
        </div>
    </div>

    <?php do_action('rv_product_tabs'); ?>
    <div class="video_box_wrapper">
        <?php do_action('rv_product_video_box'); ?>
    </div>
    <?php
    woocommerce_output_related_products();
    ?>
</div>

<?php do_action('woocommerce_after_single_product'); ?>
