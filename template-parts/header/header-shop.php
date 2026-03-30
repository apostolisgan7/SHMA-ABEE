<?php
/**
 * The shop header template
 *
 * @package Ruined
 */
?>
<?php
if (function_exists('is_product') && is_product()) {
    $header_color = 'black';
} else {
    $header_color = get_field('header_color') ?: 'white';
}

$header_class = 'header-' . esc_attr($header_color);
?>
<header id="masthead" class="site-header header-shop <?php echo $header_class; ?>">
    <div class="container">
        <div class="header-inner">
            <div class="header-left">
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) :
                        the_custom_logo();
                    else : ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                    <?php endif; ?>
                </div>

                <button class="header-catalog" type="button" data-catalog-toggle>
                    <span><?php _e('Κατάλογος προϊόντων', 'ruined'); ?></span>
                </button>

            </div>

            <div class="header-right">

                <?php if (class_exists('WooCommerce')) : ?>
                    <div class="header-search head_item">
                        <button type="button" class=" flex items-center gap-2 search-toggle header-link"
                                aria-label="<?php esc_attr_e('Open search', 'ruined'); ?>">
                            <svg width="11" height="11" viewBox="0 0 11 11" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.70618 8.70642C6.91545 8.70642 8.70642 6.91545 8.70642 4.70618C8.70642 2.4969 6.91545 0.705933 4.70618 0.705933C2.4969 0.705933 0.705933 2.4969 0.705933 4.70618C0.705933 6.91545 2.4969 8.70642 4.70618 8.70642Z"
                                      stroke="white" stroke-width="1.41185" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M9.7059 9.70644L7.53076 7.53131" stroke="white" stroke-width="1.41185"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                            <?php _e('ΑΝΑΖΗΤΗΣΗ', 'ruined'); ?>

                        </button>
                        <button type="button" class="search-toggle search_mobile"
                                aria-label="<?php esc_attr_e('Open search', 'ruined'); ?>">
                            <svg width="25" height="25" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.77 18.3C9.2807 18.3 7.82485 17.8584 6.58655 17.031C5.34825 16.2036 4.38311 15.0275 3.81318 13.6516C3.24325 12.2757 3.09413 10.7616 3.38468 9.30096C3.67523 7.84029 4.39239 6.49857 5.44548 5.44548C6.49857 4.39239 7.84029 3.67523 9.30096 3.38468C10.7616 3.09413 12.2757 3.24325 13.6516 3.81318C15.0275 4.38311 16.2036 5.34825 17.031 6.58655C17.8584 7.82485 18.3 9.2807 18.3 10.77C18.3 11.7588 18.1052 12.738 17.7268 13.6516C17.3484 14.5652 16.7937 15.3953 16.0945 16.0945C15.3953 16.7937 14.5652 17.3484 13.6516 17.7268C12.738 18.1052 11.7588 18.3 10.77 18.3ZM10.77 4.74999C9.58331 4.74999 8.42327 5.10189 7.43657 5.76118C6.44988 6.42046 5.68084 7.35754 5.22672 8.45389C4.77259 9.55025 4.65377 10.7566 4.88528 11.9205C5.11679 13.0844 5.68824 14.1535 6.52735 14.9926C7.36647 15.8317 8.43556 16.4032 9.59945 16.6347C10.7633 16.8662 11.9697 16.7474 13.0661 16.2933C14.1624 15.8391 15.0995 15.0701 15.7588 14.0834C16.4181 13.0967 16.77 11.9367 16.77 10.75C16.77 9.15869 16.1379 7.63257 15.0126 6.50735C13.8874 5.38213 12.3613 4.74999 10.77 4.74999Z"
                                      fill="currentColor"/>
                                <path d="M20 20.75C19.9015 20.7504 19.8038 20.7312 19.7128 20.6934C19.6218 20.6557 19.5392 20.6001 19.47 20.53L15.34 16.4C15.2075 16.2578 15.1354 16.0697 15.1388 15.8754C15.1422 15.6811 15.221 15.4958 15.3584 15.3583C15.4958 15.2209 15.6812 15.1422 15.8755 15.1388C16.0698 15.1354 16.2578 15.2075 16.4 15.34L20.53 19.47C20.6704 19.6106 20.7493 19.8012 20.7493 20C20.7493 20.1987 20.6704 20.3893 20.53 20.53C20.4608 20.6001 20.3782 20.6557 20.2872 20.6934C20.1962 20.7312 20.0985 20.7504 20 20.75Z"
                                      fill="currentColor"/>
                            </svg>
                        </button>
                    </div>

                    <div class="header-account head_item">
                        <?php if (is_user_logged_in()) : ?>

                            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                               class="header-link">
                                <?php _e('ΛΟΓΑΡΙΑΣΜΟΣ', 'ruined'); ?>
                            </a>

                        <?php else : ?>

                            <a href="#"
                               class="header-link js-auth-modal-trigger">
                                <?php _e('ΛΟΓΑΡΙΑΣΜΟΣ', 'ruined'); ?>
                            </a>

                        <?php endif; ?>
                    </div>


                    <div class="header-cart head_item">
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="header-link has-badge cart-contents">
                            <span class="offer_text"><?php _e('ΠΡΟΣΦΟΡΑ', 'ruined'); ?></span>
                            <span class="offer_icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
<path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z"
      stroke="#fff" stroke-width="1.5"/>
<path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z"
      stroke="#fff" stroke-width="1.5"/>
<path d="M2 3L2.26121 3.09184C3.5628 3.54945 4.2136 3.77826 4.58584 4.32298C4.95808 4.86771 4.95808 5.59126 4.95808 7.03836V9.76C4.95808 12.7016 5.02132 13.6723 5.88772 14.5862C6.75412 15.5 8.14857 15.5 10.9375 15.5H12M16.2404 15.5C17.8014 15.5 18.5819 15.5 19.1336 15.0504C19.6853 14.6008 19.8429 13.8364 20.158 12.3075L20.6578 9.88275C21.0049 8.14369 21.1784 7.27417 20.7345 6.69708C20.2906 6.12 18.7738 6.12 17.0888 6.12H11.0235M4.95808 6.12H7"
      stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
</svg></span>
                            <?php if (WC()->cart->get_cart_contents_count() > 0): ?>
                                <div class="count-wrapper">
                                    <span class="count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="header-lang head_item desktop-only">
                    <a href="#" class="is-active">EL</a>
                    <span>/</span>
                    <a href="#">EN</a>
                </div>
                <div class="menu_btn">
                    <button class="desktop-menu-button" type="button" aria-label="Open menu">
                    <span><span class="line line--top"></span>
                    <span class="line line--bottom"></span>
                    </button>
                    <span class="menu-text">MENU</span>
                </div>
                <button href="#menu" class="mobile-menu-button" aria-label="Open menu">
                    <span class="line line--top"></span>
                    <span class="line line--bottom"></span>
                </button>

            </div>

        </div>
    </div>
</header>

<?php get_template_part('template-parts/menus/main-mega-menu'); ?>
<?php get_template_part('template-parts/menus/catalog-menu'); ?>
<?php get_template_part('template-parts/search/popup'); ?>
<?php get_template_part('template-parts/woocommerce/auth-modal'); ?>
<?php get_template_part('template-parts/woocommerce/mini-cart'); ?>
