<?php
/**
 * Custom Product Card Template - MIMICS FIBO SEARCH DEFAULT MARKUP
 */
if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (empty($product)) {
    return;
}

$product_url = get_permalink($product->get_id());
?>

<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"
   class="dgwt-wcas-suggestion dgwt-wcas-suggestion-product"
   data-post-id="<?php echo esc_attr($product->get_id()); ?>">

    <span class="dgwt-wcas-si">
        <?php echo $product->get_image('woocommerce_thumbnail'); ?>
    </span>

    <div class="dgwt-wcas-content-wrapp">

        <div class="dgwt-wcas-st">
            <div class="dgwt-wcas-arrow-wrapper">
                <?php /* Αφαιρούμε το rv_button_arrow και βάζουμε μόνο το markup του εικονιδίου */ ?>
                <div class="button-arrow button-arrow--black">
        <span class="button-arrow__icon">
            <span class="button-arrow__arrow button-arrow__arrow--front"></span>
            <span class="button-arrow__arrow button-arrow__arrow--back"></span>
            <span class="button-arrow__fill"></span>
        </span>
                </div>
            </div>
            <?php if ($product->get_sku()) : ?>
                <span class="dgwt-wcas-sku">
                    (ΚΩΔ: <?php echo esc_html($product->get_sku()); ?>)
                </span>
            <?php endif; ?>
            <span class="dgwt-wcas-st-title">
                <?php echo esc_html($product->get_name()); ?>
            </span>
        </div>

    </div>
</a>