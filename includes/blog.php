<?php
/**
 * Blog (posts) — listing, AJAX category tabs, load more, featured post.
 *
 * @package Ruined
 */

if (!defined('ABSPATH')) {
    exit;
}

const RV_BLOG_PER_PAGE = 9;

/**
 * Reading time in minutes (min 1).
 */
function rv_reading_time($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $words = str_word_count(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post_id))));
    return max(1, (int) ceil($words / 200));
}

/**
 * Main blog queries use the same page size as the AJAX load more.
 */
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && ($query->is_home() || $query->is_category())) {
        $query->set('posts_per_page', RV_BLOG_PER_PAGE);
    }
});

/**
 * ACF: featured post picker on the "Posts page" (Settings → Reading).
 * Registered in code (not acf-json) so the JSON sync can't overwrite it.
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'      => 'group_rv_blog_featured',
        'title'    => 'Blog – Featured Post',
        'fields'   => [
            [
                'key'           => 'field_rv_blog_featured_post',
                'label'         => 'Προβεβλημένο άρθρο (hero)',
                'name'          => 'blog_featured_post',
                'type'          => 'post_object',
                'instructions'  => 'Αν μείνει κενό, εμφανίζεται το πιο πρόσφατο άρθρο.',
                'post_type'     => ['post'],
                'return_format' => 'id',
                'allow_null'    => 1,
                'ui'            => 1,
            ],
        ],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'posts_page']]],
    ]);
});

/**
 * Featured post ID for the blog hero (ACF choice → latest post).
 */
function rv_get_blog_featured_post_id() {
    $page_id = (int) get_option('page_for_posts');
    $id = ($page_id && function_exists('get_field')) ? (int) get_field('blog_featured_post', $page_id) : 0;

    if ($id && get_post_status($id) === 'publish') {
        return $id;
    }

    $latest = get_posts(['numberposts' => 1, 'post_status' => 'publish', 'fields' => 'ids', 'ignore_sticky_posts' => true]);
    return $latest ? (int) $latest[0] : 0;
}

/**
 * URL of the posts page (fallback: home).
 */
function rv_get_blog_url() {
    $page_id = (int) get_option('page_for_posts');
    return $page_id ? get_permalink($page_id) : home_url('/');
}

/**
 * Render a card for the current post in the loop.
 */
function rv_render_post_card() {
    get_template_part('template-parts/items/item', 'post');
}

/**
 * AJAX: filter by category / load more.
 */
add_action('wp_ajax_rv_filter_posts', 'rv_filter_posts');
add_action('wp_ajax_nopriv_rv_filter_posts', 'rv_filter_posts');

function rv_filter_posts() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rv_ajax_nonce')) {
        wp_send_json_error('Security check failed', 403);
    }

    $page = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
    $cat  = isset($_POST['cat']) ? sanitize_title(wp_unslash($_POST['cat'])) : '';

    $args = [
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => RV_BLOG_PER_PAGE,
        'paged'               => $page,
        'ignore_sticky_posts' => true,
    ];
    if ($cat !== '') {
        $args['category_name'] = $cat;
    }

    $query = new WP_Query($args);

    ob_start();
    while ($query->have_posts()) {
        $query->the_post();
        rv_render_post_card();
    }
    wp_reset_postdata();

    wp_send_json_success([
        'html'  => ob_get_clean(),
        'max'   => (int) $query->max_num_pages,
        'found' => (int) $query->found_posts,
    ]);
}
