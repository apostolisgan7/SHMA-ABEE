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
    <div class="container smaller-container">
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
                        <?php if (!empty($location_icon['url'])): ?>
                            <div class="block-contact-location__icon" aria-hidden="true">
                                <img
                                        src="<?= esc_url($location_icon['url']); ?>"
                                        alt="<?= esc_attr($location_icon['alt'] ?? ''); ?>"
                                        loading="lazy"
                                >
                            </div>
                        <?php endif; ?>
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
                    </div>

                    <div class="block-contact-location__button">
                        <?php
                        rv_button_arrow([
                                'text'          => $link['title'] ?? 'Βρείτε μας στο χάρτη',
                                'url'           => $link['url']   ?? 'https://maps.app.goo.gl/LYUK4sx7iJDLya9NA',
                                'target'        => $link['target'] ?? '_blank',
                                'variant'       => 'black',
                                'icon_position' => 'left',
                        ]);
                        ?>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="block-contact-location__media">
                    <iframe
                        class="block-contact-location__image"
                        src="https://www.google.com/maps?q=%CE%A3%CE%97%CE%9C%CE%91+%CE%91.%CE%92.%CE%95.%CE%95.+%CE%9B%CE%B5%CF%85%CE%BA%CE%AC%CE%B4%CE%BF%CF%82+2+%CE%9C%CE%BF%CF%83%CF%87%CE%AC%CF%84%CE%BF&ll=37.9628462,23.6946991&z=17&hl=el&output=embed"
                        title="<?= esc_attr($loc_title ?: 'ΣΗΜΑ'); ?>"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>

            </div>
        </div>
    </div>
</section>
