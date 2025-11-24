<?php
// ACF fields
$image = get_field('image');
$video_url = get_field('video');
$title = get_field('title');
$sub_texts = get_field('sub_texts');
$hero_boxes = get_field('hero_box');

// background fallback image
$style = '';
if (!empty($image['url'])) {
    $style = 'background-image: url(' . esc_url($image['url']) . ');';
}

// detect video platform
$video_type = '';
$video_embed = '';

if ($video_url) {

    // Self-hosted .mp4, .webm
    if (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
        $video_type = 'self';
        $video_embed = '<video class="hero-video" autoplay muted loop playsinline>
                            <source src="' . esc_url($video_url) . '" type="video/mp4">
                        </video>';
    } // YouTube
    elseif (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
        $video_type = 'youtube';

        // extract ID
        preg_match('/(youtu\.be\/|v=)([^&]+)/', $video_url, $matches);
        $youtube_id = $matches[2] ?? '';

        $video_embed = '<iframe 
            class="hero-video"
            src="https://www.youtube.com/embed/' . $youtube_id . '?autoplay=1&mute=1&controls=0&loop=1&playlist=' . $youtube_id . '&playsinline=1&modestbranding=1&showinfo=0&rel=0" 
            frameborder="0" 
            allow="autoplay; fullscreen" 
            allowfullscreen>
        </iframe>';
    } // Vimeo
    elseif (strpos($video_url, 'vimeo.com') !== false) {
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

<div class="service-hero section-full-width <?= esc_attr($block['className'] ?? ''); ?> <?= !empty($block['align']) ? 'align' . $block['align'] : ''; ?>"
     style="<?= !$video_url ? esc_attr($style) : ''; ?>">

    <?php if ($video_url && $video_type): ?>
        <div class="service-hero__video-wrapper">
            <?= $video_embed; ?>
        </div>
    <?php endif; ?>

    <div class="service-hero__overlay"></div>

    <div class="service-hero__inner container">
        <div class="service-hero__content">
            <div class="breadcumbs_title">
                <?php
                if (function_exists('rank_math_the_breadcrumbs')) {
                    echo '<div class="rv-breadcrumbs">';
                    rank_math_the_breadcrumbs();
                    echo '</div>';
                }
                ?>

                <?php if (!empty($title)): ?>
                    <div class="service-hero__title"><?= wp_kses_post($title); ?></div>
                <?php endif; ?>
            </div>
            <div class="bottom_content">
                <?php if (!empty($sub_texts['sub_title'])): ?>
                    <div class="service-hero__subtitle"><?= esc_html($sub_texts['sub_title']); ?></div>
                <?php endif; ?>

                <?php if (!empty($sub_texts['subtext'])): ?>
                    <p class="service-hero__text"><?= esc_html($sub_texts['subtext']); ?></p>
                <?php endif; ?>

                <?php
                $button = $sub_texts['button_link'] ?? null;
                if (!empty($button)) {
                    rv_button_arrow([
                            'text' => $button['title'] ?? 'Επικοινωνήστε μαζί μας',
                            'url' => $button['url'] ?? '#',
                            'target' => $button['target'] ?? '_self',
                            'variant' => 'white',
                            'icon_position' => 'left',
                    ]);
                }
                ?>
            </div>
        </div>

        <?php if ($hero_boxes): ?>
            <div class="service-hero__boxes">
                <?php foreach ($hero_boxes as $box): ?>
                    <article class="hero-box">
                        <a class="hero__link" href="<?= esc_url($box['link']['url']); ?>"
                           target="<?= esc_attr($box['link']['target']); ?>"></a>

                        <?php if ($box['image']): ?>
                            <div class="hero-box__image">
                                <img src="<?= esc_url($box['image']['url']); ?>" alt="">
                            </div>
                        <?php endif; ?>

                        <div class="hero-box__content">
                            <span class="related_title"><?= __('RELATED ARTICLE', 'ruined') ?></span>
                            <h3 class="hero-box__title"><?= esc_html($box['title']); ?></h3>

                            <div class="hero-box__link">
                                <?= esc_html($box['link']['title'] ?: 'Διάβασε περισσότερα'); ?>
                                <span class="hero-box__icon">
                                    <svg width="7" height="9" viewBox="0 0 7 9">
                                        <path d="M0.977505 0.977581L5.12476 4.48757L0.977504 7.99756"
                                              stroke="white" stroke-width="1.95503" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
