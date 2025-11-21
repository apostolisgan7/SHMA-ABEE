<?php
$title = get_field('title');
$service_boxes = get_field('service_boxes');
?>

<section class="extra-services-boxes">
    <div class="extra-services-boxes__inner container">

        <!-- BLOCK TITLE -->
        <?php if ($title): ?>
            <h2 class="extra-services-boxes__title">
                <?= esc_html($title); ?>
            </h2>
        <?php endif; ?>

        <!-- SERVICE BOXES -->
        <div class="extra-services-boxes__list">
            <?php if ($service_boxes): ?>
                <?php foreach ($service_boxes as $box): ?>

                    <?php
                    $image     = $box['image'] ?? null;
                    $box_title = $box['title'] ?? '';
                    $text      = $box['text'] ?? '';
                    $link      = $box['link'] ?? null;
                    $reversed  = $box['reversed'] ?? false;

                    // Add reversed class
                    $item_class = $reversed ? ' service-box--reversed' : '';
                    ?>

                    <article class="service-box<?= $item_class; ?>">

                        <!-- IMAGE -->
                        <?php if ($image): ?>
                            <div class="service-box__image">
                                <img src="<?= esc_url($image['url']); ?>"
                                     alt="<?= esc_attr($box_title); ?>"
                                     loading="lazy">
                            </div>
                        <?php endif; ?>

                        <!-- CONTENT -->
                        <div class="service-box__content">

                            <?php if ($box_title): ?>
                                <h3 class="service-box__title">
                                    <?= esc_html($box_title); ?>
                                </h3>
                            <?php endif; ?>

                            <?php if ($text): ?>
                                <p class="service-box__text">
                                    <?= esc_html($text); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($link): ?>
                                <div class="service-box__button">
                                    <?php
                                    rv_button_arrow([
                                        'text'          => $link['title'] ?? 'Μάθε περισσότερα',
                                        'url'           => $link['url']   ?? '#',
                                        'target'        => $link['target'] ?? '_self',
                                        'variant'       => 'black',
                                        'icon_position' => 'left',
                                    ]);
                                    ?>
                                </div>
                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
