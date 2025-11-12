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
$classes = 'rv-home-services';
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['align'])) $classes .= ' align' . $block['align'];

$as_carousel = is_array($items) && count($items) > 3;
?>
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($classes); ?>">

    <div class="rv-home-services__inner container">

        <div class="rv-home-services__head">
            <?php if ($subtitle) : ?>
                <span class="rv-home-services__subtitle">
          <span class="dot" aria-hidden="true"></span>
          <?php echo esc_html($subtitle); ?>
        </span>
            <?php endif; ?>
            <div class="right_col">
                <?php if ($title) : ?>
                    <h2 class="rv-home-services__title"><?php echo esc_html($title); ?></h2>
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
                            'class' => 'home-history__btn rv-home-services__cta',
                            'register' => false,
                    ]);
                endif;
                ?>
            </div>
        </div>

        <?php if (!empty($items)) : ?>
            <?php if ($as_carousel) : ?>
                <div class="rv-home-services__carousel swiper" data-slider="services">
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
                    <div class="swiper-pagination" aria-hidden="true"></div>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>
