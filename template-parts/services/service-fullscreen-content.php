<?php
if ( ! defined('ABSPATH') ) exit;

// ACF fields
$subtitle = get_field('full_subtitle');
$title    = get_field('full_title');
$image    = get_field('full_image');          // array
$box      = get_field('full_service_box');    // group

$block_id = 'fullscreen-content-' . $block['id'];
$classes  = 'rv-fullscreen-content';
if ( ! empty( $block['className'] ) ) {
    $classes .= ' ' . $block['className'];
}

$image_url = ( $image && ! empty($image['url']) ) ? $image['url'] : '';

// Service Box subfields
$box_image = $box['image']      ?? null;
$box_title = $box['box_title']  ?? '';
$box_text  = $box['box_text']   ?? '';
$box_link  = $box['box_link']   ?? null;
?>
<section class="rv-fullscreen-content-wrapper section-full-width">
    <div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($classes); ?> section-full-width">
        <?php if ( $image_url ): ?>
            <div class="rv-fullscreen-content__media"
                 style="background-image: url('<?php echo esc_url($image_url); ?>');">
            </div>
        <?php endif; ?>

        <div class="rv-fullscreen-content__overlay"></div>
        <div class="rv-fullscreen-content__inner">

            <div class="rv-fullscreen-content__content container">
                <?php if ($subtitle): ?>
                    <div class="rv-fullscreen-content__subtitle">
                        <?php echo esc_html($subtitle); ?>
                    </div>
                <?php endif; ?>

                <?php if ($title): ?>
                    <div class="rv-fullscreen-content__title wysiwyg">
                        <?php echo wp_kses_post($title); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( $box ): ?>
            <div class="rv-fullscreen-content__service-box-wrapper container">
                <article class="service-box">

                    <?php if ( $box_image ): ?>
                        <div class="service-box__image">
                            <img src="<?php echo esc_url($box_image['url']); ?>"
                                 alt="<?php echo esc_attr($box_title); ?>"
                                 loading="lazy">
                        </div>
                    <?php endif; ?>

                    <div class="service-box__content">

                        <?php if ( $box_title ): ?>
                            <h3 class="service-box__title">
                                <?php echo esc_html($box_title); ?>
                            </h3>
                        <?php endif; ?>

                        <?php if ( $box_text ): ?>
                            <p class="service-box__text">
                                <?php echo esc_html($box_text); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $box_link ): ?>
                            <div class="service-box__button">
                                <?php
                                rv_button_arrow([
                                    'text'          => $box_link['title']  ?? 'Μάθε περισσότερα',
                                    'url'           => $box_link['url']    ?? '#',
                                    'target'        => $box_link['target'] ?? '_self',
                                    'variant'       => 'black',
                                    'icon_position' => 'left',
                                ]);
                                ?>
                            </div>
                        <?php endif; ?>

                    </div>

                </article>
            </div>
        <?php endif; ?>

    </div>
</section>
