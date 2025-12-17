<?php

get_header();
?>

<div id="main" class="site-main">
    <?php rv_show_pages_hero(); ?>

    <div class="container mx-auto p-0">
        <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
