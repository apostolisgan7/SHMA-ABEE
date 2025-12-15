<?php
/**
 * ACF Block: Contact Infos
 * Fields:
 * - image (image array)
 * - image_title (text)
 * - info_boxes (repeater)
 *   - box_subtitle (text)
 *   - box_title (text)
 *   - box_link (link array)
 */

$image = get_field('image');
$image_title = get_field('image_title');
$boxes = get_field('info_boxes');

// Block attributes (optional, but nice to have)
$block_id = !empty($block['anchor']) ? $block['anchor'] : 'contact-infos-' . ($block['id'] ?? uniqid());
$classes = 'block-contact-infos section-full-width';
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['align'])) $classes .= ' align' . $block['align'];
?>

<section id="<?= esc_attr($block_id); ?>" class="<?= esc_attr($classes); ?>">
    <div class="container">
        <div class="block-contact-infos__inner">

            <!-- LEFT: Image -->
            <div class="block-contact-infos__media">
                <?php if (!empty($image['url'])): ?>
                    <img
                            class="block-contact-infos__img"
                            src="<?= esc_url($image['url']); ?>"
                            alt="<?= esc_attr($image['alt'] ?? $image_title ?? ''); ?>"
                            loading="lazy"
                    >
                <?php endif; ?>

                <?php if (!empty($image_title)): ?>
                    <div class="block-contact-infos__image-title">
                        <?= nl2br(esc_html($image_title)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Boxes -->
            <div class="block-contact-infos__boxes">
                <?php if (!empty($boxes) && is_array($boxes)): ?>
                <?php foreach ($boxes

                as $i => $box): ?>
                <?php
                $subtitle = $box['box_subtitle'] ?? '';
                $title = $box['box_title'] ?? '';
                $link = $box['box_link'] ?? null;

                $url = is_array($link) ? ($link['url'] ?? '') : '';
                $target = is_array($link) ? ($link['target'] ?? '_self') : '_self';
                $ltitle = is_array($link) ? ($link['title'] ?? '') : '';

                $is_active = ($i === 0); // πρώτο box active όπως στο design
                $box_class = 'block-contact-infos__box';
                ?>

                <?php if (!empty($url)): ?>
                <a class="<?= esc_attr($box_class . ' is-link'); ?>" href="<?= esc_url($url); ?>"
                   target="<?= esc_attr($target); ?>">

                    <?php else: ?>
                    <div class="<?= esc_attr($box_class); ?>">
                        <?php endif; ?>

                        <div class="block-contact-infos__box-content">
                            <div class="up_info">
                                <?php if (!empty($subtitle)): ?>
                                    <div class="block-contact-infos__box-subtitle">
                                        <?= esc_html($subtitle); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($title)): ?>
                                    <div class="block-contact-infos__box-title">
                                        <?= esc_html($title); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($ltitle)): ?>
                                <div class="block-contact-infos__box-linktext">
                                    <?= esc_html($ltitle); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="block-contact-infos__box-action" aria-hidden="true">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" role="img" aria-hidden="true">
                                <rect x="1" y="1" width="30" height="30" rx="6"></rect>
                                <path d="M13 9l7 7-7 7" stroke-width="2" stroke-linecap="round"
                                      stroke-linejoin="round"></path>
                            </svg>
                        </div>

                        <?php if (!empty($url)): ?>
                </a>
                <?php else: ?>
            </div>
            <?php endif; ?>

            <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
    </div>
</section>
