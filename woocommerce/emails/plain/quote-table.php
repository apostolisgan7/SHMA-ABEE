<?php
/**
 * HTML Template Email
 *
 * @package YITH\RequestAQuote
 * @since   1.0.0
 * @author  YITH <plugins@yithemes.com>
 */

// Ruined override: no prices (line subtotals and totals removed)
do_action( 'yith_ywraq_email_before_raq_table', $order );


echo "****************************************************\n\n";

echo "\n";
$currency = $order->get_currency();
$items = $order->get_items();
if ( ! empty( $items ) ) :
	foreach ( $items as $item ) :
		$product = yith_ywraq_get_product( $item['product_id'] );


		$meta  = yith_ywraq_get_product_meta_from_order_item( $item['item_meta'], false );
		$title = $product->get_title(); //phpcs:ignore

		if ( $product->get_sku() !== '' && ywraq_show_element_on_list( 'sku' ) ) {
			$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $product->get_sku();
			$title .= apply_filters( 'ywraq_sku_label_html', $sku, $_product ); //phpcs:ignore
		}

		echo esc_html( $title . ' ' . yith_ywraq_get_product_meta( $item, false ) . ' | ' );
		echo esc_html( $item['qty'] );
		echo "\n";
	endforeach;



	echo "\n";
endif;

echo "\n****************************************************\n\n";

do_action( 'yith_ywraq_email_after_raq_table', $order );
