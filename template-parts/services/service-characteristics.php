<?php
$char_title = get_field('char_title');
$link = get_field('link');
$items = get_field('characteristics');
?>

<section class="service-characteristics section-full-width">
    <div class="container">
        <?php if ($char_title): ?>
            <div class="service-characteristics__title">
                <?= wp_kses_post($char_title); ?>
            </div>
        <?php endif; ?>
        <div class="service-characteristics__inner">
            <div class="service-characteristics__left">

                <?php if ($link): ?>
                    <div class="service-characteristics__button">
                        <?php
                        rv_button_arrow([
                                'text' => $link['title'] ?? '',
                                'url' => $link['url'] ?? '#',
                                'target' => $link['target'] ?? '_self',
                                'variant' => 'black   ',
                                'icon_position' => 'left',
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="service-characteristics__right">
                <?php if ($items): ?>
                    <ul class="service-characteristics__list">
                        <?php foreach ($items as $item): ?>
                            <li class="service-characteristics__item">
                                <?php if (!empty($item['icon'])): ?>
                                    <div class="service-characteristics__icon">
                                        <img src="<?= esc_url($item['icon']['url']); ?>"
                                             alt="<?= esc_attr($item['title']); ?>">
                                    </div>
                                <?php endif; ?>

                                <span class="service-characteristics__item-title">
                                <?= esc_html($item['title']); ?>
                            </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <div class="service-characteristics__bg"></div>

</section>
