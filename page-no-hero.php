<?php
/**
 * Template Name: Page without Hero
 */
get_header();
?>

    <div id="main" class="site-main">
        <div class="container mx-auto">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>

<?php
get_footer();
