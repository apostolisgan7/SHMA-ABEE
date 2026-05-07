<?php
/**
 * Template Name: Page without Hero
 */
get_header();
?>

    <div id="main" class="site-main">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
    </div>

<?php
get_footer();
