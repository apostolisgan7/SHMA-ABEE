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
                    <svg width="190" height="41" viewBox="0 0 190 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M72.3809 26.89L72.6536 26.6173L79.3289 20.0618L72.2033 13.2337H87.3012V8.00002H63V11.7301L71.6911 20.0618L71.5713 20.1527L63 28.3935V32.1236H87.5697V26.89H72.3809Z"
                              fill="#000"/>
                        <path d="M112.193 8.00416V17.2075H97.9379V8.00416H91.8037V32.1236H97.9379V22.4411H112.193V32.1236H118.327V8.00416H112.193Z"
                              fill="#000"/>
                        <path d="M146.743 8.00415L139.014 23.701L138.894 23.4284L131.314 8.00002H123.102V32.1195H129.27V16.9018L136.73 32.1195H141.331L148.792 16.9018V32.1195H154.926V8.00415H146.747H146.743Z"
                              fill="#000"/>
                        <path d="M177.946 8.09502L171.659 8.15699H169.553L157.491 32.1277H163.956L166.062 27.9763H172.771L181.462 27.9144L181.491 28.0053L183.564 32.0658H190L177.938 8.09502H177.946ZM172.985 22.499H168.834L173.27 13.8368H174.729L174.791 13.9276L178.698 22.47L172.985 22.499Z"
                              fill="#000"/>
                        <path d="M41.3449 0H12.2898V1.14957L41.3449 17.5243V0Z" fill="#000"/>
                        <path d="M41.3449 40.2041H12.2898V39.0502L41.3449 22.6754V40.2041Z" fill="#000"/>
                        <path d="M0 1.52588e-05V8.42871L16.1422 17.5243V1.52588e-05H0Z" fill="#000"/>
                        <path d="M0 31.7754V40.2041H16.1422V22.6798L0 31.7754Z" fill="#000"/>
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
                            'url' => '#',
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
                        <span>o λογαριασμοσ μου</span>
                        <span>AΓΑΠΗΜΕΝΑ ΠΡΟΙΟΝΤΑ</span>
                        <span>επικοινωνια με πωλησεισ</span>
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

                        <div class="sub-grid">
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
                                'url' => '#',
                                'target' => '_self',
                                'variant' => 'black',
                                'icon_position' => 'left',
                                'class' => 'catalog_btn',
                                'register' => false,
                        ]);
                        ?>

                        <div class="mega-bottom">
                            <div class="footer-links">
                                <span>ολα τα προιοντα μασ</span>
                                <span>product resources</span>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

        </div>


    </div>
</div>
