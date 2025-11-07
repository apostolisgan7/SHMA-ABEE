<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package Ruined
 */
?>

<div class="text-center py-16">
    <h2 class="text-2xl font-bold text-dark-900 dark:text-white mb-4">
        <?php esc_html_e('No posts found', 'ruined'); ?>
    </h2>
    <p class="text-gray-600 dark:text-gray-300">
        <?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'ruined'); ?>
    </p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="mt-4 inline-block text-primary-600 dark:text-primary-400 hover:underline">
        <?php esc_html_e('Return to Home', 'ruined'); ?>
    </a>
</div>
