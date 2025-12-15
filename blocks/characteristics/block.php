<?php
$char_title = get_field('char_title');
$link = get_field('link');
$items = get_field('characteristics');
?>

<section class="block-service-characteristics  section-full-width">
    <div class="container">
        <?php if ($char_title): ?>
            <div class="block-service-characteristics__title">
                <?= wp_kses_post($char_title); ?>
            </div>
        <?php endif; ?>
        <div class="block-service-characteristics__inner">
            <div class="block-service-characteristics__left">

                <?php if ($link): ?>
                    <div class="block-service-characteristics__button">
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

            <div class="block-service-characteristics__right">
                <?php if ($items): ?>
                    <ul class="block-service-characteristics__list">
                        <?php foreach ($items as $item): ?>
                            <?php
                            $item_link = !empty($item['link']) ? $item['link'] : null;
                            $url = $item_link['url'] ?? '';
                            $target = $item_link['target'] ?? '_self';
                            ?>

                            <li class="block-service-characteristics__item"
                                    <?php if ($url): ?>
                                        role="link"
                                        tabindex="0"
                                        onclick="window.open('<?= esc_url($url); ?>','<?= esc_attr($target); ?>');"
                                        onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.open('<?= esc_url($url); ?>','<?= esc_attr($target); ?>'); }"
                                    <?php endif; ?>
                            >
                                <?php if (!empty($item['icon'])): ?>
                                    <div class="block-service-characteristics__icon">
                                        <img src="<?= esc_url($item['icon']['url']); ?>"
                                             alt="<?= esc_attr($item['title']); ?>">
                                    </div>
                                <?php endif; ?>

                                <span class="block-service-characteristics__item-title">
            <?= esc_html($item['title']); ?>
        </span>

                                <?php if ($url): ?>
                                    <div class="block-service-characteristics__button">
                                        <a href="<?= esc_url($url); ?>" target="<?= esc_attr($target); ?>">
                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" role="img"
                                                 aria-hidden="true">
                                                <rect x="1" y="1" width="30" height="30" rx="6"></rect>
                                                <path d="M13 9l7 7-7 7" stroke-width="2" stroke-linecap="round"
                                                      stroke-linejoin="round"></path>
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>

</section>
