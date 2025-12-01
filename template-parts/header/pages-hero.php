<?php
// ACF fields & post data
$image_url  = get_the_post_thumbnail_url(get_the_ID(), 'large');
$video_url  = get_field('video');
$acf_title  = get_field('title');
$title      = !empty($acf_title) ? $acf_title : get_the_title();
$text       = get_field('text');
$button     = get_field('button_link');
$hero_color       = get_field('hero_color');
$hero_color_class = '';

$button_variant = 'white';

if ( ! empty( $hero_color ) ) {
    // κάντο lowercase για σιγουριά
    $hero_color_normalized = strtolower( $hero_color );

    if ( $hero_color_normalized === 'dark' ) {
        $button_variant = 'black';
    } elseif ( $hero_color_normalized === 'light' ) {
        $button_variant = 'white';
    }
}

if ( ! empty( $hero_color ) ) {
    // το κάνουμε slug ώστε να βγει 'dark' ή 'light'
    $hero_color_class = ' pages-hero--' . sanitize_title( $hero_color );
}

// background fallback αν δεν έχει video
$style = '';
if ($image_url && !$video_url) {
    $style = 'background-image: url(' . esc_url($image_url) . ');';
}

// detect video platform
$video_type  = '';
$video_embed = '';

if ($video_url) {

    // Self-hosted .mp4, .webm
    if (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
        $video_type  = 'self';
        $video_embed = '<video class="hero-video" autoplay muted loop playsinline>
                            <source src="' . esc_url($video_url) . '" type="video/mp4">
                        </video>';

    } elseif (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
        $video_type = 'youtube';

        preg_match('/(youtu\.be\/|v=)([^&]+)/', $video_url, $matches);
        $youtube_id = $matches[2] ?? '';

        $video_embed = '<iframe 
            class="hero-video"
            src="https://www.youtube.com/embed/' . $youtube_id . '?autoplay=1&mute=1&controls=0&loop=1&playlist=' . $youtube_id . '&playsinline=1&modestbranding=1&showinfo=0&rel=0" 
            frameborder="0" 
            allow="autoplay; fullscreen" 
            allowfullscreen>
        </iframe>';

    } elseif (strpos($video_url, 'vimeo.com') !== false) {
        $video_type = 'vimeo';

        preg_match('/vimeo\.com\/(\d+)/', $video_url, $matches);
        $vimeo_id = $matches[1] ?? '';

        $video_embed = '<iframe 
            class="hero-video"
            src="https://player.vimeo.com/video/' . $vimeo_id . '?background=1&autoplay=1&loop=1&muted=1" 
            frameborder="0" 
            webkitallowfullscreen 
            mozallowfullscreen 
            allowfullscreen>
        </iframe>';
    }
}
?>

<div class="pages-hero section-full-width<?= esc_attr( $hero_color_class ); ?> <?= esc_attr($block['className'] ?? ''); ?> <?= !empty($block['align']) ? 'align' . $block['align'] : ''; ?>"
     style="<?= esc_attr($style); ?>">

    <?php if ($video_url && $video_type): ?>
        <div class="pages-hero__video-wrapper">
            <?= $video_embed; ?>
        </div>
    <?php endif; ?>

    <div class="pages-hero__overlay"></div>

    <div class="pages-hero__inner container">
        <div class="pages-hero__content">
            <div class="breadcumbs_title">
                <?php
                if (function_exists('rank_math_the_breadcrumbs')) {
                    echo '<div class="rv-breadcrumbs">';
                    rank_math_the_breadcrumbs();
                    echo '</div>';
                }
                ?>

                <?php if (!empty($title)): ?>
                    <div class="pages-hero__title"><?= wp_kses_post($title); ?></div>
                <?php endif; ?>
            </div>

            <div class="bottom_content">
                <?php if (!empty($text)): ?>
                    <p class="pages-hero__text"><?= esc_html($text); ?></p>
                <?php endif; ?>

                <?php if (!empty($button) && !empty($button['url'])): ?>
                    <?php
                    rv_button_arrow([
                            'text'          => $button['title']  ?? 'Επικοινωνήστε μαζί μας',
                            'url'           => $button['url']    ?? '#',
                            'target'        => $button['target'] ?? '_self',
                            'variant'       =>  $button_variant,
                            'icon_position' => 'left',
                    ]);
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
