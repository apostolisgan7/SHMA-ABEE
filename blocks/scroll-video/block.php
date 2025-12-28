<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$subtitle = get_field( 'subtitle' );
$title    = get_field( 'title' );
$image    = get_field( 'image' ); // ACF Image field

// Ασφαλές image URL (array / ID / URL)
$image_url = '';
if ( is_array( $image ) && isset( $image['url'] ) ) {
    $image_url = $image['url'];
} elseif ( is_int( $image ) ) {
    $image_url = wp_get_attachment_image_url( $image, 'full' );
} elseif ( is_string( $image ) ) {
    $image_url = $image;
}

$block_id = 'scroll-video-' . $block['id'];
$classes  = 'rv-scroll-video';
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}
?>

<section class="rv-scroll-video-wrapper section-full-width">
    <div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $classes ); ?> section-full-width">
        <div class="rv-scroll-video__inner">

            <?php if ( $image_url ): ?>
                <div class="rv-scroll-video__media"
                     style="background-image:url('<?php echo esc_url( $image_url ); ?>'); background-size:cover; background-position:center;background-attachment: fixed">
                </div>
            <?php endif; ?>

            <div class="rv-scroll-video__overlay"></div>

            <div class="rv-scroll-video__content container">
                <?php if ( $subtitle ): ?>
                    <div class="rv-scroll-video__subtitle">
                        <?php echo esc_html( $subtitle ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $title ): ?>
                    <div class="rv-scroll-video__title wysiwyg">
                        <?php echo wp_kses_post( $title ); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
