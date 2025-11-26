<?php
/**
 * Custom Product Card Template - MIMICS FIBO SEARCH DEFAULT MARKUP
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( empty( $product ) ) {
    return;
}
?>

<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"
   class="dgwt-wcas-suggestion dgwt-wcas-suggestion-product"
   data-post-id="<?php echo esc_attr( $product->get_id() ); ?>">

    <span class="dgwt-wcas-si">
        <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
    </span>

    <div class="dgwt-wcas-content-wrapp">
        <div class="dgwt-wcas-st">

            <span class="dgwt-wcas-st-title">
                <?php echo esc_html( $product->get_name() ); ?>
            </span>

            <?php if ( $product->get_sku() ) : ?>
                <span class="dgwt-wcas-sku">
                    (ΚΩΔ: <?php echo esc_html( $product->get_sku() ); ?>)
                </span>
            <?php endif; ?>

        </div>
    </div>
</a>