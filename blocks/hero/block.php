<?php
// ACF fields
$image = get_field('image');
$title = get_field('title');
$sub_texts = get_field('sub_texts');
$hero_boxes = get_field('hero_box');
// background style
$style = '';
if (!empty($image['url'])) {
    $style = 'background-image: url(' . esc_url($image['url']) . ');';
}

// button data
$button = $sub_texts['button_link'] ?? null;
?>

<div class="home-hero section-full-width <?= esc_attr($block['className'] ?? ''); ?> <?= !empty($block['align']) ? 'align' . $block['align'] : ''; ?>"
     style="<?= esc_attr($style); ?>">
    <div class="home-hero__overlay"></div>

    <div class="home-hero__inner container">
        <div class="home-hero__content">
            <?php if (!empty($title)): ?>
                <div class="home-hero__title">
                    <?= wp_kses_post($title); ?>
                </div>
            <?php endif; ?>

            <div class="bottom_content">
                <?php if (!empty($sub_texts['sub_title'])): ?>
                    <div class="home-hero__subtitle">
                        <?= esc_html($sub_texts['sub_title']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($sub_texts['subtext'])): ?>
                    <p class="home-hero__text">
                        <?= esc_html($sub_texts['subtext']); ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($button)): ?>
                    <?php
                    rv_button_arrow([
                            'text' => $button['title'] ?? 'Επικοινωνήστε μαζί μας',
                            'url' => $button['url'] ?? '#',
                            'target' => $button['target'] ?? '_self',
                            'variant' => 'white',
                            'icon_position' => 'left',
                    ]);
                    ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($hero_boxes): ?>
            <div class="home-hero__boxes">
                <?php foreach ($hero_boxes as $box):
                    $box_image = $box['image'] ?? null;
                    $box_title = $box['title'] ?? '';
                    $box_link = $box['link'] ?? null;
                    ?>
                    <article class="hero-box">
                        <a class="hero__link" href="<?= esc_url($box_link['url']); ?>"
                           target="<?= esc_attr($box_link['target'] ?? '_self'); ?>"></a>
                        <?php if ($box_image): ?>
                            <div class="hero-box__image">
                                <img src="<?= esc_url($box_image['url']); ?>" alt="<?= esc_attr($box_title); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="hero-box__content">
                            <?php if ($box_title): ?>
                                <h3 class="hero-box__title"><?= esc_html($box_title); ?></h3>
                            <?php endif; ?>

                            <?php if ($box_link): ?>
                                <div class="hero-box__link" >
                                    <?= esc_html($box_link['title'] ?: 'Διάβασε περισσότερα'); ?>
                                    <span class="hero-box__icon" aria-hidden="true"><svg width="7" height="9"
                                                                                         viewBox="0 0 7 9" fill="none"
                                                                                         xmlns="http://www.w3.org/2000/svg">
<path d="M0.977505 0.977581L5.12476 4.48757L0.977504 7.99756" stroke="white" stroke-width="1.95503"
      stroke-linecap="round" stroke-linejoin="round"/>
</svg></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>