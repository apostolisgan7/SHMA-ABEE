<?php
$title          = get_field('title');
$carousel_texts = get_field('carousel_texts');
$image          = get_field('image');

$image_url = '';

if (!empty($image) && is_array($image) && isset($image['url'])) {
    $image_url = $image['url'];
}
?>

<section class="rv-map-infos section-full-width">
    <div class="rv-map-infos__grid">

        <!-- LEFT -->
        <div class="rv-map-infos__left">

            <?php if (!empty($title)) : ?>
                <div class="rv-map-infos__heading">
                    <?php echo wp_kses_post($title); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($carousel_texts)) : ?>
                <div class="rv-map-infos__carousel swiper">
                    <div class="swiper-wrapper">

                        <?php foreach ($carousel_texts as $row) : ?>
                            <div class="swiper-slide">
                                <div class="rv-map-infos__info">

                                    <?php if (!empty($row['title'])) : ?>
                                        <h4><?php echo esc_html($row['title']); ?></h4>
                                    <?php endif; ?>

                                    <?php if (!empty($row['text'])) : ?>
                                        <p><?php echo esc_html($row['text']); ?></p>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <div class="rv-map-infos__pagination"></div>
                </div>
            <?php endif; ?>

        </div>

        <!-- RIGHT -->
        <?php if ($image_url) : ?>
            <div class="rv-map-infos__right">
                <div class="rv-map-infos__map">
                    <?php
                    echo wp_get_attachment_image(
                        $image['ID'],
                        'full',
                        false,
                        [
                            'class' => 'rv-map-infos__img',
                            'loading' => 'lazy'
                        ]
                    );
                    ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>