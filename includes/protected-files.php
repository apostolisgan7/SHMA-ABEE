<?php
/**
 * Members-only PDFs.
 *
 * nginx rewrites /wp-content/uploads/**.pdf to /?rv_file=<path inside uploads>
 * (see docs / hosting note), so this gate runs for direct links too.
 * Guests are sent to the home page; logged-in users get the file.
 */

defined('ABSPATH') || exit;

add_action('template_redirect', function () {
    if (!isset($_GET['rv_file'])) return;

    // Never let a page/CDN cache store this response: a cached guest redirect would be served to members too.
    if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);

    if (!is_user_logged_in()) {
        nocache_headers();
        wp_safe_redirect(home_url('/'));
        exit;
    }

    $base = realpath(wp_get_upload_dir()['basedir']);
    $path = realpath($base . '/' . ltrim(wp_unslash($_GET['rv_file']), '/'));

    // realpath() also blocks ../ traversal; only PDFs inside uploads are served.
    if (!$path || strpos($path, $base . DIRECTORY_SEPARATOR) !== 0
        || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'pdf' || !is_file($path)) {
        status_header(404);
        nocache_headers();
        exit;
    }

    nocache_headers();
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($path) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}, 0);

/**
 * Attributes for a download link: real URL for members, auth-modal trigger for guests
 * (the PDF URL is then not in the HTML at all).
 */
function rv_file_link_attrs($url, $new_tab = true) {
    if (is_user_logged_in()) {
        return 'href="' . esc_url($url) . '"' . ($new_tab ? ' target="_blank" rel="noopener"' : '');
    }
    return 'href="#" class="js-auth-modal-trigger"';
}
