<?php
/**
 * "Services" archive consolidation.
 *
 * The real services listing lives on the manually built Page at /ypiresies/
 * (Template Name: Services, filled in with ACF). The "service" CPT archive
 * (/service/) and any stray Page using the same template have no ACF content
 * of their own, so they render empty. Until that content is rebuilt on the
 * CPT archive itself, send visitors and breadcrumbs to the page that actually
 * has content.
 *
 * @package Ruined
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Get the canonical services Page (/ypiresies/), if it exists.
 *
 * @return WP_Post|null
 */
function ruined_get_services_page() {
    static $page = null;
    static $looked_up = false;

    if (!$looked_up) {
        $page = get_page_by_path('ypiresies');
        $looked_up = true;
    }

    return $page;
}

/**
 * Redirect the empty service archive/page to the real /ypiresies/ page.
 */
add_action('template_redirect', function () {
    $services_page = ruined_get_services_page();

    if (!$services_page) {
        return;
    }

    $is_empty_archive = is_post_type_archive('service');
    $is_duplicate_page = is_page()
        && get_page_template_slug() === 'archive-service.php'
        && get_queried_object_id() !== $services_page->ID;

    if ($is_empty_archive || $is_duplicate_page) {
        wp_safe_redirect(get_permalink($services_page), 301);
        exit;
    }
});

/**
 * Point the "Services" breadcrumb crumb to /ypiresies/ instead of the
 * empty /service/ archive link that Rank Math builds from the CPT rewrite slug,
 * and swap its label to Greek — the "service" CPT labels (ACF JSON) are English
 * ("Services"), and that raw label never passes through gettext, so
 * includes/translations.php can't catch it.
 */
add_filter('rank_math/frontend/breadcrumb/items', function ($crumbs) {
    $services_page = ruined_get_services_page();

    if (!$services_page) {
        return $crumbs;
    }

    $archive_link = get_post_type_archive_link('service');
    $services_url = get_permalink($services_page);

    foreach ($crumbs as $index => $crumb) {
        if ($archive_link && isset($crumb[1]) && untrailingslashit($crumb[1]) === untrailingslashit($archive_link)) {
            $crumbs[$index][0] = 'Υπηρεσίες';
            $crumbs[$index][1] = $services_url;
        }
    }

    return $crumbs;
});
