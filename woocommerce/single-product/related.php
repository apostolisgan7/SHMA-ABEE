<?php
/**
 * Related Products
 *
 * @package WooCommerce\Templates
 * @version 10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $related_products ) :
    /**
     * Ensure all images of related products are lazy loaded by increasing the
     * current media count to WordPress's lazy loading threshold if needed.
     * Because wp_increase_content_media_count() is a private function, we
     * check for its existence before use.
     */
    if ( function_exists( 'wp_increase_content_media_count' ) ) {
        $content_media_count = wp_increase_content_media_count( 0 );
        if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
            wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
        }
    }

    $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Σχετικά Προϊόντα', 'ruined' ) );
    ?>

    <section class="rv-related-products">
        <div class="rv-related-products__inner">

            <div class="rv-related-products__head">
                <h2 class="rv-related-products__title">
                    <?php echo esc_html( $heading ); ?>
                </h2>

                <div class="rv-rp__navwrap">
                    <button class="rv-rp__nav rv-rp__nav--prev" aria-label="<?php esc_attr_e('Προηγούμενο','ruined'); ?>">
                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.68422 11.4943L0.911987 6.20308L6.68422 0.911865" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button class="rv-rp__nav rv-rp__nav--next" aria-label="<?php esc_attr_e('Επόμενο','ruined'); ?>">
                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.911971 0.911955L6.6842 6.20317L0.91197 11.4944" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="rv-related-products__carousel swiper" data-slider="products">
                <div class="swiper-wrapper">
                    <?php foreach ( $related_products as $related_product ) : ?>
                        <div class="swiper-slide">
                            <?php
                            $post_object = get_post( $related_product->get_id() );
                            setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

                            set_query_var('product_post', $post_object);
                            get_template_part( 'template-parts/items/item', 'product' );
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination" aria-hidden="true"></div>
            </div>

            <?php wp_reset_postdata(); ?>

        </div>
    </section>

<?php endif; ?>
