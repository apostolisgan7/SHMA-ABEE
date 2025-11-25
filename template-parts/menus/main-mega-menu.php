<?php
/**
 * Mega Menu Template
 */

if ( ! has_nav_menu( 'primary' ) ) {
    return;
}

$locations = get_nav_menu_locations();
$menu_id   = $locations['primary'];
$menu_items = wp_get_nav_menu_items( $menu_id );

// Group by parent
$parents = [];
foreach ( $menu_items as $item ) {
    if ( $item->menu_item_parent == 0 ) {
        $parents[$item->ID] = [
            'item'   => $item,
            'childs' => []
        ];
    }
}

// Attach sub-items
foreach ( $menu_items as $item ) {
    if ( $item->menu_item_parent != 0 && isset( $parents[$item->menu_item_parent] ) ) {
        $parents[$item->menu_item_parent]['childs'][] = $item;
    }
}

$parents = array_values($parents);

$left_items  = array_slice($parents, 0, 3);
$right_items = array_slice($parents, 3);
?>

<div class="mega-overlay" id="megaMenuOverlay"></div>

<div id="megaMenu" class="mega-menu-container" data-lenis-prevent>

    <button class="mega-close-btn" id="megaMenuClose">
        ✕
    </button>

    <div class="mega-inner">

        <div class="mega-columns"data-lenis-prevent>

            <!-- LEFT COLUMN -->
            <div class="mega-col">
                <?php foreach ($left_items as $block): ?>
                    <div class="mega-item">
                        <a href="<?php echo esc_url($block['item']->url); ?>" class="mega-parent">
                            <?php echo esc_html($block['item']->title); ?>
                        </a>

                        <?php if (!empty($block['childs'])): ?>
                            <div class="mega-sub">
                                <?php foreach ($block['childs'] as $sub): ?>
                                    <a href="<?php echo esc_url($sub->url); ?>" class="mega-sub-link">
                                        <?php echo esc_html($sub->title); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="mega-col">
                <?php foreach ($right_items as $block): ?>
                    <div class="mega-item">
                        <a href="<?php echo esc_url($block['item']->url); ?>" class="mega-parent">
                            <?php echo esc_html($block['item']->title); ?>
                        </a>

                        <?php if (!empty($block['childs'])): ?>
                            <div class="mega-sub">
                                <?php foreach ($block['childs'] as $sub): ?>
                                    <a href="<?php echo esc_url($sub->url); ?>" class="mega-sub-link">
                                        <?php echo esc_html($sub->title); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- FOOTER MENU ROWS -->
        <div class="mega-footer">

            <div class="mega-footer-col">
                <h4>LET'S GET SOCIAL</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'social-menu',
                    'container' => false,
                    'menu_class' => 'mega-footer-links',
                ]);
                ?>
                <div class="bottom_links">
                    <a href="#">ΝΟΜΟΘΕΣΙΑ</a>
                    <a href="#">ισολογισμοι</a>
                </div>
            </div>

            <div class="mega-footer-col">
                <h4>ΕΠΙΚΟΙΝΩΝΗΣΤΕ ΜΑΖΙ ΜΑΣ</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'support-menu',
                    'container' => false,
                    'menu_class' => 'mega-footer-links',
                ]);
                ?>
                <div class="bottom_links">
                    <a href="#">ΟΡΟΙ ΧΡΗΣΗΣ</a>
                    <a href="#">ΠΟΛΙΤΙΚΗ COOKIES</a>
                    <a href="#">ΠΟΛΙΤΙΚΗ ΑΠΟΡΡΗΤΟΥ</a>
                </div>
            </div>

        </div>

    </div>
</div>
