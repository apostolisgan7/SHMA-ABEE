<?php
/**
 * Block: Home Products (acf/home-products)
 * Fields:
 * - title (wysiwyg)
 * - text (wysiwyg)
 * - home_products (relationship -> product)
 */
if (!defined('ABSPATH')) exit;

$title   = get_field('title');   // wysiwyg
$text    = get_field('text');    // wysiwyg
$items   = get_field('home_products');

$block_id = 'home-products-' . $block['id'];
$classes  = 'rv-home-products';
if (!empty($block['className'])) $classes .= ' ' . $block['className'];
if (!empty($block['align']))     $classes .= ' align' . $block['align'];

$as_carousel = is_array($items) && count($items) > 3;
?>
<section id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($classes); ?>">
    <div class="rv-home-products__inner container">

        <div class="rv-home-products__head">
            <?php if ($title): ?>
                <div class="rv-home-products__title wysiwyg"><?php echo wp_kses_post($title); ?></div>
            <?php endif; ?>
            <?php if ($text): ?>
                <div class="rv-home-products__text wysiwyg"><?php echo wp_kses_post($text); ?></div>
            <?php endif; ?>

            <?php if ($as_carousel): ?>
                <div class="rv-hp__navwrap">
                    <button class="rv-hp__nav rv-hp__nav--prev" aria-label="<?php esc_attr_e('Προηγούμενο','ruined'); ?>">
                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.68422 11.4943L0.911987 6.20308L6.68422 0.911865" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                    </button>
                    <button class="rv-hp__nav rv-hp__nav--next" aria-label="<?php esc_attr_e('Επόμενο','ruined'); ?>">
                        <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.911971 0.911955L6.6842 6.20317L0.91197 11.4944" stroke="black" stroke-width="1.82386" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($items)): ?>
            <?php if ($as_carousel): ?>
                <div class="rv-home-products__carousel swiper" data-slider="products">
                    <div class="swiper-wrapper">
                        <?php foreach ($items as $post_obj): setup_postdata($post_obj); ?>
                            <div class="swiper-slide">
                                <?php get_template_part('template-parts/items/item','product',['post'=>$post_obj]); ?>
                            </div>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                    <div class="swiper-pagination" aria-hidden="true"></div>
                </div>
            <?php else: ?>
                <div class="rv-home-products__list">
                    <?php foreach ($items as $post_obj): setup_postdata($post_obj);
                        get_template_part('template-parts/items/item','product',['post'=>$post_obj]);
                    endforeach; wp_reset_postdata(); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</section>
