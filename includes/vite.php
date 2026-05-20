<?php

define('DIST_DIR',  'dist');
define('DIST_URI',  get_template_directory_uri() . '/' . DIST_DIR);
define('DIST_PATH', get_template_directory() . '/' . DIST_DIR);
define('VITE_SERVER', 'http://localhost:5173');

define('IS_VITE_DEVELOPMENT', !file_exists(DIST_PATH . '/.vite/manifest.json'));

/* -------------------------------------------------------
   Helper: enqueue one Vite entry from manifest
------------------------------------------------------- */
function ruined_enqueue_entry(string $entry_key, string $handle, array $deps = []): void {
    static $manifest = null;
    if ($manifest === null) {
        $path = DIST_PATH . '/.vite/manifest.json';
        $manifest = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }

    if (!isset($manifest[$entry_key])) return;
    $entry = $manifest[$entry_key];

    // JS
    wp_register_script($handle, DIST_URI . '/' . $entry['file'], $deps, null, [
        'in_footer' => true,
        'strategy'  => 'defer',
    ]);
    wp_enqueue_script($handle);

    // CSS bundled with this entry
    if (!empty($entry['css'])) {
        foreach ($entry['css'] as $i => $css_file) {
            wp_enqueue_style($handle . '-css-' . $i, DIST_URI . '/' . $css_file, [], null);
        }
    }
}

/* -------------------------------------------------------
   type="module" filter — covers all theme script handles
------------------------------------------------------- */
add_filter('script_loader_tag', function (string $tag, string $handle, string $src): string {
    $module_handles = ['ruined-main', 'ruined-single-product', 'ruined-shop'];
    if (in_array($handle, $module_handles, true)) {
        return '<script type="module" src="' . esc_url($src) . '" id="' . esc_attr($handle) . '-js"></script>';
    }
    return $tag;
}, 10, 3);

/* -------------------------------------------------------
   Production asset loader
------------------------------------------------------- */
function ruined_asset_loader(): void {
    if (IS_VITE_DEVELOPMENT) return;

    // main.js — always
    ruined_enqueue_entry('src/js/main.js', 'ruined-main');

    wp_localize_script('ruined-main', 'rv_globals', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('rv_ajax_nonce'),
    ]);

    // single-product.js — only on single product pages
    if (is_singular('product')) {
        ruined_enqueue_entry('src/js/single-product.js', 'ruined-single-product');
    }

    // shop.js — on shop, category, tag archives
    if (is_shop() || is_product_category() || is_product_tag()) {
        ruined_enqueue_entry('src/js/shop.js', 'ruined-shop');
    }
}
add_action('wp_enqueue_scripts', 'ruined_asset_loader');

/* -------------------------------------------------------
   Dev mode — Vite dev server
------------------------------------------------------- */
function vite_head_scripts(): void {
    if (!IS_VITE_DEVELOPMENT) return;

    echo '<script type="module" src="' . esc_url(VITE_SERVER) . '/@vite/client"></script>';
    echo '<script type="module" src="' . esc_url(VITE_SERVER) . '/src/js/main.js"></script>';

    if (is_singular('product')) {
        echo '<script type="module" src="' . esc_url(VITE_SERVER) . '/src/js/single-product.js"></script>';
    }
    if (is_shop() || is_product_category() || is_product_tag()) {
        echo '<script type="module" src="' . esc_url(VITE_SERVER) . '/src/js/shop.js"></script>';
    }

    $globals = wp_json_encode([
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('rv_ajax_nonce'),
    ]);
    echo '<script>window.rv_globals = ' . $globals . ';</script>';
}
add_action('wp_head', 'vite_head_scripts');
