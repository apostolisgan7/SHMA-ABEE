<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_yith = class_exists('YITH_Request_Quote');
?>
<div id="offcanvas-cart" class="offcanvas-cart">
    <div class="offcanvas-cart__content">
        <div class="offcanvas-cart__header">
            <h3 class="offcanvas-cart__title">
                <?php echo $is_yith ? 'Λίστα Προσφοράς' : __('Καλάθι', 'ruined'); ?>
            </h3>
            <button class="offcanvas-cart__close" aria-label="<?php esc_attr_e('Close', 'ruined'); ?>">
                ✕
            </button>
        </div>
        <div class="offcanvas-cart__body">
            <?php if ($is_yith) : ?>
                <?php echo rv_raq_mini_list_html(); ?>
            <?php else : ?>
                <?php woocommerce_mini_cart(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="offcanvas-cart__overlay"></div>
