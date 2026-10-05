<?php
/**
 * Positional variation icons (YITH Color & Label Variations)
 *
 * For the attributes below, the swatch icon depends on the option's POSITION
 * on the product, not on the term: 1st option → icon 1 (Α), 2nd → icon 2 (Β),
 * 3rd → icon 3 (Γ). Whatever the actual values are (1mm/2mm/3mm, Δ.60/Δ.90/…),
 * every product shows the same three icons in the same order.
 *
 * Icons live in src/img/icons/variations/{prefix}-{n}.svg (or .png).
 * If a file is missing, the icon set in Products → Attributes is used instead.
 */

if (!defined('ABSPATH')) {
    exit;
}

// taxonomy => file prefix
const RUINED_POSITIONAL_ICON_ATTRIBUTES = [
    'pa_pachos-alouminiou' => 'pachos',
    'pa_dimension'         => 'diastasi',
];

/**
 * Icon URL for an attribute position (1-based), or null if no file exists.
 */
function ruined_positional_icon_url(string $prefix, int $position): ?string {
    foreach (['svg', 'png'] as $ext) {
        $relative = "/src/img/icons/variations/{$prefix}-{$position}.{$ext}";
        if (file_exists(get_template_directory() . $relative)) {
            return get_template_directory_uri() . $relative;
        }
    }
    return null;
}

/**
 * Term slugs used for variations, in the same order the dropdown/swatches render them.
 */
function ruined_variation_term_order(WC_Product $product, string $taxonomy): array {
    static $cache = [];
    $key = $product->get_id() . '|' . $taxonomy;

    if (!isset($cache[$key])) {
        $used = $product->get_variation_attributes()[$taxonomy] ?? [];
        $all  = wc_get_product_terms($product->get_id(), $taxonomy, ['fields' => 'slugs']);
        $cache[$key] = array_values(array_intersect($all, $used));
    }

    return $cache[$key];
}

// Runs after YITH's per-product override (priority 10) so the positional icon wins.
add_filter('yith_wccl_create_custom_attributes_term_attr', function ($attr, $taxonomy, $term, $product) {
    if (!isset(RUINED_POSITIONAL_ICON_ATTRIBUTES[$taxonomy]) || !$product instanceof WC_Product) {
        return $attr;
    }

    $index = array_search($term->slug, ruined_variation_term_order($product, $taxonomy), true);
    if ($index === false) {
        return $attr;
    }

    $url = ruined_positional_icon_url(RUINED_POSITIONAL_ICON_ATTRIBUTES[$taxonomy], $index + 1);
    if ($url) {
        $attr['type']  = 'image';
        $attr['value'] = $url;
    }

    return $attr;
}, 20, 4);
