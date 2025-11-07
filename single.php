<?php
/**
 * The template for displaying single posts
 *
 * @package Ruined
 */

get_header(); ?>

<main id="main" class="site-main py-12 lg:py-16">
    <div class="container mx-auto px-4 max-w-4xl">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-dark-800 rounded-xl shadow-lg overflow-hidden'); ?>>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="h-64 md:h-96 w-full overflow-hidden">
                        <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
                    </div>
                <?php endif; ?>

                <div class="p-6 md:p-8">
                    <!-- Post Categories -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php echo ruined_posted_category(); ?>
                    </div>

                    <!-- Post Title -->
                    <h1 class="text-3xl md:text-4xl font-bold text-dark-900 dark:text-white mb-4 leading-tight">
                        <?php the_title(); ?>
                    </h1>

                    <!-- Post Meta -->
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-8">
                        <div class="flex items-center mr-6">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <?php the_author_posts_link(); ?>
                        </div>
                    </div>

                    <!-- Post Content -->
                    <div class="prose dark:prose-invert max-w-none">
                        <?php the_content(); ?>
                    </div>

                    <!-- Post Tags -->
                    <?php if (has_tag()) : ?>
                        <div class="mt-12 pt-6 border-t border-gray-200 dark:border-dark-600">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4"><?php esc_html_e('Tags', 'ruined'); ?></h3>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $tags = get_the_tags();
                                if ($tags) {
                                    foreach ($tags as $tag) {
                                        echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="inline-block px-3 py-1 text-sm bg-gray-100 dark:bg-dark-700 text-gray-800 dark:text-gray-200 rounded-full hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors">' . esc_html($tag->name) . '</a>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Author Box -->
                    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-dark-600">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 mr-4">
                                <?php echo get_avatar(get_the_author_meta('ID'), 80, '', get_the_author(), ['class' => 'rounded-full']); ?>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-dark-900 dark:text-white"><?php the_author(); ?></h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1"><?php echo get_the_author_meta('description'); ?></p>
                                <div class="flex space-x-4 mt-3">
                                    <?php if (get_the_author_meta('twitter')) : ?>
                                        <a href="<?php echo esc_url(get_the_author_meta('twitter')); ?>" class="text-gray-500 hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                                            <span class="sr-only">Twitter</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (get_the_author_meta('facebook')) : ?>
                                        <a href="<?php echo esc_url(get_the_author_meta('facebook')); ?>" class="text-gray-500 hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                                            <span class="sr-only">Facebook</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Post Navigation -->
                    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-dark-600">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <?php previous_post_link('%link', '← %title', true, '', 'category'); ?>
                            </div>
                            <div class="text-right">
                                <?php next_post_link('%link', '%title →', true, '', 'category'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    <?php
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
