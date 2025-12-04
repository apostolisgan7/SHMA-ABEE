<?php
/**
 * The shop header template
 *
 * @package Ruined
 */
?>
<?php
$header_color = get_field('header_color') ?: 'white';
$header_class = 'header-' . esc_attr($header_color);
?>
<header id="masthead" class="site-header header-shop <?php echo $header_class; ?>">
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
                    <button type="button" class=" flex items-center gap-2 search-toggle header-link head_item " aria-label="<?php esc_attr_e( 'Open search', 'ruined' ); ?>">
                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.70618 8.70642C6.91545 8.70642 8.70642 6.91545 8.70642 4.70618C8.70642 2.4969 6.91545 0.705933 4.70618 0.705933C2.4969 0.705933 0.705933 2.4969 0.705933 4.70618C0.705933 6.91545 2.4969 8.70642 4.70618 8.70642Z" stroke="white" stroke-width="1.41185" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.7059 9.70644L7.53076 7.53131" stroke="white" stroke-width="1.41185" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <?php _e( 'ΑΝΑΖΗΤΗΣΗ', 'ruined' ); ?>

                    </button>


                    <div class="header-account head_item">
                        <a href="#"
                           class="header-link js-auth-modal-trigger">
                            <?php _e( 'ΛΟΓΑΡΙΑΣΜΟΣ', 'ruined' ); ?>
                        </a>
                    </div>


                    <div class="header-cart head_item">
                        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-link has-badge">
                            <?php _e( 'ΠΡΟΣΦΟΡΑ', 'ruined' ); ?>

                        </a>
                    </div>
                <?php endif; ?>

                <div class="header-lang head_item desktop-only">
                    <a href="#" class="is-active">EL</a>
                    <span>/</span>
                    <a href="#">EN</a>
                </div>
                <button class="desktop-menu-button" type="button" aria-label="Open menu">
                    <span class="line line--top"></span>
                    <span class="line line--bottom"></span>
                </button>
                <a href="#menu" class="mobile-menu-button" aria-label="Open menu">
                    <span class="line line--top"></span>
                    <span class="line line--bottom"></span>
                </a>

            </div>

        </div>
    </div>
</header>

<?php get_template_part( 'template-parts/menus/main-mega-menu' ); ?>
<?php get_template_part( 'template-parts/menus/catalog-menu' ); ?>
<?php get_template_part( 'template-parts/search/popup' ); ?>
<?php get_template_part( 'template-parts/woocommerce/auth-modal' ); ?>
