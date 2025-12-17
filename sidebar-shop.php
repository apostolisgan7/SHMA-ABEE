<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if YITH WooCommerce Ajax Product Filter is active
$has_yith = class_exists('YITH_WCAN');
?>

<aside id="secondary" class="widget-area shop-sidebar">

    <?php if (is_active_sidebar('sidebar-shop')) : ?>
        <?php dynamic_sidebar('sidebar-shop'); ?>
    <?php else : ?>
        <?php if (!$has_yith) : ?>
            <div class="default-widget">
                <h3><?php esc_html_e('Categories', 'ruined'); ?></h3>
                <?php the_widget('WC_Widget_Product_Categories'); ?>
            </div>
            
            <div class="default-widget">
                <h3><?php esc_html_e('Filter by Price', 'ruined'); ?></h3>
                <?php the_widget('WC_Widget_Price_Filter'); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</aside>
