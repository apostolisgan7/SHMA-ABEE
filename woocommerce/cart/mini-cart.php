<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart'); ?>

<?php if (!WC()->cart->is_empty()) : ?>

    <ul class="mini-cart" data-lenis-prevent>
        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
            $product = $cart_item['data'];
            if (!$product || !$product->exists()) continue;
            ?>

            <li class="mini-cart__item" data-key="<?php echo esc_attr($cart_item_key); ?>">

                <div class="mini-cart__image">
                    <?php echo $product->get_image('woocommerce_thumbnail'); ?>
                </div>

                <div class="mini-cart__content">
                    <p class="mini-cart__title">
                        <?php echo esc_html($product->get_name()); ?>
                    </p>

                    <div class="mini-cart__meta">
                        <?php
                        $sku = $product->get_sku();

                        // Αν είναι variation και δεν έχει δικό του SKU,
                        // πάρε του parent
                        if (empty($sku) && $product->is_type('variation')) {
                            $parent = wc_get_product($product->get_parent_id());
                            if ($parent) {
                                $sku = $parent->get_sku();
                            }
                        }
                        ?>
                        <?php if ($sku) : ?>
                            <span class="mini-cart__sku">
                              SKU: <?php echo esc_html($sku); ?>
                             </span>
                        <?php endif; ?>
                        <div class="mini-cart__qty">
                            <button class="qty-minus" data-key="<?php echo esc_attr($cart_item_key); ?>">−</button>
                            <span class="qty-value"><?php echo esc_html($cart_item['quantity']); ?></span>
                            <button class="qty-plus" data-key="<?php echo esc_attr($cart_item_key); ?>">+</button>
                        </div>
                        <span class="mini-cart__price">
                <?php echo WC()->cart->get_product_price($product); ?>
            </span>
                    </div>
                </div>

                <button class="mini-cart__remove remove_from_cart_button"
                        data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>">
                    ×
                </button>

            </li>
        <?php endforeach; ?>
    </ul>

    <div class="mini-cart__footer">
        <div class="mini-cart__subtotal">
            <span><?php esc_html_e('Subtotal', 'ruined'); ?></span>
            <strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
        </div>

        <a href="<?php echo wc_get_cart_url(); ?>" class="button button--primary">
            View cart
        </a>

        <a href="<?php echo wc_get_checkout_url(); ?>" class="button button--checkout">
            Checkout
        </a>
    </div>

<?php else : ?>
    <p class="mini-cart__empty">Το καλάθι είναι άδειο</p>
<?php endif; ?>


<?php do_action('woocommerce_after_mini_cart'); ?>
