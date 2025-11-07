<?php
// Exit if accessed directly
if ( ! defined('DGWT_WCAS_FILE') ) exit;

/**
 * $args['results']['products'] ή $args['results']['results'] ανά έκδοση.
 * Παίζουμε safe με δύο checks.
 */
$items = [];
if ( ! empty($args['results']['products']) ) {
    $items = $args['results']['products'];
} elseif ( ! empty($args['results']['results']) ) {
    $items = $args['results']['results'];
}
?>

<div class="rv-results-grid">
    <?php if ( empty($items) ) : ?>
        <p class="rv-no-results"><?php esc_html_e('No products found','ruined'); ?></p>
    <?php else: ?>
        <div class="rv-grid">
            <?php foreach ( $items as $vars ) : ?>
                <?php
                // Κάθε κάρτα προϊόντος – δικό μας template:
                // path: /themes/Ruined/fibosearch/details-panel/product.php
                include locate_template('fibosearch/details-panel/product.php', false, false);
                ?>
            <?php endforeach; ?>
        </div>

        <?php if ( ! empty($args['results']['see_all_url']) && ! empty($args['results']['total']) ) : ?>
            <div class="rv-see-all">
                <a href="<?php echo esc_url( $args['results']['see_all_url'] ); ?>" class="view-all">
                    <?php printf( esc_html__('See all products… (%d)', 'ruined'), (int) $args['results']['total'] ); ?>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
