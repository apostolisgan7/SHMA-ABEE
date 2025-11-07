<?php
/**
 * The main template file for displaying blog posts
 * 
 * @package Ruined
 */

get_header();
?>

<main id="primary" class="site-main py-12">
    <div class="container mx-auto px-4">
        <header class="mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-dark-900 dark:text-white mb-4">
                <?php single_post_title('', true); ?>
            </h1>
            <?php if (get_theme_mod('blog_description')) : ?>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    <?php echo esc_html(get_theme_mod('blog_description')); ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                // Start the Loop
                while (have_posts()) : 
                    the_post();
                    // Include the template part for the content
                    get_template_part('template-parts/content/content', 'archive');
                endwhile; 
                ?>
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '&larr; ' . esc_html__('Previous', 'ruined'),
                    'next_text' => esc_html__('Next', 'ruined') . ' &rarr;',
                    'class'     => 'pagination flex justify-center space-x-2',
                ]);
                ?>
            </div>
            
        <?php else : 
            // If no content, include the "No posts found" template
            get_template_part('template-parts/content/content', 'none');
        endif; ?>
    </div>
</main>

<?php
get_footer();

