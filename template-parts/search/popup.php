<div class="search-overlay"></div>

<div class="search-popup" aria-hidden="true">
    <div class="search-container" data-lenis-prevent>
        <div class="search-header">
            <div class="site-logo"><?php the_custom_logo(); ?></div>
            <button type="button" class="search-close" aria-label="<?php esc_attr_e('Close search', 'ruined'); ?>">
                &times;
            </button>
        </div>

        <div>
            <div class="rv-fibo-form">
                <?php echo do_shortcode('[fibosearch layout="block" submit="0" results="#rv-fibo-results"]'); ?>
            </div>

            <div class="popular-searches">
                <div class="search-tags">
                    <a href="#" class="search-tag">Yποχρεωτική Διακοπή Πορείας</a>
                    <a href="#" class="search-tag">Διακοπή Πορείας</a>
                    <a href="#" class="search-tag">Μπαριέρα</a>
                </div>
            </div>
        </div>
        <div class="search-content">
            <aside class="search-help">
                <div class="help_top">
                    <h4><?php esc_html_e('Βοήθεια', 'ruined'); ?></h4>
                    <ul>
                        <li><a href="#"><?php esc_html_e('Συχνές Ερωτήσεις', 'ruined'); ?></a></li>
                        <li><a href="#"><?php esc_html_e('Επικοινωνία', 'ruined'); ?></a></li>
                        <li><a href="#"><?php esc_html_e('Ο Λογαριασμός μου', 'ruined'); ?></a></li>
                    </ul>
                </div>
                <div class="help_bottom">
                    <span><?php esc_html_e('shma abee © 2025', 'ruined'); ?></span>
                </div>
            </aside>

            <div class="search-results-wrapper">
                <h4 class="section-title"><?php esc_html_e('Αποτελέσματα Προϊόντων', 'ruined'); ?></h4>
                <div id="rv-default-products" class="rv-products-grid">
                    <div class="rv-grid-inner">
                        <?php
                        $args = array(
                                'post_type' => 'product',
                                'posts_per_page' => 6,
                                'meta_key' => 'total_sales',
                                'orderby' => 'meta_value_num',
                        );
                        $loop = new WP_Query($args);
                        if ($loop->have_posts()) :
                            while ($loop->have_posts()) : $loop->the_post();
                                global $product;
                                get_template_part('template-parts/items/item', 'product-search');
                            endwhile;
                        endif;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>

                <section class="search-results" id="rv-fibo-results" style="display:none;"></section>
            </div>
        </div>
    </div>
</div>
