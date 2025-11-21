<?php
/**
 * Block: Home Services (acf/home-services)
 */

if (!defined('ABSPATH')) exit;

$subtitle = get_field('subtitle');
$title = get_field('title');
$button_link = get_field('button_link');
$items = get_field('service_items');

$block_id = 'home-services-' . $block['id'];
$classes = 'grid-services';
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['align'])) $classes .= ' align' . $block['align'];
?>
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($classes); ?>">
    <div class="grid-services__inner container">
        <div class="grid-services__head">
            <?php if ($subtitle) : ?>
                <span class="grid-services__subtitle">
                    <span class="dot" aria-hidden="true"></span>
                    <?php echo esc_html($subtitle); ?>
                </span>
            <?php endif; ?>
            <div class="right_col">
                <?php if ($title) : ?>
                    <h2 class="grid-services__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php
                if (!empty($button_link)) :
                    $btn_url = $button_link['url'] ?? '#';
                    $btn_title = $button_link['title'] ?? '';
                    $btn_target = $button_link['target'] ?? '_self';

                    rv_button_arrow([
                        'text' => $btn_title ?: __('Όλες οι υπηρεσίες μας', 'ruined'),
                        'url' => $btn_url,
                        'target' => $btn_target,
                        'variant' => 'black',
                        'icon_position' => 'left',
                        'class' => 'home-history__btn grid-services__cta',
                        'register' => false,
                    ]);
                endif;
                ?>
            </div>
        </div>

        <?php if (!empty($items)) : ?>
            <!-- Desktop Grid -->
            <div class="grid-services__grid">
                <?php foreach ($items as $post_obj) : ?>
                    <div class="grid-services__item">
                        <?php
                        get_template_part(
                            'template-parts/items/item',
                            'service',
                            array('post' => $post_obj)
                        );
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Mobile Carousel -->
            <div class="grid-services__carousel swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($items as $post_obj) : ?>
                        <div class="swiper-slide">
                            <?php
                            get_template_part(
                                'template-parts/items/item',
                                'service',
                                array('post' => $post_obj)
                            );
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php endif; ?>
    </div>
</section>
