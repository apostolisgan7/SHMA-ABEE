<?php
/**
 * Catalog Menu Popup
 *
 * @package Ruined
 */

$locations = get_nav_menu_locations();
$menu_id = isset($locations['catalog-menu']) ? $locations['catalog-menu'] : null;

$menu_items = $menu_id ? wp_get_nav_menu_items($menu_id) : [];
$parents = [];
$children = [];

if ($menu_items) {
    foreach ($menu_items as $item) {
        if ($item->menu_item_parent == 0) {
            $parents[$item->ID] = $item;
        } else {
            $children[$item->menu_item_parent][] = $item;
        }
    }
}
?>

<div id="megaMenuBackdrop" class="mega-backdrop">
    <div id="megaMenuContainer" class="mega-container">

        <div class="mega-bg-deco"></div>


        <div class="mega-header mega-animate-header">
            <div class="left mega-animate-header">
                <div class="logo mega-animate-header">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" version="1.1" viewBox="0 0 227 44">
                        <defs>
                            <style>
                                .st0 {
                                    fill: #000;
                                }

                                .st1 {
                                    fill: none;
                                }

                                .st2 {
                                    clip-path: url(#clippath);
                                }
                            </style>
                            <clipPath id="clippath">
                                <rect class="st1" y="0" width="227" height="43.5"></rect>
                            </clipPath>
                        </defs>
                        <g class="st2">
                            <g>
                                <path class="st0" d="M76.7,30.8l.4-.4,8.7-8.6-9.3-9h19.6v-6.9h-31.6v4.9l11.3,10.9h-.2c0,.1-11.1,10.9-11.1,10.9v4.9h31.9v-6.9h-19.7Z"></path>
                                <path class="st0" d="M128.4,6v12.1h-18.5V6h-8v31.7h8v-12.7h18.5v12.7h8V6h-8Z"></path>
                                <path class="st0" d="M173.3,6l-10,20.6-.2-.4-9.8-20.3h-10.7v31.7h8v-20l9.7,20h6l9.7-20v20h8V6h-10.6Z"></path>
                                <path class="st0" d="M211.3,6.1h-6.6c0,0-2.7,0-2.7,0l-15.7,31.5h8.4l2.7-5.5h18.4c0,0,0,0,0,0l2.7,5.3h8.4l-15.7-31.5ZM206.5,25h-5.4l5.7-11.3h.4s5.1,11.3,5.1,11.3h-5.8s0,0,0,0Z"></path>
                                <path class="st0" d="M0,0v8.4l19.6,9.3V6.6l23.4,11.1V0H0Z"></path>
                                <path class="st0" d="M0,43.5v-8.4l19.6-9.3v11.1l23.4-11.1v17.7H0Z"></path>
                            </g>
                        </g>
                    </svg>

                </div>
                <h2 class="mega-title mega-animate-header">Κατάλογος Προϊόντων</h2>
            </div>

            <button class="mega-close mega-animate-header" type="button" data-catalog-close>
                ✕
            </button>
        </div>

        <div class="mega-grid">
            <div class="mega-left">
                <h3 class="column-label mega-animate-header">Κεντρικές Κατηγορίες</h3>
                <div class="mega-left-list">

                    <?php foreach ($parents as $parent):
                        $icon = get_field('menu_icon', $parent->ID);
                        ?>
                        <div class="mega-left-item" data-category="cat-<?php echo $parent->ID; ?>">
                            <?php if ($icon): ?>
                                <div class="item-icon">
                                    <?php
                                    if ($icon) {
                                        $svg = file_get_contents($icon['url']);
                                        echo '<div class="item-icon svg-icon">' . $svg . '</div>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <span class="item-label"><?php echo esc_html($parent->title); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php
                    rv_button_arrow([
                            'text' => __('Όλες οι κατηγορίες', 'ruined'),
                            'url' => '/shop/',
                            'target' => '_self',
                            'variant' => 'black',
                            'icon_position' => 'left',
                            'class' => 'catalog_btn mega-animate-header',
                            'register' => false,
                    ]);
                    ?>
                </div>

                <div class="left-footer mega-animate-right">
                    <div class="mega-very-bottom mega-animate-header">
                        <?php if (is_user_logged_in()) : ?>
                            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">o λογαριασμοσ μου</a>
                        <?php else : ?>
                            <a href="#" class="js-auth-modal-trigger">o λογαριασμοσ μου</a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url(function_exists('tinv_url_wishlist_default') ? tinv_url_wishlist_default() : '#'); ?>">AΓΑΠΗΜΕΝΑ ΠΡΟΙΟΝΤΑ</a>
                        <a href="<?php echo esc_url(home_url('/epikoinonia/')); ?>">επικοινωνια με πωλησεισ</a>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="mega-right">
                <div class="mega-watermark" data-watermark></div>
                <?php foreach ($parents as $parent): ?>
                    <div class="mega-right-panel" data-category-panel="cat-<?php echo $parent->ID; ?>">

                        <h3 class="panel-label">
                            <?php echo strtoupper($parent->title); ?>
                        </h3>

                        <div class="sub-grid" data-lenis-prevent>
                            <?php if (!empty($children[$parent->ID])): ?>
                                <?php foreach ($children[$parent->ID] as $child): ?>
                                    <a href="<?php echo esc_url($child->url); ?>"
                                       class="sub-item mega-animate-right">
                                        <?php echo esc_html($child->title); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <?php
                        rv_button_arrow([
                                'text' => __('Όλα τα προιόντα', 'ruined'),
                                'url' => $parent->url,
                                'target' => '_self',
                                'variant' => 'black',
                                'icon_position' => 'left',
                                'class' => 'catalog_btn mega-animate-right',
                                'register' => false,
                        ]);
                        ?>

                        <div class="mega-bottom">
                            <div class="footer-links">
                                <span>ολα τα προιοντα μασ</span>
                                <span><?php esc_html_e( 'πόροι προϊόντων', 'ruined' ); ?></span>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

        </div>


    </div>
</div>
