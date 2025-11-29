<nav id="menu">
    <?php
    wp_nav_menu([
            'theme_location' => 'mobile-menu',
            'container'      => false,
            'menu_id'        => 'mobile-menu-list',
            'depth'          => 4,
    ]);
    ?>
</nav>
