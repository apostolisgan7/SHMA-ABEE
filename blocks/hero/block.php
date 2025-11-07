<?php
/**
 * Hero Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create class attribute allowing for custom "className" and "align" values.
$classes = ['ruined-block', 'ruined-hero', 'relative py-20 bg-cover bg-center'];
if (!empty($block['className'])) {
    $classes[] = $block['className'];
}
if (!empty($block['align'])) {
    $classes[] = 'align' . $block['align'];
}

// Get ACF values
$image = get_field('image');
$title = get_field('title');
$sub_texts = get_field('sub_texts');

// Set up background image style
$styles = [];
if ($image) {
    $styles[] = 'background-image: url(' . esc_url($image['url']) . ')';
}
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" style="<?php echo esc_attr(implode('; ', $styles)); ?>">
    <div class="absolute inset-0 bg-black/50"></div>
    
    <div class="container mx-auto px-4 relative z-10 py-16 md:py-24">
        <div class="max-w-4xl mx-auto text-center text-white">
            <?php if (!empty($sub_texts['sub_title'])) : ?>
                <div class="text-lg font-medium mb-2"><?php echo esc_html($sub_texts['sub_title']); ?></div>
            <?php endif; ?>
            
            <?php if ($title) : ?>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            
            <?php if (!empty($sub_texts['subtext'])) : ?>
                <p class="text-xl mb-8"><?php echo esc_html($sub_texts['subtext']); ?></p>
            <?php endif; ?>
            
            <?php if (!empty($sub_texts['button_link'])) : 
                $link = $sub_texts['button_link'];
                $link_url = $link['url'] ?? '#';
                $link_title = $link['title'] ?? 'Learn More';
                $link_target = $link['target'] ?? '_self';
            ?>
                <a href="<?php echo esc_url($link_url); ?>" 
                   target="<?php echo esc_attr($link_target); ?>"
                   class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-8 rounded-lg transition-colors">
                    <?php echo esc_html($link_title); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>