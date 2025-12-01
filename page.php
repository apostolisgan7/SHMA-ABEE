<?php
get_header();
?>

	<main id="main" class="site-main">
        <?php get_template_part('template-parts/header/pages-hero'); ?>
		<div class="container mx-auto">
			<?php
			while (have_posts()) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</main>

<?php
get_footer();