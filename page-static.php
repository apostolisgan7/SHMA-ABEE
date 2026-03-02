<?php
/**
 * Template Name: Page Static
 */
get_header();
?>

<div id="main" class="site-main static-page-wrapper">
    <?php rv_show_pages_hero(); ?>

    <div class="container static-page-content mx-auto">
        <?php while (have_posts()) : the_post();
            the_content(); endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
