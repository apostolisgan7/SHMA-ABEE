<?php
$title = get_field('title');   // text
$link  = get_field('link');    // link array
$text  = get_field('text');    // wysiwyg
$logos = get_field('logos');   // repeater

// Optional: allow inline markup if you paste <span> in text field
$title_html = $title ? wp_kses_post($title) : '';
?>

<section class="block-text-with-logos section-full-width">
    <div class="smaller-container container">

        <?php if (!empty($title_html)): ?>
            <div class="block-text-with-logos__title">
                <p><?php echo $title_html; ?></p>
            </div>
        <?php endif; ?>

        <div class="block-text-with-logos__inner">

            <!-- LEFT -->
            <div class="block-text-with-logos__left">
                <?php if (!empty($link)): ?>
                    <div class="block-text-with-logos__cta">
                        <?php
                        rv_button_arrow([
                            'text'          => $link['title'] ?? '',
                            'url'           => $link['url'] ?? '#',
                            'target'        => $link['target'] ?? '_self',
                            'variant'       => 'black',
                            'icon_position' => 'left',
                        ]);
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT -->
            <div class="block-text-with-logos__right">
                <?php if (!empty($text)): ?>
                    <div class="block-text-with-logos__text">
                        <?php echo wp_kses_post($text); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- LOGOS -->
        <?php if (!empty($logos)): ?>
            <div class="block-text-with-logos__logos">
                <?php foreach ($logos as $row): ?>
                    <?php
                    $logo = $row['logo'] ?? null;
                    if (empty($logo) || empty($logo['ID'])) continue;
                    ?>
                    <div class="block-text-with-logos__logo">
                        <?php
                        echo wp_get_attachment_image(
                            $logo['ID'],
                            'medium',
                            false,
                            [
                                'loading' => 'lazy',
                                'alt'     => esc_attr($logo['alt'] ?? ''),
                            ]
                        );
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>