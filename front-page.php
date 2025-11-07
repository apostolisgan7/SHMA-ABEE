<?php
/**
 * The front page template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @package Ruined
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    // Start the loop
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php
                    the_content();
                    
                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>
                </div>
            </article>
            <?php
        endwhile;
    else :
        // If no content, include the "No posts found" template.
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
</main>

<?php
get_footer();
