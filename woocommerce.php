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

                    <?php
                    $paged = 1;
                    $per_page = 12;

                    $args = [
                            'post_type'      => 'product',
                            'post_status'    => 'publish',
                            'posts_per_page' => $per_page,
                            'paged'          => $paged,
                    ];

                    $query = new WP_Query($args);
                    ?>

                    <?php if ($query->have_posts()) : ?>
                        <ul class="products columns-4 rv-products-grid" data-page="1">
                            <?php while ($query->have_posts()) : $query->the_post(); ?>
                                <?php wc_get_template_part('content', 'product'); ?>
                            <?php endwhile; ?>
                        </ul>

                        <?php if ($query->max_num_pages > 1) : ?>
                            <div class="rv-load-more-wrap">
                                <?php
                                rv_button_arrow([
                                        'text' => 'Φόρτωσε περισσότερα',
                                        'url' => '#',
                                        'target' => '_self',
                                        'variant' => 'black',
                                        'icon_position' => 'left',
                                        'class' => 'rv-load-more',
                                ]);
                                ?>
                                <span class="rv-load-more-data"
                                      data-max="<?php echo esc_attr($query->max_num_pages); ?>"></span>
                            </div>
                        <?php endif; ?>


                    <?php else : ?>
                        <p>Δεν βρέθηκαν προϊόντα.</p>
                    <?php endif; ?>

                    <?php wp_reset_postdata(); ?>

                </div>
        </div>
    </div>
</main>

<?php
get_footer();

