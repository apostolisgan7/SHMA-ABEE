<?php
/**
 * Video Section - Mapping ACF fields to the video Box template
 * Fields: video_image (array), video_title (text), video_text (wysiwyg), video_link (url)
 */

$video_image = get_field('video_image') ?: null;
$video_title = get_field('video_title') ?: '';
$video_text = get_field('video_text') ?: '';
$video_link = get_field('video_link') ?: null;
?>

<div id="video-section" class="video_box_wrapper">
    <div class="video-box">

        <?php if ($video_image): ?>
            <div class="video-box__image">
                <img src="<?php echo esc_url($video_image['url']); ?>"
                     alt="<?php echo esc_attr($video_image['alt'] ?: $video_title); ?>"
                     loading="lazy">
            </div>
        <?php endif; ?>

        <div class="video-box__content">

            <?php if ($video_title): ?>
                <h3 class="video-box__title">
                    <?php echo esc_html($video_title); ?>
                </h3>
            <?php endif; ?>

            <?php if ($video_text): ?>
                <div class="video-box__text">
                    <?php echo wp_kses_post($video_text); ?>
                </div>
            <?php endif; ?>

            <?php if ($video_link): ?>
                <?php if ($video_link): ?>
                    <div class="video-box__button">
                        <a href="<?php echo esc_url($video_link); ?>"
                           class="button-arrow button-arrow--black"
                           data-fancybox="video-gallery"
                           data-caption="<?php echo esc_attr($video_title); ?>">

                    <span class="button-arrow__icon">
                        <span class="button-arrow__arrow button-arrow__arrow--front"></span>
                        <span class="button-arrow__arrow button-arrow__arrow--back"></span>
                        <span class="button-arrow__fill"></span>
                    </span>

                            <span class="button-arrow__text">Δείτε το Video</span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
