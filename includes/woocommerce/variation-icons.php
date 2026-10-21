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

// taxonomy => file prefix (Πάχος is "thickness" on live, "pachos-alouminiou" on local)
const RUINED_POSITIONAL_ICON_ATTRIBUTES = [
    'pa_thickness'         => 'pachos',
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
function ruined_variation_term_order(WC_Product_Variable $product, string $taxonomy): array {
    static $cache = [];
    $key = $product->get_id() . '|' . $taxonomy;

    if (!isset($cache[$key])) {
        $used = $product->get_variation_attributes()[$taxonomy] ?? [];
        $all  = wc_get_product_terms($product->get_id(), $taxonomy, ['fields' => 'slugs']);
        $cache[$key] = array_values(array_intersect($all, $used));
    }

    return $cache[$key];
}

/**
 * Dimensions ("110 × 110", "60x90", …) always listed from smallest to largest.
 * Sorts by the numbers in the term, so the positional icons stay consistent too.
 */
add_filter('woocommerce_get_product_terms', function ($terms, $product_id, $taxonomy, $args) {
    if ($taxonomy !== 'pa_dimension' || !is_array($terms) || count($terms) < 2) {
        return $terms;
    }

    $numbers = function ($term) {
        $text = is_object($term) ? $term->name : (string) $term;
        preg_match_all('/\d+(?:[.,]\d+)?/', $text, $m);
        return array_map(fn ($n) => (float) str_replace(',', '.', $n), $m[0]);
    };

    usort($terms, fn ($a, $b) => $numbers($a) <=> $numbers($b));

    return $terms;
}, 10, 4);

/**
 * "Διάσταση" → "Διάσταση (Υ × Μ)" everywhere the label is shown (height first, then length).
 * Done on display only: the stored attribute name stays "Διάσταση", so CSV imports and the
 * ERP bridge keep matching it.
 */
add_filter('woocommerce_attribute_label', function ($label, $name) {
    if ($name === 'pa_dimension' && strpos($label, '(Υ × Μ)') === false) {
        return $label . ' (Υ × Μ)';
    }
    return $label;
}, 10, 2);

// Runs after YITH's per-product override (priority 10) so the positional icon wins.
// YITH also runs this filter for simple products that merely list the attribute;
// only variable products have get_variation_attributes(), so skip the rest.
add_filter('yith_wccl_create_custom_attributes_term_attr', function ($attr, $taxonomy, $term, $product) {
    if (!isset(RUINED_POSITIONAL_ICON_ATTRIBUTES[$taxonomy]) || !$product instanceof WC_Product_Variable) {
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
