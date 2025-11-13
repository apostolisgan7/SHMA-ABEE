<?php
if ( ! defined('ABSPATH') ) exit;

$subtitle = get_field('subtitle');
$title    = get_field('title');
$video    = get_field('video'); // url (όπως το δήλωσες στο ACF)

$block_id = 'scroll-video-' . $block['id'];
$classes  = 'rv-scroll-video';
if ( ! empty($block['className']) ) $classes .= ' ' . $block['className'];
?>
<section class="rv-scroll-video-wrapper section-full-width">
    <div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($classes); ?> section-full-width">
        <div class="rv-scroll-video__inner">

            <?php if ($video): ?>
                <div class="rv-scroll-video__media">
                    <video
                        class="rv-scroll-video__video"
                        src="<?php echo esc_url($video); ?>"
                        playsinline
                        muted
                        preload="auto"
                    ></video>
                </div>
            <?php endif; ?>

            <div class="rv-scroll-video__overlay"></div>

            <div class="rv-scroll-video__content container">
                <?php if ($subtitle): ?>
                    <div class="rv-scroll-video__subtitle">
                        <?php echo esc_html($subtitle); ?>
                    </div>
                <?php endif; ?>

                <?php if ($title): ?>
                    <div class="rv-scroll-video__title wysiwyg">
                        <?php echo wp_kses_post($title); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
<?php
