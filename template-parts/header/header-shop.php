<?php
/**
 * The shop header template
 *
 * @package Ruined
 */
?>
<header id="masthead" class="site-header header-shop">
    <div class="container">
        <div class="header-inner">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) :
                    the_custom_logo();
                else :
                    ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                <?php endif; ?>
            </div>

            <div class="header-shop-content">
                <nav id="shop-navigation" class="shop-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'shop',
                            'menu_id'        => 'shop-menu',
                            'container'      => false,
                        )
                    );
                    ?>
                </nav>

                <div class="header-actions">
                    <?php if (class_exists('WooCommerce')) : ?>

                        <!-- 🔍 Search Button -->
                        <button type="button" class="search-toggle" aria-label="<?php esc_attr_e('Open search', 'ruined'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>

                        <div class="header-account">
                            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" title="<?php esc_attr_e('My Account', 'ruined'); ?>">
                                <span class="dashicons dashicons-admin-users"></span>
                            </a>
                        </div>
                        <div class="header-cart">
                            <a href="#" class="cart-contents" title="<?php esc_attr_e('View your shopping cart', 'ruined'); ?>">
                                <span class="cart-contents__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                    <span class="cart-contents-count" data-cart-count="<?php echo esc_attr(WC()->cart->get_cart_contents_count()); ?>">
                                        <?php echo WC()->cart->get_cart_contents_count(); ?>
                                    </span>
                                </span>
                            </a>
                        </div>
                        
                        <?php // Include the off-canvas cart template
                        if (function_exists('wc_get_template')) {
                            wc_get_template('template-parts/woocommerce/mini-cart.php');
                        }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>
<?php get_template_part('template-parts/search/popup'); ?>
