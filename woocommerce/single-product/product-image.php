<?php
defined( 'ABSPATH' ) || exit;

global $product;

$featured_id = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();

$all_ids = array_filter(array_merge([$featured_id], $gallery_ids));
?>

<div class="woocommerce-product-gallery woocommerce-product-gallery--with-swiper">

    <!-- MAIN -->
    <div class="rv-gallery-main swiper">
        <div class="swiper-wrapper">

            <?php foreach ($all_ids as $img_id): ?>
                <div class="swiper-slide">
                    <?php echo wp_get_attachment_image($img_id, 'large', false, ['class' => 'main-slide-image']); ?>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="rv-gallery-controls">
            <button class="swiper-button-prev"></button>
            <button class="swiper-button-next"></button>
        </div>
    </div>

    <!-- THUMBNAILS -->
    <div class="rv-gallery-thumbs swiper">
        <div class="swiper-wrapper">
            <?php foreach ($all_ids as $img_id): ?>
                <div class="swiper-slide">
                    <?php echo wp_get_attachment_image($img_id, 'thumbnail', false, ['class' => 'thumb-slide-image']); ?>
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
