<?php get_header(); ?>

<div class="wrapper_404">
    <div class="content_404">
        <h1 class="text-9xl">404</h1>
        <p class="text-2xl">Page Not Found</p>
        <?php rv_button_arrow([
                'text' =>'Επιστροφή στην Αρχική',
                'url' => home_url('/'),
                'target' => '_self',
                'variant' => 'white',
                'icon_position' => 'left',
        ]); ?>
    </div>
</div>

<?php get_footer(); ?>
