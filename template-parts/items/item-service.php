<?php
/**
 * Template Part: Service Item Card
 * Args: [ 'post' => WP_Post ]
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_obj = isset($args['post']) ? $args['post'] : null;
if ( ! $post_obj instanceof WP_Post ) return;

$permalink = get_permalink($post_obj);
$title     = get_the_title($post_obj->ID);

// Featured image
$img_id    = get_post_thumbnail_id($post_obj->ID);
$img_src   = $img_id ? wp_get_attachment_image_url($img_id, 'medium_large') : '';
$img_alt   = $img_id ? get_post_meta($img_id, '_wp_attachment_image_alt', true) : $title;

// Taxonomy term (service-category)
$terms = get_the_terms($post_obj->ID, 'service-category');
$term  = ( is_array($terms) && ! is_wp_error($terms) ) ? reset($terms) : null;
$term_name = $term ? $term->name : '';

?>
<article class="rv-service-card">
    <a class="rv-service-card__link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">

        <div class="rv-service-card__media">
            <?php if ($img_src): ?>
                <img class="rv-service-card__img" src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" />
            <?php else: ?>
                <div class="rv-service-card__placeholder" aria-hidden="true"></div>
            <?php endif; ?>
            <span class="rv-service-card__hover"></span>
        </div>

        <div class="rv-service-card__meta">
            <?php if ($term_name): ?>
                <div class="rv-service-card__tag">
                    <?php echo esc_html($term_name); ?>
                </div>
            <?php endif; ?>

            <h3 class="rv-service-card__title"><?php echo esc_html($title); ?></h3>

            <span class="rv-service-card__btn" aria-hidden="true" focusable="false">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" role="img" aria-hidden="true">
          <rect x="1" y="1" width="30" height="30" rx="6"></rect>
          <path d="M13 9l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
      </span>
        </div>

    </a>
</article>
