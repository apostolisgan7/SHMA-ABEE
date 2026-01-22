<?php
$banner = get_field('contact_bottom_banner', 'option');

if (!$banner) return;

$title        = $banner['title'] ?? '';
$text         = $banner['text'] ?? '';
$button_left  = $banner['button_left'] ?? null;
$button_right = $banner['button_right'] ?? null;

if (!$title && !$text) return;
?>

<section class="contact-bottom-banner">
    <div class="container">
        <div class="banner-inner">

            <?php if ($title) : ?>
                <h3 class="banner-title">
                    <?php echo esc_html($title); ?>
                </h3>
            <?php endif; ?>

            <?php if ($text) : ?>
                <p class="banner-text">
                    <?php echo esc_html($text); ?>
                </p>
            <?php endif; ?>

            <div class="banner-actions">
                <?php if ($button_left) : ?>
                    <a
                        href="<?php echo esc_url($button_left['url']); ?>"
                        target="<?php echo esc_attr($button_left['target'] ?: '_self'); ?>"
                        class="btn btn-white"
                    >
                        <?php echo esc_html($button_left['title']); ?>
                    </a>
                <?php endif; ?>

                <?php if ($button_right) : ?>
                    <a
                        href="<?php echo esc_url($button_right['url']); ?>"
                        target="<?php echo esc_attr($button_right['target'] ?: '_self'); ?>"
                        class="btn btn-outline"
                    >
                        <?php echo esc_html($button_right['title']); ?>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
