<?php
/**
 * WooCommerce template file
 */

get_header();

$has_sidebar = is_active_sidebar('sidebar-shop') || class_exists('YITH_WCAN');
?>

<main id="primary" class="site-main">
    <?php rv_show_pages_hero(); ?>
    <div class="container mx-auto p-0">
        <?php do_action('ruined_before_shop_grid'); ?>
        <div class="flex flex-wrap -mx-4 shop-layout">
            <?php if ($has_sidebar && (is_shop() || is_product_category() || is_product_tag())) : ?>
                <div class="w-full lg:w-1/4 px-4 shop_sidebar">
                    <?php get_sidebar('shop'); ?>
                </div>
                <div class="w-full lg:w-3/4 px-4 shop_content">
            <?php else : ?>
                <div class="w-full px-4">
            <?php endif; ?>

                <?php woocommerce_content(); ?>

            </div>
        </div>
    </div>
</main>

<?php
get_footer();

