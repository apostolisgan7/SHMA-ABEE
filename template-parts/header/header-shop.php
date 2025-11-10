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
            <div class="header-left">
                <div class="site-branding">
                    <?php
                    if ( has_custom_logo() ) :
                        the_custom_logo();
                    else : ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                                <?php bloginfo( 'name' ); ?>
                            </a>
                        </h1>
                    <?php endif; ?>
                </div>

                <button class="header-catalog" type="button" data-catalog-toggle>
                    <span><?php _e( 'Κατάλογος προϊόντων', 'ruined' ); ?></span>
                </button>

            </div>

            <div class="header-right">

                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                    <!-- search -->
                    <button type="button" class="search-toggle header-link" aria-label="<?php esc_attr_e( 'Open search', 'ruined' ); ?>">
                        <?php _e( 'ΑΝΑΖΗΤΗΣΗ', 'ruined' ); ?>
                    </button>

                    <!-- account -->
                    <div class="header-account">
                        <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="header-link">
                            <?php _e( 'ΛΟΓΑΡΙΑΣΜΟΣ', 'ruined' ); ?>
                        </a>
                    </div>

                    <div class="header-cart">
                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-link has-badge">
                            <?php _e( 'ΠΡΟΣΦΟΡΑ', 'ruined' ); ?>
<!--                            --><?php //if ( WC()->cart ) : ?>
<!--                                <span class="cart-contents-count">-->
<!--                                    --><?php //echo WC()->cart->get_cart_contents_count(); ?>
<!--                                </span>-->
<!--                            --><?php //endif; ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="header-lang desktop-only">
                    <a href="#" class="is-active">EL</a>
                    <span>/</span>
                    <a href="#">EN</a>
                </div>

                <button class="mobile-menu-button" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'ruined' ); ?>">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

        </div>
    </div>
</header>

<?php get_template_part( 'template-parts/menus/catalog-menu' ); ?>
<?php get_template_part( 'template-parts/search/popup' ); ?>
