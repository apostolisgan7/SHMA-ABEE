<?php
/**
 * Συγχρονισμός του "Catalog Menu" με τις κατηγορίες προϊόντων (WooCommerce product_cat).
 *
 * Δομή: parent categories (top level) + άμεσα children τους. Grandchildren
 * και βαθύτερα επίπεδα αγνοούνται, ώστε να ταιριάζει με template-parts/menus/catalog-menu.php
 * που διαβάζει μόνο menu_item_parent === 0 (parents) και ένα επίπεδο children.
 */

function ruined_get_catalog_menu_id() {
    $locations = get_nav_menu_locations();

    return isset( $locations['catalog-menu'] ) ? (int) $locations['catalog-menu'] : 0;
}

function ruined_sync_catalog_menu_categories() {
    $menu_id = ruined_get_catalog_menu_id();

    if ( ! $menu_id ) {
        return new WP_Error(
            'ruined_no_catalog_menu',
            __( 'Δεν έχει ανατεθεί menu στο location "Catalog Menu" (Εμφάνιση → Μενού).', 'ruined' )
        );
    }

    /*
     * Ευρετήριο υπαρχόντων menu items ανά term_id, μόνο για product_cat items.
     * Έτσι γίνεται update in place αντί για delete+recreate, και δεν χάνονται
     * meta (π.χ. το ACF "menu_icon") που είναι δεμένα πάνω στο post ID του item.
     * Τυχόν custom links στο ίδιο menu δεν αγγίζονται καθόλου, αφού δεν μπαίνουν
     * σε αυτό το ευρετήριο.
     */
    $existing_by_term = array();

    foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
        if ( 'taxonomy' === $item->type && 'product_cat' === $item->object ) {
            $existing_by_term[ (int) $item->object_id ] = $item;
        }
    }

    $parent_terms = get_terms(
        array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => 0,
            'orderby'    => 'menu_order',
            'order'      => 'ASC',
        )
    );

    if ( is_wp_error( $parent_terms ) ) {
        return $parent_terms;
    }

    $seen_term_ids = array();
    $position      = 1;

    foreach ( $parent_terms as $parent_term ) {

        $parent_menu_item_id = ruined_upsert_catalog_menu_item(
            $menu_id,
            $parent_term,
            0,
            $position,
            $existing_by_term
        );

        if ( is_wp_error( $parent_menu_item_id ) ) {
            continue;
        }

        $seen_term_ids[] = $parent_term->term_id;
        $position++;

        $child_terms = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'parent'     => $parent_term->term_id,
                'orderby'    => 'menu_order',
                'order'      => 'ASC',
            )
        );

        if ( is_wp_error( $child_terms ) || empty( $child_terms ) ) {
            continue;
        }

        foreach ( $child_terms as $child_term ) {

            $child_menu_item_id = ruined_upsert_catalog_menu_item(
                $menu_id,
                $child_term,
                $parent_menu_item_id,
                $position,
                $existing_by_term
            );

            if ( is_wp_error( $child_menu_item_id ) ) {
                continue;
            }

            $seen_term_ids[] = $child_term->term_id;
            $position++;
        }
    }

    /*
     * Ό,τι category item είχε μείνει από πριν αλλά δεν ξαναβρέθηκε τώρα
     * (διαγράφηκε, άδειασε από προϊόντα, ή μετακινήθηκε βαθύτερα από level 2)
     * αφαιρείται.
     */
    foreach ( $existing_by_term as $term_id => $item ) {
        if ( ! in_array( $term_id, $seen_term_ids, true ) ) {
            wp_delete_post( $item->ID, true );
        }
    }

    return true;
}

function ruined_upsert_catalog_menu_item( $menu_id, $term, $parent_menu_item_id, $position, $existing_by_term ) {

    $existing_item_id = isset( $existing_by_term[ $term->term_id ] )
        ? $existing_by_term[ $term->term_id ]->ID
        : 0;

    return wp_update_nav_menu_item(
        $menu_id,
        $existing_item_id,
        array(
            'menu-item-title'     => $term->name,
            'menu-item-object'    => 'product_cat',
            'menu-item-object-id' => $term->term_id,
            'menu-item-type'      => 'taxonomy',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $parent_menu_item_id,
            'menu-item-position'  => $position,
        )
    );
}

/**
 * Χειροκίνητο sync από το κουμπί στη σελίδα Εμφάνιση → Μενού.
 */
add_action( 'admin_post_ruined_sync_catalog_menu', 'ruined_handle_catalog_menu_sync_request' );

function ruined_handle_catalog_menu_sync_request() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Δεν έχεις δικαίωμα για αυτή την ενέργεια.', 'ruined' ), 403 );
    }

    check_admin_referer( 'ruined_sync_catalog_menu' );

    $result = ruined_sync_catalog_menu_categories();

    $redirect_args = is_wp_error( $result )
        ? array(
            'catalog_menu_sync'         => 'error',
            'catalog_menu_sync_message' => rawurlencode( $result->get_error_message() ),
        )
        : array( 'catalog_menu_sync' => 'success' );

    wp_safe_redirect( add_query_arg( $redirect_args, admin_url( 'nav-menus.php' ) ) );
    exit;
}

/*
 * Meta box στο αριστερό column της σελίδας Menus (μαζί με τα "Pages",
 * "Custom Links", "Categories"), αντί για admin_notices — έτσι το κουμπί
 * εμφανίζεται πάντα κανονικά στη σελίδα, ό,τι κι αν κάνει κάποιο plugin
 * (π.χ. ASE) που μαζεύει/κρύβει τα admin notices.
 */
add_action( 'admin_head-nav-menus.php', 'ruined_register_catalog_menu_sync_metabox' );

function ruined_register_catalog_menu_sync_metabox() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    add_meta_box(
        'ruined-catalog-menu-sync',
        __( 'Catalog Menu Sync', 'ruined' ),
        'ruined_render_catalog_menu_sync_metabox',
        'nav-menus',
        'side',
        'default'
    );
}

function ruined_render_catalog_menu_sync_metabox() {

    $status = isset( $_GET['catalog_menu_sync'] ) ? sanitize_text_field( wp_unslash( $_GET['catalog_menu_sync'] ) ) : '';
    ?>
    <p><?php esc_html_e( 'Συγχρονισμός κατηγοριών προϊόντων (parent + άμεσα children) στο menu που είναι ανατεθειμένο στο location "Catalog Menu".', 'ruined' ); ?></p>

    <?php if ( 'success' === $status ) : ?>
        <p style="color:#008a20;"><?php esc_html_e( 'Ο συγχρονισμός ολοκληρώθηκε.', 'ruined' ); ?></p>
    <?php elseif ( 'error' === $status ) : ?>
        <?php
        $message = isset( $_GET['catalog_menu_sync_message'] )
            ? sanitize_text_field( wp_unslash( $_GET['catalog_menu_sync_message'] ) )
            : __( 'Παρουσιάστηκε σφάλμα.', 'ruined' );
        ?>
        <p style="color:#b32d2e;"><?php echo esc_html( $message ); ?></p>
    <?php endif; ?>

    <p>
        <a
            href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=ruined_sync_catalog_menu' ), 'ruined_sync_catalog_menu' ) ); ?>"
            class="button button-primary"
        >
            <?php esc_html_e( 'Sync κατηγοριών τώρα', 'ruined' ); ?>
        </a>
    </p>
    <?php
}
