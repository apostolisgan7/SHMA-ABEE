<?php
/**
 * ACF Block: Contact Location
 * Fields:
 * - title (text)
 * - text (textarea)
 * - location_info (group)
 *    - location_title (text)
 *    - locations (wysiwyg)
 *    - hours (text)
 * - location_icon (image array)
 * - image (image array)
 */

$title         = get_field('title');
$text          = get_field('text');
$location_info = get_field('location_info') ?: [];
$location_icon = get_field('location_icon');
$image         = get_field('image');

// Group fields
$loc_title = $location_info['location_title'] ?? '';
$locations = $location_info['locations'] ?? '';
$hours     = $location_info['hours'] ?? '';

// Block attributes (nice-to-have)
$block_id = !empty($block['anchor']) ? $block['anchor'] : 'contact-location-' . ($block['id'] ?? uniqid());
$classes  = 'block-contact-location section-full-width';
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['align']))     $classes .= ' align' . $block['align'];
?>

<section id="<?= esc_attr($block_id); ?>" class="<?= esc_attr($classes); ?>">
    <div class="container">
        <div class="block-contact-location__card">
            <div class="block-contact-location__inner">

                <!-- LEFT -->
                <div class="block-contact-location__left">

                    <div class="block-contact-location__intro">
                        <?php if (!empty($title)): ?>
                            <h2 class="block-contact-location__title"><?= esc_html($title); ?></h2>
                        <?php endif; ?>

                        <?php if (!empty($text)): ?>
                            <p class="block-contact-location__text"><?= esc_html($text); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="block-contact-location__info">
                        <div class="block-contact-location__info-content">

                            <?php if (!empty($loc_title)): ?>
                                <h3 class="block-contact-location__location-title"><?= esc_html($loc_title); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($locations)): ?>
                                <div class="block-contact-location__locations">
                                    <?= wp_kses_post($locations); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($hours)): ?>
                                <div class="block-contact-location__hours">
                                    <?= esc_html($hours); ?>
                                </div>
                            <?php endif; ?>

                        </div>

                        <?php if (!empty($location_icon['url'])): ?>
                            <div class="block-contact-location__icon" aria-hidden="true">
                                <img
                                    src="<?= esc_url($location_icon['url']); ?>"
                                    alt="<?= esc_attr($location_icon['alt'] ?? ''); ?>"
                                    loading="lazy"
                                >
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="block-contact-location__media">
                    <?php if (!empty($image['url'])): ?>
                        <img
                            class="block-contact-location__image"
                            src="<?= esc_url($image['url']); ?>"
                            alt="<?= esc_attr($image['alt'] ?? $title ?? ''); ?>"
                            loading="lazy"
                        >
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>
