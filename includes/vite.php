<?php

// --- Constants ---
define('DIST_DIR', 'dist');
define('DIST_URI', get_template_directory_uri() . '/' . DIST_DIR);
define('DIST_PATH', get_template_directory() . '/' . DIST_DIR);
define('VITE_SERVER', 'http://localhost:5173');

// Check if we are in development mode by the absence of the manifest file.
define('IS_VITE_DEVELOPMENT', !file_exists(DIST_PATH . '/.vite/manifest.json'));

// --- Asset Loading ---
function ruined_asset_loader() {
    if (IS_VITE_DEVELOPMENT) {
        return; // In dev, Vite handles everything via the head hook.
    }

    $manifest_path = DIST_PATH . '/.vite/manifest.json';
    if (!file_exists($manifest_path)) return;

    $manifest = json_decode(file_get_contents($manifest_path), true);
    $entry = 'src/js/main.js';

    // Enqueue the main JS file
    if (isset($manifest[$entry]['file'])) {
        wp_enqueue_script('ruined-main-js', DIST_URI . '/' . $manifest[$entry]['file'], [], null, true);
    }

    // Enqueue the CSS files for the main JS entry
    if (isset($manifest[$entry]['css'])) {
        foreach ($manifest[$entry]['css'] as $css_file) {
            wp_enqueue_style('ruined-main-css', DIST_URI . '/' . $css_file, [], null);
        }
    }
}
add_action('wp_enqueue_scripts', 'ruined_asset_loader');

// --- Vite Dev Server Scripts ---
function vite_head_scripts() {
    if (IS_VITE_DEVELOPMENT) {
        echo '<script type="module" crossorigin src="' . VITE_SERVER . '/@vite/client"></script>';
        echo '<script type="module" src="' . VITE_SERVER . '/src/js/main.js"></script>';
    }
}
add_action('wp_head', 'vite_head_scripts');
