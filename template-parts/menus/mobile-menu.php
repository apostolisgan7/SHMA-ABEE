<?php
/**
 * Mobile Menu
 *
 * STRUCTURE CHANGE: .bottom-nav is now INSIDE the .mobile-menu <aside>.
 * This makes it a flex child of the menu column, so it always sits
 * at the bottom of the visible menu area — never behind browser chrome.
 *
 * The menu shell uses top + bottom (not height: vh) to define its size,
 * and the content stage (between header and bottom-nav) scrolls independently.
 */
$menu_items = wp_get_nav_menu_items('mobile-menu');
?>

<div class="mobile-menu-backdrop"></div>

<aside class="mobile-menu" aria-hidden="true" aria-label="<?php esc_attr_e('Mobile Navigation', 'ruined'); ?>">

    <div class="mobile-menu__header">
        <button class="mobile-menu__back" aria-label="<?php esc_attr_e('Πίσω', 'ruined'); ?>" aria-hidden="true">←</button>
        <h3 class="mobile-menu__title"><?php esc_html_e('Menu', 'ruined'); ?></h3>
        <button class="mobile-menu__close" aria-label="<?php esc_attr_e('Κλείσιμο μενού', 'ruined'); ?>">✕</button>
    </div>

    <div class="mobile-menu__stage">
        <div class="mobile-menu__track">

            <?php
            $levels = [];

            if ( ! empty($menu_items) && ! is_wp_error($menu_items) ) {
                foreach ($menu_items as $item) {
                    $levels[$item->menu_item_parent][] = $item;
                }
            }

            if ( ! function_exists('render_mobile_panels') ) :
                function render_mobile_panels($parent_id, $levels, $level = 0) {
                    if (!isset($levels[$parent_id])) return;

                    echo '<div class="mobile-menu__panel" data-parent="' . (int) $parent_id . '">';

                    foreach ($levels[$parent_id] as $item) {
                        $has_children = isset($levels[$item->ID]);

                        echo '<button
            class="menu-item ' . ($has_children ? 'has-children' : '') . '"
            data-id="' . (int) $item->ID . '"
            data-title="' . esc_attr($item->title) . '"
            ' . (!$has_children ? 'data-link="' . esc_url($item->url) . '"' : '') . '
        >';
                        echo esc_html($item->title);
                        if ($has_children) echo '<span class="chevron" aria-hidden="true">›</span>';
                        echo '</button>';
                    }

                    echo '</div>';

                    foreach ($levels[$parent_id] as $item) {
                        if (isset($levels[$item->ID])) {
                            render_mobile_panels($item->ID, $levels, $level + 1);
                        }
                    }
                }
            endif;

            render_mobile_panels(0, $levels);
            ?>

        </div>
    </div>

    <?php
    get_template_part('template-parts/menus/mobile-bottom-nav');
    ?>

</aside>