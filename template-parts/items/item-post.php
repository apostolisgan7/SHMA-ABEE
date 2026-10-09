<?php
/**
 * Template Part: Post Item Card (uses the current post in the loop)
 */
if (!defined('ABSPATH')) exit;

$post_id   = get_the_ID();
$permalink = get_permalink($post_id);
$title     = get_the_title($post_id);
$cats      = get_the_category($post_id);
$cat_name  = $cats ? $cats[0]->name : '';
$author_id = (int) get_post_field('post_author', $post_id);
?>
<article class="rv-post-card">
    <a class="rv-post-card__link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
        <div class="rv-post-card__media">
            <?php if (has_post_thumbnail($post_id)) : ?>
                <?php echo get_the_post_thumbnail($post_id, 'medium_large', ['class' => 'rv-post-card__img', 'loading' => 'lazy']); ?>
            <?php else : ?>
                <div class="rv-post-card__placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>

        <div class="rv-post-card__meta">
            <div class="rv-post-card__head">
                <?php if ($cat_name) : ?>
                    <span class="rv-post-card__tag"><?php echo esc_html($cat_name); ?></span>
                <?php endif; ?>
                <span class="rv-post-card__date"><?php echo esc_html(get_the_date('j F, Y', $post_id)); ?></span>
            </div>

            <h3 class="rv-post-card__title"><?php echo esc_html($title); ?></h3>

            <div class="rv-post-card__foot">
                <span class="rv-post-card__author">
                    <?php echo get_avatar($author_id, 32, '', '', ['class' => 'rv-post-card__avatar']); ?>
                    <span><?php echo esc_html(get_the_author_meta('display_name', $author_id)); ?></span>
                </span>
                <span class="rv-post-card__btn" aria-hidden="true">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" role="img" aria-hidden="true">
                        <rect x="1" y="1" width="30" height="30" rx="6"></rect>
                        <path d="M13 9l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
            </div>
        </div>
    </a>
</article>
