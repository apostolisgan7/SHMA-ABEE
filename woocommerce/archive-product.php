<?php
/**
 * The Template for displaying product archives
 */

get_header();
?>

    <main id="primary" class="site-main">
        <?php rv_show_pages_hero(); ?>
        <div class="section-full-width">
            <div class="container sec_padding mx-auto">
                <?php do_action('ruined_before_shop_grid'); ?>

                <div class="flex flex-wrap -mx-4 shop-layout">
                    <?php if (is_active_sidebar('sidebar-shop') && (is_shop() || is_product_category() || is_product_tag())) : ?>
                    <div class="w-full lg:w-1/4 px-4 shop_sidebar">
                        <?php get_sidebar('shop'); ?>
                    </div>
                    <div class="w-full lg:w-3/4 px-4 shop_content">
                        <?php else : ?>
                        <div class="w-full px-4">
                            <?php endif; ?>

                            <?php if (woocommerce_product_loop()) : ?>
                                <?php do_action('woocommerce_before_shop_loop'); ?>

                                <ul class="products columns-4 rv-products-grid">
                                    <?php
                                    if (wc_get_loop_prop('total')) {
                                        while (have_posts()) {
                                            the_post();
                                            wc_get_template_part('content', 'product');
                                        }
                                    }
                                    ?>
                                </ul>

                                <?php do_action('woocommerce_after_shop_loop'); ?>

                                <div class="rv-load-more-wrap">
                                    <?php
                                    rv_render_load_more_button();
                                    ?>
                                </div>

                            <?php else : ?>
                                <?php do_action('woocommerce_no_products_found'); ?>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
    </main>

<?php
get_footer();