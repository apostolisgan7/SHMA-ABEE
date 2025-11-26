
    <nav id="menu"  class="mmmenu">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_id'        => 'mobile-menu-list',
            'depth'          => 3,
        ]);
        ?>
    </nav>

