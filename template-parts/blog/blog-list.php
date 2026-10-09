<?php
/**
 * Template Part: Blog listing (hero + category tabs + grid + load more)
 * Used by home.php (posts page) and archive.php (category archives).
 */
if (!defined('ABSPATH')) exit;

global $wp_query;

$active_cat = is_category() ? get_queried_object()->slug : '';
$blog_url   = rv_get_blog_url();
$categories = get_categories(['hide_empty' => true]);

$featured_id = rv_get_blog_featured_post_id();
$f_cats      = $featured_id ? get_the_category($featured_id) : [];
?>
<main id="primary" class="site-main rv-blog section-full-width">

    <?php if ($featured_id) : ?>
        <section class="rv-blog-hero section-full-width">
            <div class="container sec_padding">
                <div class="rv-blog-hero__inner">
                    <div class="rv-blog-hero__content" data-animate="fade-up">
                        <div class="rv-post-card__head">
                            <?php if ($f_cats) : ?>
                                <span class="rv-post-card__tag"><?php echo esc_html($f_cats[0]->name); ?></span>
                            <?php endif; ?>
                            <span class="rv-post-card__date"><?php echo esc_html(get_the_date('j F, Y', $featured_id)); ?></span>
                        </div>
                        <h1 class="rv-blog-hero__title"><?php echo esc_html(get_the_title($featured_id)); ?></h1>
                        <?php
                        rv_button_arrow([
                            'text'          => __('Διαβάστε περισσότερα', 'ruined'),
                            'url'           => get_permalink($featured_id),
                            'variant'       => 'black',
                            'icon_position' => 'left',
                            'register'      => false,
                        ]);
                        ?>
                    </div>
                    <a class="rv-blog-hero__image" href="<?php echo esc_url(get_permalink($featured_id)); ?>" data-animate="image-reveal" data-animate-direction="right" aria-hidden="true" tabindex="-1">
                        <?php echo get_the_post_thumbnail($featured_id, 'large', ['fetchpriority' => 'high']); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="rv-blog-list section-full-width"
             x-data="rvBlog({ cat: '<?php echo esc_js($active_cat); ?>', max: <?php echo (int) $wp_query->max_num_pages; ?> })">
        <div class="container sec_padding">

            <h2 class="rv-blog-list__title" data-animate="title-reveal"><?php esc_html_e('Ενημερώσεις ανά κατηγορία', 'ruined'); ?></h2>

            <?php if (!empty($categories)) : ?>
                <div class="rv-tabs-nav rv-blog-tabs" role="tablist">
                    <a href="<?php echo esc_url($blog_url); ?>" role="tab"
                       :class="{active: cat === ''}"
                       @click.prevent="setCat('', $el.href)"><?php esc_html_e('Όλα τα Άρθρα', 'ruined'); ?></a>
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" role="tab"
                           :class="{active: cat === '<?php echo esc_js($category->slug); ?>'}"
                           @click.prevent="setCat('<?php echo esc_js($category->slug); ?>', $el.href)"><?php echo esc_html($category->name); ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="rv-blog-grid" x-ref="grid" :class="{'is-loading': loading}" aria-live="polite">
                <?php
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        rv_render_post_card();
                    }
                }
                ?>
            </div>

            <p class="rv-blog-empty" x-show="empty" x-cloak><?php esc_html_e('Δεν βρέθηκαν άρθρα.', 'ruined'); ?></p>

            <div class="rv-load-more-wrap" x-show="page < max" x-cloak
                 :class="{'is-loading': loading}" @click.prevent="loadMore()">
                <?php
                rv_button_arrow([
                    'text'          => __('Φόρτωσε περισσότερα', 'ruined'),
                    'url'           => '#',
                    'variant'       => 'black',
                    'icon_position' => 'left',
                    'register'      => false,
                ]);
                ?>
            </div>
        </div>
    </section>
</main>
