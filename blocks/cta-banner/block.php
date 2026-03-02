<?php
$banner_image = get_field('banner_image');
$banner_title = get_field('banner_title');
$button_text = get_field('button_text');
$button_left = get_field('button_left_');
$button_right = get_field('button_right');

$bg = $banner_image ? 'style="background-image:url(' . esc_url($banner_image['url']) . ')"' : '';
?>

<section class="service-banner">
    <div class="service-banner__bg" <?= $bg ?>></div>
    <div class="service-banner__overlay"></div>

    <div class="service-banner__inner container">

        <!-- LEFT -->
        <div class="service-banner__left">
            <?php if ($banner_title): ?>
                <h2 class="service-banner__title"><?= esc_html($banner_title) ?></h2>
            <?php endif; ?>
        </div>

        <!-- RIGHT -->
        <div class="service-banner__right">

            <?php if ($button_text): ?>
                <p class="service-banner__desc"><?= esc_html($button_text) ?></p>
            <?php endif; ?>

            <div class="service-banner__buttons">
                <?php if ($button_left): ?>
                    <a class="service-banner__button btn_left" target="_self" href="<?= $button_left['url'] ?>">
                        <?= $button_left['title'] ?>
                    </a>
                <?php endif; ?>

                <?php if ($button_right): ?>
                    <a class="service-banner__button btn_right" target="_self" href="<?= $button_right['url'] ?>">
                        <?= $button_right['title'] ?>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
