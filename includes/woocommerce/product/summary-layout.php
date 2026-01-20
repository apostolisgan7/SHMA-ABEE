<?php
defined('ABSPATH') || exit;
global $product;

$is_variable = $product->is_type('variable');

$has_variations = false;
if ($is_variable) {
    $variation_attributes = $product->get_variation_attributes();
    $has_variations = !empty(array_filter($variation_attributes));
}
?>

<div class="rv-summary-accordion" x-data="{ open: 'tech' }">

    <!-- ΠΡΟΪΟΝΤΑ ΙΔΙΑΣ ΚΑΤΗΓΟΡΙΑΣ -->
    <?php
    $same_products = get_field('same_cat_products', get_the_ID());

    if ($same_products) :
        ?>
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

    <!-- ΤΕΧΝΙΚΑ ΧΑΡΑΚΤΗΡΙΣΤΙΚΑ (ΜΟΝΟ ΑΝ ΥΠΑΡΧΟΥΝ VARIATIONS) -->
    <?php if ($has_variations) : ?>
        <div class="rv-accordion-item">
            <button
                    @click="open = open === 'tech' ? null : 'tech'"
                    :aria-expanded="open === 'tech'"
            >
                <span>Τεχνικά Χαρακτηριστικά</span>
                <div class="rv-accordion-arrow">
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.911796 5.62592L5.62484 0.911926L10.3379 5.62592" stroke="black"
                              stroke-width="1.82386"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </button>

            <div class="main_accodion_content" x-show="open === 'tech'" x-collapse>
                <?php
                woocommerce_variable_add_to_cart();
                ?>

            </div>
        </div>
    <?php endif; ?>



    <?php if (!$is_variable) : ?>
        <?php
        woocommerce_template_single_add_to_cart();
        ?>
    <?php endif; ?>
</div>
