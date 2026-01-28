<?php
/**
 * Template Part: Pages Hero
 */

$shop_page_id = wc_get_page_id( 'shop' );

// FLAGS
$is_product_search = is_search() && get_query_var( 'post_type' ) === 'product';
$is_shop_page      = is_shop();
$is_wc_listing     = is_shop() || is_product_taxonomy();

// ACF SHOP PAGE CHECK
$shop_has_acf =
        ! empty( get_field( 'hero_color', $shop_page_id ) ) ||
        ! empty( get_field( 'video', $shop_page_id ) ) ||
        ! empty( get_field( 'title', $shop_page_id ) ) ||
        ! empty( get_field( 'text', $shop_page_id ) ) ||
        ! empty( get_field( 'button_link', $shop_page_id ) );


// --------------------------------------------------
// 0) PRODUCT SEARCH (ACF OPTIONS GROUP)
// --------------------------------------------------
if ( $is_product_search ) {

    $search_settings = get_field( 'search_page', 'option' ) ?: [];

    $default_hero_image = get_field( 'product_hero_default_image', 'option' );
    $image_url = $default_hero_image
            ? wp_get_attachment_image_url( $default_hero_image, 'large' )
            : get_template_directory_uri() . '/src/img/default.png';

    $search_term = get_search_query();
    if ( empty( $search_term ) && ! empty( $_GET['s'] ) ) {
        $search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );
    }

    $title = $search_term
            ? 'Αποτελέσματα για “' . $search_term . '”'
            : 'Αποτελέσματα αναζήτησης';

    $hero_color = $search_settings['product_search_hero_color'] ?? 'dark';
    $text       = $search_settings['text'] ?? '';
    $button     = $search_settings['button_link'] ?? null;
    $video_url  = '';
}


// --------------------------------------------------
// 1) SHOP PAGE (ACF PER PAGE)
// --------------------------------------------------
elseif ( $is_shop_page && $shop_has_acf ) {

    $image_url  = get_the_post_thumbnail_url( $shop_page_id, 'large' );
    $video_url  = get_field( 'video', $shop_page_id );
    $acf_title  = get_field( 'title', $shop_page_id );

    $title      = $acf_title ?: get_the_title( $shop_page_id );
    $text       = get_field( 'text', $shop_page_id );
    $button     = get_field( 'button_link', $shop_page_id );
    $hero_color = get_field( 'hero_color', $shop_page_id );
}


// --------------------------------------------------
// 2) PRODUCT TAXONOMY (ACF OPTIONS GROUP)
// --------------------------------------------------
elseif ( is_product_taxonomy() ) {

    $taxonomy_settings = get_field( 'taxonomy_page', 'option' ) ?: [];

    $default_hero_image = get_field( 'product_hero_default_image', 'option' );
    $image_url = $default_hero_image
            ? wp_get_attachment_image_url( $default_hero_image, 'large' )
            : get_template_directory_uri() . '/src/img/default.png';

    $title = 'Αποτελέσματα για “' . single_term_title( '', false ) . '”';

    $hero_color = $taxonomy_settings['product_taxonomy_hero_color'] ?? 'dark';
    $text       = $taxonomy_settings['text'] ?? '';
    $button     = $taxonomy_settings['button_link'] ?? null;
    $video_url  = '';
}


// --------------------------------------------------
// 3) SHOP χωρίς ACF
// --------------------------------------------------
elseif ( $is_wc_listing ) {

    $default_hero_image = get_field( 'product_hero_default_image', 'option' );
    $image_url = $default_hero_image
            ? wp_get_attachment_image_url( $default_hero_image, 'large' )
            : get_template_directory_uri() . '/src/img/default.png';

    $title      = 'Όλα τα προϊόντα';
    $hero_color = 'dark';
    $text       = '';
    $button     = null;
    $video_url  = '';
}


// --------------------------------------------------
// 4) NORMAL PAGES
// --------------------------------------------------
else {

    $image_url  = get_the_post_thumbnail_url( get_the_ID(), 'large' );
    $video_url  = get_field( 'video' );
    $acf_title  = get_field( 'title' );

    $title      = $acf_title ?: get_the_title();
    $text       = get_field( 'text' );
    $button     = get_field( 'button_link' );
    $hero_color = get_field( 'hero_color' );
}


// --------------------------------------------------
// FALLBACK IMAGE
// --------------------------------------------------
if ( empty( $image_url ) ) {
    $image_url = get_template_directory_uri() . '/src/img/default.png';
}


// --------------------------------------------------
// HERO COLOR + BUTTON VARIANT
// --------------------------------------------------
$hero_color_class = '';
$button_variant   = 'white';

if ( ! empty( $hero_color ) ) {
    $hero_color_class = ' pages-hero--' . sanitize_title( $hero_color );
    $button_variant  = strtolower( $hero_color ) === 'dark' ? 'black' : 'white';
}


// --------------------------------------------------
// BACKGROUND STYLE
// --------------------------------------------------
$style = '';
if ( $image_url && empty( $video_url ) ) {
    $style = 'background-image: url(' . esc_url( $image_url ) . ');';
}


// --------------------------------------------------
// VIDEO HANDLING
// --------------------------------------------------
$video_type  = '';
$video_embed = '';

if ( $video_url ) {

    if ( preg_match( '/\.(mp4|webm|ogg)$/i', $video_url ) ) {
        $video_type  = 'self';
        $video_embed = '<video class="hero-video" autoplay muted loop playsinline>
            <source src="' . esc_url( $video_url ) . '" type="video/mp4">
        </video>';
    }
}
?>

<div class="pages-hero section-full-width<?= esc_attr( $hero_color_class ); ?>">

    <?php if ( ! empty( $image_url ) ) : ?>
        <!-- ✅ Real LCP Image Fix -->
        <img
                class="pages-hero__bg"
                src="<?= esc_url( $image_url ); ?>"
                alt=""
                decoding="async"
                loading="eager"
                fetchpriority="high"
        >
    <?php endif; ?>

    <?php if ( $video_url && $video_type ) : ?>
        <div class="pages-hero__video-wrapper">
            <?= $video_embed; ?>
        </div>
    <?php endif; ?>

    <div class="pages-hero__overlay"></div>

    <div class="pages-hero__inner container">
        <div class="pages-hero__content">

            <div class="breadcumbs_title">
                <?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) : ?>
                    <div class="rv-breadcrumbs"><?php rank_math_the_breadcrumbs(); ?></div>
                <?php endif; ?>

                <?php if ( ! empty( $title ) ) : ?>
                    <h1 class="pages-hero__title"><?= wp_kses_post( $title ); ?></h1>
                <?php endif; ?>

                <?php if ( ! empty( $text ) ) : ?>
                    <p class="pages-hero__text"><?= esc_html( $text ); ?></p>
                <?php endif; ?>
            </div>

            <div class="bottom_content">
                <?php if ( ! empty( $button['url'] ) ) :
                    rv_button_arrow([
                            'text'          => $button['title'] ?? 'Επικοινωνήστε μαζί μας',
                            'url'           => $button['url'],
                            'target'        => $button['target'] ?? '_self',
                            'variant'       => $button_variant,
                            'icon_position' => 'left',
                    ]);
                endif; ?>
            </div>

        </div>
    </div>
</div>

