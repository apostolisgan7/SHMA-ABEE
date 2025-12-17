<?php
/**
 * Template Part: Pages Hero
 */


$shop_page_id  = wc_get_page_id( 'shop' );
$is_shop_page  = is_shop();
$is_wc_listing = is_shop() || is_product_taxonomy() || ( is_search() && get_query_var('post_type') === 'product' );

// Έχει ACF το Shop page;
$shop_has_acf =
        ! empty( get_field( 'hero_color', $shop_page_id ) ) ||
        ! empty( get_field( 'video', $shop_page_id ) ) ||
        ! empty( get_field( 'title', $shop_page_id ) ) ||
        ! empty( get_field( 'text', $shop_page_id ) ) ||
        ! empty( get_field( 'button_link', $shop_page_id ) );


// --------------------------------------------------
// 1) SHOP PAGE ΜΕ ACF – ΠΡΟΤΕΡΑΙΟΤΗΤΑ
// --------------------------------------------------
if ( $is_shop_page && $shop_has_acf ) {

    $image_url  = get_the_post_thumbnail_url( $shop_page_id, 'large' );
    $video_url  = get_field( 'video', $shop_page_id );
    $acf_title  = get_field( 'title', $shop_page_id );
    $title      = $acf_title ?: get_the_title( $shop_page_id );
    $text       = get_field( 'text', $shop_page_id );
    $button     = get_field( 'button_link', $shop_page_id );
    $hero_color = get_field( 'hero_color', $shop_page_id );

}

// --------------------------------------------------
// 2) DEFAULT WOO LISTING HERO (shop w/o ACF, taxonomies, product search)
// --------------------------------------------------
elseif ( $is_wc_listing ) {

    // Default hero image (from ACF options)
    $default_hero_image = get_field( 'product_hero_default_image', 'option' );

    if ( $default_hero_image ) {
        $image_url = wp_get_attachment_image_url( $default_hero_image, 'large' );
    } else {
        $image_url = get_template_directory_uri() . '/src/img/default.png';
    }

    // WooCommerce δεν χρησιμοποιεί ACF στα listings
    $video_url  = '';
    $text       = '';
    $button     = null;
    $hero_color = 'dark';

    // Dynamic Title
    if ( is_shop() ) {
        $title = 'Όλα τα προϊόντα';
    } elseif ( is_product_taxonomy() ) {
        $title = 'Αποτελέσματα για “' . single_term_title( '', false ) . '”';
    } elseif ( is_search() && get_query_var('post_type') === 'product' ) {
        $title = 'Αποτελέσματα για “' . get_search_query() . '”';
    } else {
        $title = get_the_archive_title();
    }
}

// --------------------------------------------------
// 3) NORMAL PAGES – ACF per page template
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

if ( empty( $image_url ) ) {

    // default από ACF options πρώτα
    $default_hero_image = get_field( 'product_hero_default_image', 'option' );

    if ( $default_hero_image ) {
        $image_url = wp_get_attachment_image_url( $default_hero_image, 'large' );
    } else {
        // default από το theme
        $image_url = get_template_directory_uri() . '/src/img/default.png';
    }
}
// --------------------------------------------------
// 4) Hero Color + Button Variant
// --------------------------------------------------
$hero_color_class = '';
$button_variant   = 'white';

if ( ! empty( $hero_color ) ) {
    $hero_color_normalized = strtolower( $hero_color );

    if ( $hero_color_normalized === 'dark' ) {
        $button_variant = 'black';
    } elseif ( $hero_color_normalized === 'light' ) {
        $button_variant = 'white';
    }

    $hero_color_class = ' pages-hero--' . sanitize_title( $hero_color );
}


// --------------------------------------------------
// 5) Background Image (fallback)
// --------------------------------------------------
$style = '';
if ( $image_url && empty( $video_url ) ) {
    $style = 'background-image: url(' . esc_url( $image_url ) . ');';
}


// --------------------------------------------------
// 6) VIDEO HANDLING
// --------------------------------------------------
$video_type  = '';
$video_embed = '';

if ( $video_url ) {

    // Self-hosted
    if ( preg_match( '/\.(mp4|webm|ogg)$/i', $video_url ) ) {
        $video_type  = 'self';
        $video_embed = '<video class="hero-video" autoplay muted loop playsinline>
            <source src="' . esc_url( $video_url ) . '" type="video/mp4">
        </video>';
    }

    // YouTube
    elseif ( strpos( $video_url, 'youtu' ) !== false ) {
        preg_match( '/(youtu\.be\/|v=)([^&]+)/', $video_url, $matches );
        $youtube_id = $matches[2] ?? '';
        $video_type = 'youtube';
        $video_embed = '<iframe class="hero-video"
            src="https://www.youtube.com/embed/' . $youtube_id . '?autoplay=1&mute=1&controls=0&loop=1&playlist=' . $youtube_id . '&playsinline=1"
            frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
    }

    // Vimeo
    elseif ( strpos( $video_url, 'vimeo' ) !== false ) {
        preg_match( '/vimeo\.com\/(\d+)/', $video_url, $matches );
        $vimeo_id = $matches[1] ?? '';
        $video_type = 'vimeo';
        $video_embed = '<iframe class="hero-video"
            src="https://player.vimeo.com/video/' . $vimeo_id . '?background=1&autoplay=1&loop=1&muted=1"
            frameborder="0" allowfullscreen></iframe>';
    }
}

?>

<div class="pages-hero section-full-width<?= esc_attr( $hero_color_class ); ?>"
     style="<?= esc_attr( $style ); ?>">

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
                    <div class="pages-hero__title"><?= wp_kses_post( $title ); ?></div>
                <?php endif; ?>
                <?php if ( ! empty( $text ) ) : ?>
                    <p class="pages-hero__text"><?= esc_html( $text ); ?></p>
                <?php endif; ?>
            </div>

            <div class="bottom_content">

                <?php if ( ! empty( $button['url'] ) ) :
                    rv_button_arrow([
                            'text'  => $button['title'] ?? 'Επικοινωνήστε μαζί μας',
                            'url'   => $button['url'],
                            'target'=> $button['target'] ?? '_self',
                            'variant'=> $button_variant,
                            'icon_position'=>'left',
                    ]);
                endif; ?>
            </div>

        </div>
    </div>
</div>
