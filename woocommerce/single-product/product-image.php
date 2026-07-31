<?php
defined( 'ABSPATH' ) || exit;

global $product;

$featured_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();

// array_values reindexes 0..n-1 after array_filter drops empty ids, so
// position-based checks below (slicing the first 4, flagging index 3)
// can rely on $index actually meaning "position", not a possibly-gapped key.
$all_ids = array_values(array_filter(array_merge([$featured_id], $gallery_ids)));

// Αν δεν υπάρχει καμία εικόνα, βάζουμε το placeholder string για να παίξει το loop
if ( empty( $all_ids ) ) {
    $all_ids[] = 'placeholder';
}
?>

<div class="woocommerce-product-gallery woocommerce-product-gallery--with-swiper">

    <div class="rv-gallery-main swiper">

        <?php if (shortcode_exists('ti_wishlists_addtowishlist')) : ?>
            <div class="rv-gallery-wishlist">
                <?php echo do_shortcode('[ti_wishlists_addtowishlist product_id="' . get_the_ID() . '" variation_id="0"]'); ?>
            </div>
        <?php endif; ?>

        <div class="swiper-wrapper">

            <?php foreach ($all_ids as $img_id): ?>
                <div class="swiper-slide">
                    <?php
                    if ( $img_id === 'placeholder' ) {
                        // Εμφάνιση Placeholder Εικόνας WooCommerce
                        echo sprintf(
                                '<img src="%s" alt="%s" class="main-slide-image wp-post-image" />',
                                esc_url( wc_placeholder_img_src( 'large' ) ),
                                esc_html__( 'Awaiting product image', 'woocommerce' )
                        );
                    } else {
                        // Κανονική Εικόνα Προϊόντος
                        $full = wp_get_attachment_image_url($img_id, 'full');
                        echo wp_get_attachment_image(
                                $img_id,
                                'full',
                                false,
                                [
                                        'class' => 'main-slide-image',
                                                        'data-src' => esc_url($full),
                                ]
                        );
                    }
                    ?>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="rv-gallery-controls">
            <button class="swiper-button-prev"></button>
            <button class="swiper-button-next"></button>
        </div>
    </div>

    <div class="rv-gallery-thumbs-wrap">
        <div class="rv-gallery-thumbs swiper">
            <div class="swiper-wrapper">
                <?php
                // Only the first 4 thumbs are ever rendered here — the rest
                // of $all_ids never hits this markup at all, so there's no
                // scroll/overflow/cap CSS to fight with. The full set is
                // still available via the main swiper + the "+N" badge,
                // which opens the Fancybox gallery over all of $all_ids.
                $max_visible_thumbs = 4;
                $hidden_thumbs_count = count($all_ids) - $max_visible_thumbs;
                $visible_thumb_ids = array_slice($all_ids, 0, $max_visible_thumbs, true);

                foreach ($visible_thumb_ids as $index => $img_id):
                    $is_overflow_thumb = $hidden_thumbs_count > 0 && $index === $max_visible_thumbs - 1;
                    ?>
                    <div class="swiper-slide<?php echo $is_overflow_thumb ? ' rv-gallery-thumb--more' : ''; ?>">
                        <?php
                        if ( $img_id === 'placeholder' ) {
                            echo sprintf(
                                    '<img src="%s" alt="%s" class="thumb-slide-image" />',
                                    esc_url( wc_placeholder_img_src( 'thumbnail' ) ),
                                    esc_html__( 'Placeholder', 'woocommerce' )
                            );
                        } else {
                            echo wp_get_attachment_image($img_id, 'thumbnail', false, ['class' => 'thumb-slide-image']);
                        }
                        if ( $is_overflow_thumb ) : ?>
                            <span class="rv-gallery-thumb-count">+<?php echo esc_html($hidden_thumbs_count); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="zoom-wrapper">
            <button type="button" class="rv-gallery-zoom" aria-label="<?php esc_attr_e('Μεγέθυνση', 'ruined'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                </svg>
            </button>
        </div>
    </div>

</div>