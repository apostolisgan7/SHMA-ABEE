<?php
/**
 * Template Part: Product Item Card
 * Args: [ 'post' => WP_Post ]
 */
if ( ! defined('ABSPATH') ) exit;

$post_obj = get_query_var('product_post');
if ( ! $post_obj instanceof WP_Post ) return;

$product   = wc_get_product( $post_obj->ID );
$permalink = get_permalink( $post_obj );
$title     = get_the_title( $post_obj->ID );

/** Main Image: featured ή Woo placeholder */
$img_id = get_post_thumbnail_id( $post_obj->ID );
if ( $img_id ) {
    $img_src = wp_get_attachment_image_url( $img_id, 'large' );
    $img_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $title;
} else {
    $img_src = function_exists('wc_placeholder_img_src')
            ? wc_placeholder_img_src( 'woocommerce_single' )
            : ( wc()->plugin_url() . '/assets/images/placeholder.png' );
    $img_alt = $title;
}

/** Gallery Image (first image from gallery) */
$gallery_img_src = '';
$gallery_img_alt = '';
$gallery_ids = $product ? $product->get_gallery_image_ids() : [];
if ( !empty($gallery_ids) ) {
    $gallery_img_src = wp_get_attachment_image_url( $gallery_ids[0], 'large' );
    $gallery_img_alt = get_post_meta( $gallery_ids[0], '_wp_attachment_image_alt', true ) ?: $title;
}

/** 1η κατηγορία */
$cat_name = '';
$cats = wp_get_post_terms( $post_obj->ID, 'product_cat', ['fields'=>'names'] );
if ( is_array($cats) && !empty($cats) ) $cat_name = array_shift($cats);

/** Τιμή/stock */
$price_html = $product ? $product->get_price_html() : '';
$in_stock   = $product ? $product->is_in_stock() : false;

/** CTA: add-to-cart ή read more */
$show_add_to_cart = $product && $product->is_purchasable() && $in_stock;
$add_to_cart_url  = $show_add_to_cart ? $product->add_to_cart_url() : $permalink;
$add_to_cart_text = $show_add_to_cart ? esc_html__('Προσθήκη', 'ruined') : esc_html__('Περισσότερα', 'ruined');
$btn_attrs        = $show_add_to_cart ? sprintf(
        'data-quantity="1" data-product_id="%d" data-product_sku="%s" rel="nofollow" class="rv-product-card__btn add_to_cart_button ajax_add_to_cart"',
        $product->get_id(),
        esc_attr( $product->get_sku() )
) : 'class="rv-product-card__btn"';
?>
<article class="rv-product-card">
    <a class="rv-product-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">

        <div class="rv-product-card__media">
            <img class="rv-product-card__img rv-product-card__img--main"
                 src="<?php echo esc_url( $img_src ); ?>"
                 alt="<?php echo esc_attr( $img_alt ); ?>"
                 loading="lazy" decoding="async" />
            
            <?php if ( !empty($gallery_img_src) ) : ?>
                <img class="rv-product-card__img rv-product-card__img--gallery"
                     src="<?php echo esc_url( $gallery_img_src ); ?>"
                     alt="<?php echo esc_attr( $gallery_img_alt ); ?>"
                     loading="lazy" decoding="async" />
            <?php endif; ?>

            <?php if ( $in_stock ) : ?>
                <span class="rv-product-card__stock"><i></i><?php _e('Διαθέσιμο','ruined'); ?></span>
            <?php endif; ?>
        </div>

        <div class="rv-product-card__meta">
            <?php if ( $cat_name ) : ?>
                <div class="rv-product-card__tag"><?php echo esc_html( $cat_name ); ?></div>
            <?php endif; ?>

            <h3 class="rv-product-card__title"><?php echo esc_html( $title ); ?></h3>

            <?php if ( $price_html ) : ?>
                <div class="rv-product-card__price"><?php echo wp_kses_post( $price_html ); ?></div>
            <?php endif; ?>

            <a href="<?php echo esc_url( $permalink ); ?>" class="rv-product-card__btn" aria-label="<?php echo esc_attr( $add_to_cart_text ); ?>">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" role="img" aria-hidden="true">
                    <rect x="1" y="1" width="30" height="30" rx="6"></rect>
                    <path d="M13 9l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>
        </div>

    </a>
</article>
