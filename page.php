<?php

get_header();
?>

<main id="main" class="site-main">
    <?php rv_show_pages_hero(); ?>

    <div class="container mx-auto">
        <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
