<?php get_header(); ?>

<main id="main" class="site-main py-12 lg:py-16">
    <div class="container mx-auto px-4">
        <?php if (have_posts()) : ?>
            <header class="mb-12 text-center">
                <?php
                the_archive_title('<h1 class="text-3xl md:text-4xl font-bold text-dark-900 dark:text-white mb-4">', '</h1>');
                the_archive_description('<div class="max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-400">', '</div>');
                
                // Show category filter if on blog page
                if (is_home() || is_archive()) :
                    $categories = get_categories(['hide_empty' => true]);
                    if (!empty($categories)) :
                ?>
                    <div class="flex flex-wrap justify-center gap-2 mt-8">
                        <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full transition-colors <?php echo is_home() ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-dark-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-600'; ?>">
                            <?php esc_html_e('All', 'ruined'); ?>
                        </a>
                        <?php foreach ($categories as $category) : ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full transition-colors <?php echo (is_category($category->term_id) ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-dark-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-600'); ?>">
                                <?php echo esc_html($category->name); ?>
                                <span class="ml-1.5 bg-white/20 dark:bg-black/20 px-2 py-0.5 rounded-full text-xs">
                                    <?php echo esc_html($category->count); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php 
                    endif;
                endif; 
                ?>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('group bg-white dark:bg-dark-800 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="block overflow-hidden h-48 md:h-56">
                                <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105']); ?>
                            </a>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <?php echo ruined_posted_category(); ?>
                            </div>
                            
                            <h2 class="text-xl font-bold text-dark-900 dark:text-white mb-3 leading-snug">
                                <a href="<?php the_permalink(); ?>" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                <?php echo get_the_excerpt(); ?>
                            </p>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('M j, Y'); ?></time>
                                    <span class="mx-2">•</span>
                                    <span><?php the_reading_time(); ?></span>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                                    <?php esc_html_e('Read More', 'ruined'); ?>
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="mt-12">
                <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '&larr; ' . __('Previous', 'ruined'),
                    'next_text' => __('Next', 'ruined') . ' &rarr;',
                    'class'     => 'pagination',
                ]);
                ?>
            </div>

        <?php else : ?>
            <div class="text-center py-16">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4"><?php esc_html_e('No posts found', 'ruined'); ?></h2>
                <p class="text-gray-600 dark:text-gray-400"><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'ruined'); ?></p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block mt-6 px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <?php esc_html_e('Back to Home', 'ruined'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
