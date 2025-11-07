<?php get_header(); ?>

<div class="container mx-auto px-4 py-xl text-center">
    <h1 class="text-9xl font-extrabold text-gray-300">404</h1>
    <p class="text-2xl mt-4">Page Not Found</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="mt-md inline-block button-primary">Go Home</a>
    <style>.button-primary { @include button-primary; }</style>
</div>

<?php get_footer(); ?>
