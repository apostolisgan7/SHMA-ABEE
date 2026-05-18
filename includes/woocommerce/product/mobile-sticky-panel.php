<?php
defined('ABSPATH') || exit;
global $product;
if (!$product) return;
?>

<div class="rv-mobile-sticky-wrap">
    <button class="rv-mobile-sticky-btn" aria-expanded="false">
        <span>Αίτηση Προσφοράς</span>
    </button>
</div>

<div class="rv-mobile-panel" id="rv-mobile-panel" aria-hidden="true">
    <div class="rv-mobile-panel-overlay"></div>
    <div class="rv-mobile-panel-drawer">
        <div class="rv-mobile-panel-header">
            <div class="rv-mobile-panel-info">
                <h2 class="rv-mobile-panel-title"><?php echo esc_html(get_the_title()); ?></h2>
                <?php if ($product->get_sku()) : ?>
                    <span class="rv-mobile-panel-sku">Κωδικός: <?php echo esc_html($product->get_sku()); ?></span>
                <?php endif; ?>
            </div>
            <button class="rv-mobile-panel-close" aria-label="Κλείσιμο">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L17 17M17 1L1 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        <div class="rv-mobile-panel-body" id="rv-mobile-panel-body"></div>
    </div>
</div>
