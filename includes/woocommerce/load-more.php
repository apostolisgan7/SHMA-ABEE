<?php
// ========================
// LOAD MORE (CONTEXT AWARE)
// ========================

// Αφαιρούμε το default pagination
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);

/**
 * Helper function: Εμφανίζει το κουμπί μόνο όταν υπάρχουν περισσότερες σελίδες.
 */
function rv_render_load_more_button() {
    global $wp_query;

    $max_pages = $wp_query->max_num_pages;

    // Αν έχουμε μόνο 1 σελίδα (π.χ. μετά από φιλτράρισμα), δεν τυπώνουμε τίποτα
    if ( $max_pages <= 1 ) return;

    ?>
    <div class="rv-load-more-wrap">
        <?php
        rv_button_arrow([
            'text' => 'Φόρτωσε περισσότερα',
            'url' => '#',
            'target' => '_self',
            'variant' => 'black',
            'icon_position' => 'left',
            'class' => 'rv-load-more',
        ]);
        ?>
        <div class="rv-load-more-data"
             data-max="<?php echo esc_attr($max_pages); ?>"
             data-total="<?php echo esc_attr($wp_query->found_posts); ?>"
             data-query='<?php
             $args = $wp_query->query_vars;
             unset($args['paged']);
             echo esc_attr(json_encode($args));
             ?>'>
        </div>
    </div>
    <?php
}

// AJAX Handlers
add_action('wp_ajax_rv_load_more_products', 'rv_load_more_products');
add_action('wp_ajax_nopriv_rv_load_more_products', 'rv_load_more_products');

function rv_load_more_products() {
    $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $query_vars = isset($_POST['query']) ? json_decode(stripslashes($_POST['query']), true) : [];

    if (empty($query_vars)) wp_die();

    // Setup Query Args
    $query_vars['paged'] = $page;
    $query_vars['post_status'] = 'publish';
    $query_vars['post_type'] = 'product';

    // Καθαρισμός για αποφυγή συγκρούσεων
    unset($query_vars['wc_query']);
    unset($query_vars['lazy_load_term_meta']);

    $query = new WP_Query($query_vars);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
    }

    wp_reset_postdata();
    wp_die();
}