<?php
defined('ABSPATH') || exit;
?>

<div class="archive-header">

    <div class="archive-header__left">
        <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

        <?php do_action('yith_wcan_filters_opener'); ?>

        <?php woocommerce_result_count(); ?>
    </div>

    <div class="archive-header__right">
        <?php do_action('ruined_shop_view_toggle'); ?>
        <?php woocommerce_catalog_ordering(); ?>
    </div>

</div>
