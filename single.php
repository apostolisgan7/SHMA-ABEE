<?php
/**
 * The template for displaying single posts
 *
 * @package Ruined
 */

get_header();
?>

<main id="main" class="site-main rv-single-post section-full-width">
    <?php while (have_posts()) : the_post();
        $post_id   = get_the_ID();
        $cats      = get_the_category();
        $author_id = (int) get_the_author_meta('ID');
        $url       = get_permalink();
        $title     = get_the_title();
        $share     = [
            'in' => ['label' => 'LinkedIn', 'href' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url)],
            'f'  => ['label' => 'Facebook', 'href' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($url)],
            'x'  => ['label' => 'X', 'href' => 'https://twitter.com/intent/tweet?url=' . rawurlencode($url) . '&text=' . rawurlencode($title)],
        ];
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('rv-single-post__article'); ?>>
            <div class="container sec_padding">
                <div class="rv-single-post__wrap">

                    <header class="rv-single-post__header" data-animate="fade-up">
                        <div class="rv-post-card__head">
                            <?php if ($cats) : ?>
                                <a class="rv-post-card__tag" href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>"><?php echo esc_html($cats[0]->name); ?></a>
                            <?php endif; ?>
                            <span class="rv-post-card__date"><?php echo esc_html(get_the_date('j F, Y')); ?></span>
                        </div>

                        <h1 class="rv-single-post__title"><?php the_title(); ?></h1>

                        <div class="rv-single-post__author">
                            <?php echo get_avatar($author_id, 56, '', '', ['class' => 'rv-post-card__avatar']); ?>
                            <div>
                                <strong><?php the_author(); ?></strong>
                                <span><?php printf(esc_html__('Χρόνος Ανάγνωσης: %d λεπτά', 'ruined'), rv_reading_time()); ?></span>
                            </div>
                        </div>
                    </header>

                    <div class="rv-single-post__bar" x-data="{ copied: false }">
                        <button type="button" class="rv-single-post__copy"
                                @click="navigator.clipboard.writeText('<?php echo esc_js($url); ?>').then(() => { copied = true; setTimeout(() => copied = false, 2000) })">
                            <svg width="22" height="22" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18.4777 8.62268H22.1732C22.982 8.62268 23.7829 8.78199 24.5302 9.09151C25.2774 9.40103 25.9564 9.85471 26.5283 10.4266C27.1002 10.9986 27.5539 11.6775 27.8634 12.4248C28.1729 13.172 28.3322 13.9729 28.3322 14.7818C28.3322 15.5906 28.1729 16.3915 27.8634 17.1387C27.5539 17.886 27.1002 18.565 26.5283 19.1369C25.9564 19.7088 25.2774 20.1625 24.5302 20.472C23.7829 20.7815 22.982 20.9408 22.1732 20.9408H18.4777M11.0868 20.9408H7.3914C6.58258 20.9408 5.78168 20.7815 5.03443 20.472C4.28717 20.1625 3.6082 19.7088 3.03628 19.1369C1.88123 17.9818 1.23233 16.4152 1.23233 14.7818C1.23233 13.1483 1.88123 11.5817 3.03628 10.4266C4.19133 9.27158 5.75792 8.62268 7.3914 8.62268H11.0868" stroke="currentColor" stroke-width="2.46363" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.85352 14.7817H19.708" stroke="currentColor" stroke-width="2.46363" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span x-show="!copied"><?php esc_html_e('Αντιγραφή Συνδέσμου', 'ruined'); ?></span>
                            <span x-show="copied" x-cloak><?php esc_html_e('Ο σύνδεσμος αντιγράφηκε', 'ruined'); ?></span>
                        </button>

                        <div class="rv-single-post__share">
                            <span><?php esc_html_e('Δημοσίευση:', 'ruined'); ?></span>
                            <?php foreach ($share as $key => $item) : ?>
                                <a href="<?php echo esc_url($item['href']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($item['label']); ?>">
                                    <?php if ($key === 'in') : ?>
                                        <svg width="23" height="23" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2.8348 5.26717H2.80059C2.43843 5.28981 2.07549 5.23743 1.7345 5.11331C1.39351 4.9892 1.08182 4.79602 0.818938 4.54587C0.556059 4.29573 0.347656 3.99401 0.206776 3.65959C0.0658964 3.32518 -0.0044248 2.96529 0.000215572 2.60244C0.00485594 2.2396 0.084358 1.88162 0.233744 1.55092C0.383129 1.22022 0.599181 0.923926 0.868371 0.680585C1.13756 0.437245 1.45409 0.2521 1.79814 0.136746C2.14219 0.0213908 2.50635 -0.0216903 2.86782 0.0101998C3.23 -0.0151007 3.59349 0.0346819 3.93553 0.156428C4.27756 0.278173 4.59076 0.469257 4.8555 0.717703C5.12023 0.966149 5.33079 1.2666 5.47399 1.60022C5.61718 1.93385 5.68991 2.29346 5.68763 2.65651C5.68535 3.01955 5.60811 3.37822 5.46073 3.71002C5.31336 4.04182 5.09904 4.3396 4.8312 4.5847C4.56337 4.8298 4.24779 5.01693 3.90426 5.13437C3.56072 5.25181 3.19663 5.29702 2.8348 5.26717ZM0.492337 8.80564H5.21029V22.9595H0.492337V8.80564ZM17.5949 8.80564C16.7999 8.80779 16.0157 8.98957 15.3009 9.33739C14.586 9.68522 13.959 10.1901 13.4667 10.8143V8.80564H8.74875V22.9595H13.4667V16.4723C13.4667 15.8467 13.7152 15.2466 14.1576 14.8043C14.6 14.3619 15.2 14.1133 15.8257 14.1133C16.4513 14.1133 17.0513 14.3619 17.4937 14.8043C17.9361 15.2466 18.1846 15.8467 18.1846 16.4723V22.9595H22.9026V14.1133C22.9026 12.7056 22.3434 11.3556 21.348 10.3602C20.3526 9.36484 19.0026 8.80564 17.5949 8.80564Z" fill="currentColor"/></svg>
                                    <?php elseif ($key === 'f') : ?>
                                        <svg width="11" height="22" viewBox="0 0 11 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8.6946 3.52433H10.6154V0.148647C9.68539 0.0481693 8.75096 -0.00143762 7.81595 3.17019e-05C5.03694 3.17019e-05 3.1366 1.76218 3.1366 4.98925V7.77048H0V11.5496H3.1366V21.2308H6.89642V11.5496H10.0228L10.4928 7.77048H6.89642V5.36079C6.89642 4.24618 7.1825 3.52433 8.6946 3.52433Z" fill="currentColor"/></svg>
                                    <?php else : ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2H22l-7.2 8.2L23.3 22h-6.6l-5.2-6.8L5.5 22H2.4l7.7-8.8L1.9 2h6.8l4.7 6.2L18.9 2zm-1.2 18h1.7L7.4 3.9H5.6L17.7 20z"/></svg>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if (has_post_thumbnail()) : ?>
                        <figure class="rv-single-post__image" data-animate="image-reveal">
                            <?php the_post_thumbnail('full'); ?>
                        </figure>
                    <?php endif; ?>

                    <div class="rv-single-post__content rv-post-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </article>

        <?php
        $related_args = [
            'post_type'           => 'post',
            'posts_per_page'      => 3,
            'post__not_in'        => [$post_id],
            'ignore_sticky_posts' => true,
        ];
        if ($cats) {
            $related_args['category__in'] = wp_list_pluck($cats, 'term_id');
        }
        $related = new WP_Query($related_args);
        ?>
        <?php if ($related->have_posts()) : ?>
            <section class="rv-related-posts section-full-width">
                <div class="container sec_padding">
                    <div class="rv-related-posts__head">
                        <h2><?php esc_html_e('Σχετικά Άρθρα', 'ruined'); ?></h2>
                        <?php
                        rv_button_arrow([
                            'text'     => __('Όλα τα Άρθρα', 'ruined'),
                            'url'      => rv_get_blog_url(),
                            'variant'  => 'black',
                            'register' => false,
                        ]);
                        ?>
                    </div>
                    <div class="rv-blog-grid">
                        <?php
                        while ($related->have_posts()) {
                            $related->the_post();
                            rv_render_post_card();
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
