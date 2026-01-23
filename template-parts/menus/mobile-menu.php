<?php
$menu_items = wp_get_nav_menu_items('mobile-menu');
?>

<div class="mobile-menu-backdrop"></div>

<aside class="mobile-menu">

    <div class="mobile-menu__header">
        <button class="mobile-menu__back">←</button>
        <h3 class="mobile-menu__title">Menu</h3>
        <button class="mobile-menu__close">✕</button>
    </div>

    <div class="mobile-menu__stage">
        <div class="mobile-menu__track">

            <?php
            // Χτίζουμε levels manually
            $levels = [];

            foreach ($menu_items as $item) {
                $levels[$item->menu_item_parent][] = $item;
            }

            function render_mobile_panels($parent_id, $levels, $level = 0) {
                if (!isset($levels[$parent_id])) return;

                echo '<div   class="mobile-menu__panel"   data-parent="'.$parent_id.'">';

                foreach ($levels[$parent_id] as $item) {
                    $has_children = isset($levels[$item->ID]);

                    echo '<button 
            class="menu-item '.($has_children ? 'has-children' : '').'"
            data-id="'.$item->ID.'"
            data-title="'.esc_attr($item->title).'"
            '.(!$has_children ? 'data-link="'.$item->url.'"' : '').'
        >';
                    echo esc_html($item->title);
                    if ($has_children) echo '<span class="chevron">›</span>';
                    echo '</button>';
                }

                echo '</div>';

                // 🔁 recursion: NEW PANEL per item that has children
                foreach ($levels[$parent_id] as $item) {
                    if (isset($levels[$item->ID])) {
                        render_mobile_panels($item->ID, $levels, $level + 1);
                    }
                }
            }


            render_mobile_panels(0, $levels);
            ?>

        </div>
    </div>
    <?php get_template_part('template-parts/menus/mobile-bottom-nav'); ?>


</aside>


