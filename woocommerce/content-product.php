<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
    return;
}

// Include custom product item template
set_query_var( 'product_post', get_post() );
get_template_part( 'template-parts/items/item', 'product' );
?>
