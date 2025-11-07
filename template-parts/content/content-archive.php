<?php
/**
 * Template part for displaying post archives
 *
 * @package Ruined
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-dark-800 rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail h-48 overflow-hidden">
            <a href="<?php the_permalink(); ?>" class="block h-full">
                <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition-transform duration-500 hover:scale-105']); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="p-6">
        <div class="post-meta text-sm text-gray-500 dark:text-gray-400 mb-3 flex items-center space-x-4">
            <time datetime="<?php echo get_the_date('c'); ?>">
                <?php echo get_the_date(); ?>
            </time>
            <span>•</span>
            <span class="post-categories">
                <?php the_category(', '); ?>
            </span>
        </div>
        
        <h2 class="text-xl font-bold mb-3 text-dark-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>
        
        <div class="post-excerpt text-gray-600 dark:text-gray-300 mb-4">
            <?php the_excerpt(); ?>
        </div>
        
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
            <a href="<?php the_permalink(); ?>" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">
                <?php esc_html_e('Read More', 'ruined'); ?>
                <span class="ml-1">→</span>
            </a>
            
            <?php if (get_comments_number() > 0) : ?>
                <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <?php echo get_comments_number(); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
</article>
