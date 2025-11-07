<?php
/**
 * Template functions and definitions
 *
 * @package Ruined
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add sticky header class to body if enabled in customizer
 */
function ruined_add_sticky_header_class($classes) {
    if (get_theme_mod('ruined_sticky_header', false)) {
        $classes[] = 'has-sticky-header';
    }
    return $classes;
}
add_filter('body_class', 'ruined_add_sticky_header_class');

/**
 * Calculate reading time in minutes
 *
 * @param string $content The post content
 * @return string Reading time in minutes
 */
function ruined_reading_time($content = '') {
    // If no content is provided, get the current post content
    if (empty($content)) {
        $content = get_post_field('post_content');
    }

    // Remove HTML tags and shortcodes
    $content = wp_strip_all_tags(strip_shortcodes($content));

    // Count words
    $word_count = str_word_count($content);

    // Average reading speed (words per minute)
    $words_per_minute = 200;

    // Calculate reading time
    $reading_time = ceil($word_count / $words_per_minute);

    // Return reading time with proper singular/plural form
    return sprintf(
        _n('%d min read', '%d min read', $reading_time, 'ruined'),
        $reading_time
    );
}

/**
 * Display the post reading time
 */
function the_reading_time($content = '') {
    echo esc_html(ruined_reading_time($content));
}

/**
 * Get the first category of the post with link
 */
function ruined_posted_category() {
    $categories = get_the_category();
    if (!empty($categories)) {
        $category = $categories[0];
        return sprintf(
            '<a href="%s" class="inline-block text-xs font-medium px-2.5 py-1 rounded-full bg-primary-100 dark:bg-dark-700 text-primary-800 dark:text-primary-200">%s</a>',
            esc_url(get_category_link($category->term_id)),
            esc_html($category->name)
        );
    }
    return '';
}

/**
 * Display the post date with human-readable format
 */
function ruined_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date())
    );

    return sprintf(
        '<span class="posted-on">%s</span>',
        $time_string
    );
}

/**
 * Display the post author with avatar and name
 */
function ruined_posted_by() {
    return sprintf(
        '<div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                %s
            </div>
            <div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">%s</span>
                <div class="flex space-x-1 text-sm text-gray-500 dark:text-gray-400">
                    <time datetime="%s">%s</time>
                    <span aria-hidden="true">&middot;</span>
                    <span>%s</span>
                </div>
            </div>
        </div>',
        get_avatar(get_the_author_meta('ID'), 40, '', get_the_author(), ['class' => 'h-10 w-10 rounded-full']),
        esc_html(get_the_author()),
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date()),
        ruined_reading_time()
    );
}
