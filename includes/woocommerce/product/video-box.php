<?php
/**
 * Video Section
 */

$video_image = get_field('video_image') ?: null;
$video_title = get_field('video_title') ?: '';
$video_text  = get_field('video_text') ?: '';
$video_link  = get_field('video_link') ?: null;

/**
 * If all fields are empty, stop rendering the section
 */
if (!$video_image && !$video_title && !$video_text && !$video_link) {
    return;
}
?>

<div id="video-section" class="video_box_wrapper">
    <div class="video-box">

        <?php if ($video_image):
            $image_url = is_array($video_image) ? $video_image['url'] : $video_image;
            $image_alt = is_array($video_image) ? ($video_image['alt'] ?? '') : '';
            ?>
            <div class="video-box__image">
                <img src="<?php echo esc_url($image_url); ?>"
                     alt="<?php echo esc_attr($image_alt ?: $video_title); ?>"
                     loading="lazy">
            </div>
        <?php endif; ?>

        <?php if ($video_title || $video_text || $video_link): ?>
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

            </div>
        <?php endif; ?>

    </div>
</div>