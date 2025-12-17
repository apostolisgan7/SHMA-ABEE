<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="offcanvas-cart" class="offcanvas-cart">
    <div class="offcanvas-cart__content">
        <div class="offcanvas-cart__header">
            <h3 class="offcanvas-cart__title"><?php esc_html_e('Your Cart', 'ruined'); ?></h3>
            <button class="offcanvas-cart__close" aria-label="<?php esc_attr_e('Close cart', 'ruined'); ?>">
                <span></span>
                <span></span>
            </button>
        </div>
        <div class="offcanvas-cart__body">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
</div>
<div class="offcanvas-cart__overlay"></div>