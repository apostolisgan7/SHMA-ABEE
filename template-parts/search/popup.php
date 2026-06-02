<div class="search-overlay"></div>

<div class="search-popup" aria-hidden="true">
    <div class="search-container" data-lenis-prevent>
        <div class="search-header">
            <div class="site-logo"><?php the_custom_logo(); ?></div>
            <button type="button" class="search-close" aria-label="<?php esc_attr_e('Close search', 'ruined'); ?>">
                ✕
            </button>
        </div>

        <div>
            <div class="rv-fibo-form">
                <?php echo do_shortcode('[fibosearch layout="block" submit="0" results="#rv-fibo-results"]'); ?>
            </div>

            <div class="popular-searches">
                <div class="search-tags">
                    <?php
                    $tags = get_terms([
                            'taxonomy'   => 'product_tag',
                            'hide_empty' => true,
                    ]);

                    if (!empty($tags) && !is_wp_error($tags)) :
                        foreach ($tags as $tag) :
                            ?>
                            <a
                                    href="<?php echo esc_url(get_term_link($tag)); ?>"
                                    class="search-tag"
                            >
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

        </div>
        <div class="search-content">
            <aside class="search-help">
                <div class="help_top">
                    <h4><?php esc_html_e('Βοήθεια', 'ruined'); ?></h4>
                    <?php
                    $faq_url     = get_permalink(get_page_by_path('faq'))     ?: home_url('/');
                    $contact_url = get_permalink(get_page_by_path('contact')) ?: home_url('/');
                    $account_url = wc_get_page_permalink('myaccount');
                    ?>
                    <ul>
                        <li><a href="<?php echo esc_url($faq_url); ?>"><?php esc_html_e('Συχνές Ερωτήσεις', 'ruined'); ?></a></li>
                        <li><a href="<?php echo esc_url($contact_url); ?>"><?php esc_html_e('Επικοινωνία', 'ruined'); ?></a></li>
                        <li><a href="<?php echo esc_url($account_url); ?>"><?php esc_html_e('Ο Λογαριασμός μου', 'ruined'); ?></a></li>
                    </ul>
                </div>
                <div class="help_bottom">
                    <span>shma abee &copy; <?php echo esc_html(date('Y')); ?></span>
                </div>
            </aside>

            <div class="search-results-wrapper">
                <h4 class="section-title"
                    id="rv-search-title"
                    data-default-title="<?php esc_attr_e('Δημοφιλή Προϊόντα', 'ruined'); ?>"
                    data-results-title="<?php esc_attr_e('Αποτελέσματα Προϊόντων', 'ruined'); ?>">
                    <?php esc_html_e('Δημοφιλή Προϊόντα', 'ruined'); ?>
                </h4>
                <div id="rv-default-products" class="rv-products-grid">
                    <div class="rv-grid-inner">
                        <?php
                        $popular_ids = get_transient('rv_popular_product_ids');
                        if (false === $popular_ids) {
                            $id_query = new WP_Query([
                                'post_type'      => 'product',
                                'post_status'    => 'publish',
                                'posts_per_page' => 6,
                                'meta_key'       => 'total_sales',
                                'orderby'        => 'meta_value_num',
                                'order'          => 'DESC',
                                'fields'         => 'ids',
                            ]);
                            $popular_ids = $id_query->posts ?: [];
                            set_transient('rv_popular_product_ids', $popular_ids, HOUR_IN_SECONDS);
                        }

                        if (!empty($popular_ids)) :
                            $loop = new WP_Query([
                                'post_type'      => 'product',
                                'post_status'    => 'publish',
                                'post__in'       => $popular_ids,
                                'orderby'        => 'post__in',
                                'posts_per_page' => 6,
                            ]);
                            if ($loop->have_posts()) :
                                while ($loop->have_posts()) : $loop->the_post();
                                    global $product;
                                    get_template_part('template-parts/items/item', 'product-search');
                                endwhile;
                                wp_reset_postdata();
                            endif;
                        endif;
                        ?>
                    </div>
                </div>

                <section class="search-results" id="rv-fibo-results"></section>
            </div>
        </div>
    </div>
</div>
