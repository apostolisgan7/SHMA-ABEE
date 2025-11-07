<?php
/**
 * Hero section template part
 *
 * @package Ruined
 */

// Get hero content from theme mods or use defaults
$hero_title = get_theme_mod('hero_title', 'Welcome to ' . get_bloginfo('name'));
$hero_description = get_theme_mod('hero_description', 'A modern WordPress theme with clean design and powerful features.');
$hero_button_text = get_theme_mod('hero_button_text', 'Get Started');
$hero_button_url = get_theme_mod('hero_button_url', '#');
$hero_background = get_theme_mod('hero_background', get_template_directory_uri() . '/assets/images/hero-bg.jpg');
?>

<section class="relative bg-dark-900 text-white py-20 md:py-32 overflow-hidden">
    <!-- Background Image/Overlay -->
    <?php if ($hero_background) : ?>
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-r from-dark-900/90 to-dark-800/80"></div>
            <img src="<?php echo esc_url($hero_background); ?>" 
                 alt="<?php echo esc_attr($hero_title); ?>" 
                 class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <?php if (get_theme_mod('show_hero_badge', true)) : ?>
                <span class="inline-block bg-primary-600 text-white text-sm font-semibold px-4 py-1 rounded-full mb-4">
                    <?php echo esc_html(get_theme_mod('hero_badge_text', 'New Release')); ?>
                </span>
            <?php endif; ?>
            
            <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6" data-aos="fade-up">
                <?php echo esc_html($hero_title); ?>
            </h1>
            
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                <?php echo esc_html($hero_description); ?>
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                <a href="<?php echo esc_url($hero_button_url); ?>" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-300">
                    <?php echo esc_html($hero_button_text); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
                
                <?php if (get_theme_mod('show_secondary_button', true)) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('secondary_button_url', '#')); ?>" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-transparent hover:bg-white/10 border-2 border-white/20 text-white font-medium rounded-lg transition-colors duration-300">
                        <?php echo esc_html(get_theme_mod('secondary_button_text', 'Learn More')); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
