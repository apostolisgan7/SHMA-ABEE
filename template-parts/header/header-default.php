<?php
/**
 * The default header template
 *
 * @package Ruined
 */
?>
<header id="masthead" class="site-header bg-white shadow-sm">
    <div class="container">
        <div class="header-inner flex items-center justify-between">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) :
                    the_custom_logo();
                else :
                    ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                    <?php
                    $description = get_bloginfo('description', 'display');
                    if ($description || is_customize_preview()) :
                        ?>
                        <p class="site-description"><?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Desktop Navigation -->
            <div class="flex items-center space-x-4">
                <nav id="site-navigation" class="main-navigation hidden md:block">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'container'      => false,
                            'menu_class'     => 'desktop-menu flex items-center space-x-6',
                        )
                    );
                    ?>
                </nav>

                <!-- Search Toggle Button -->
                <button id="search-toggle" class="p-2 rounded-full hover:bg-gray-100 transition-colors" aria-label="<?php esc_attr_e('Search', 'ruined'); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="md:hidden" aria-label="<?php esc_attr_e('Toggle mobile menu', 'ruined'); ?>">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="mobile-menu-overlay fixed inset-0 bg-white dark:bg-dark-900 z-40 opacity-0 invisible transition-all duration-500 ease-in-out">
        <div class="container h-full flex items-center justify-center">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'mobile-menu',
                    'container'      => false,
                    'menu_class'     => 'mobile-menu text-center space-y-8',
                    'link_before'    => '<span class="menu-item-inner">',
                    'link_after'     => '</span>',
                )
            );
            ?>
        </div>
    </div>
</header>
