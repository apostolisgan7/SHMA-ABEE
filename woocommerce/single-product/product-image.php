<?php
defined( 'ABSPATH' ) || exit;

global $product;

$featured_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();

$all_ids = array_filter(array_merge([$featured_id], $gallery_ids));

// Αν δεν υπάρχει καμία εικόνα, βάζουμε το placeholder string για να παίξει το loop
if ( empty( $all_ids ) ) {
    $all_ids[] = 'placeholder';
}
?>

<div class="woocommerce-product-gallery woocommerce-product-gallery--with-swiper">

    <div class="rv-gallery-main swiper">
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
                                'large',
                                false,
                                [
                                        'class' => 'main-slide-image',
                                        'data-fancybox' => 'product-gallery',
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

    <div class="rv-gallery-thumbs swiper">
        <div class="swiper-wrapper">
            <?php foreach ($all_ids as $img_id): ?>
                <div class="swiper-slide">
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
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="zoom-wrapper">
            <a class="rv-gallery-zoom">
                ⛶
            </a>
        </div>
    </div>

</div>