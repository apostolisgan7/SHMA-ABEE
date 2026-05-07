<?php
$title = get_field('title');
$subtitle = get_field('subtitle');
$form_shortcode = get_field('form_shortcode');

$classes  = 'block-contact-form';
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
?>

<section id="sima-contact-form" class="<?= esc_attr($classes); ?>">
    <div class="container p-0">
        <div class="block-contact-form__card">

            <div class="block-contact-form__head">
                <?php if ($title): ?>
                    <h2 class="block-contact-form__title" data-animate="title-reveal"><?= esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($subtitle): ?>
                    <p class="block-contact-form__subtitle" data-animate="fade-up" data-animate-delay="0.15"><?= esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <div class="block-contact-form__body" data-animate="fade-up" data-animate-delay="0.3">
                <?= $form_shortcode ? do_shortcode($form_shortcode) : ''; ?>
            </div>

        </div>
    </div>
</section>
