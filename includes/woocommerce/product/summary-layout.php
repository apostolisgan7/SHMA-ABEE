<?php
defined('ABSPATH') || exit;
global $product;

$is_variable  = $product->is_type('variable');
$same_products = get_field('same_cat_products', get_the_ID());

$has_variations = false;
if ($is_variable) {
    $variation_attributes = $product->get_variation_attributes();
    $has_variations = !empty(array_filter($variation_attributes));
}

$has_simple_attributes = false;
if (!$is_variable) {
    foreach ($product->get_attributes() as $attribute) {
        if (!$attribute->get_visible()) continue;
        if ($attribute->is_taxonomy()) {
            $terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
            if (!empty($terms)) { $has_simple_attributes = true; break; }
        } else {
            if (!empty($attribute->get_options())) { $has_simple_attributes = true; break; }
        }
    }
}

$default_open = $is_variable ? 'tech' : ($has_simple_attributes ? 'tech' : ($same_products ? 'related' : 'null'));
?>

<div class="rv-summary-accordion" x-data="{ open: '<?php echo $default_open; ?>' }">

    <!-- ΠΡΟΪΟΝΤΑ ΙΔΙΑΣ ΚΑΤΗΓΟΡΙΑΣ -->
    <?php if ($same_products) : ?>
        <div class="rv-accordion-item">
            <button @click="open = open === 'related' ? null : 'related'"
                    :aria-expanded="open === 'related'">
                <span>Προϊόντα ίδιας κατηγορίας</span>
                <div class="rv-accordion-arrow">
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.911796 5.62592L5.62484 0.911926L10.3379 5.62592" stroke="black"
                              stroke-width="1.82386"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </button>

            <div class="rv-products--list-only" x-show="open === 'related'" x-collapse>
                <ul class="products">
                    <?php
                    foreach ($same_products as $post_obj) {
                        set_query_var('product_post', $post_obj);
                        get_template_part('template-parts/items/item-product');
                    }
                    ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- ΤΕΧΝΙΚΑ ΧΑΡΑΚΤΗΡΙΣΤΙΚΑ -->
    <?php if ($has_variations) : ?>
        <div class="rv-accordion-item rv-accordion-item--static">
            <div class="rv-accordion-header">
                <span>Τεχνικά Χαρακτηριστικά</span>
                <div class="rv-accordion-arrow">
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.911796 5.62592L5.62484 0.911926L10.3379 5.62592" stroke="black"
                              stroke-width="1.82386"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="main_accodion_content">
                <?php woocommerce_variable_add_to_cart(); ?>
            </div>
        </div>
    <?php elseif ($has_simple_attributes) : ?>
        <div class="rv-accordion-item rv-accordion-item--static">
            <div class="rv-accordion-header">
                <span>Τεχνικά Χαρακτηριστικά</span>
            </div>
            <div class="main_accodion_content">
                <div class="rv-tech-table">
                    <?php foreach ($product->get_attributes() as $attribute) :
                        if (!$attribute->get_visible()) continue;
                        $label = wc_attribute_label($attribute->get_name());
                        if ($attribute->is_taxonomy()) {
                            $terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), ['fields' => 'names']);
                            $value = !empty($terms) ? implode(' &nbsp; ', $terms) : '';
                        } else {
                            $value = !empty($attribute->get_options()) ? implode(' &nbsp; ', $attribute->get_options()) : '';
                        }
                        if (empty($value)) continue;
                    ?>
                        <div class="row">
                            <span><?php echo esc_html($label); ?></span>
                            <strong><?php echo wp_kses_post($value); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$is_variable) : ?>
        <?php woocommerce_template_single_add_to_cart(); ?>
    <?php endif; ?>
</div>
