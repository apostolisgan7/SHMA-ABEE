</main>

<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-upper">
            <div class="footer-cta">
                <h2 class="footer-title" data-animate="title-reveal"><?php echo wp_kses_post( __( 'Κατεύθυνσή μας: <br>Η Οδική Ασφάλεια', 'ruined' ) ); ?></h2>
            </div>
            <div class="footer-newsletter" data-animate="fade-up" data-animate-delay="0.2">
                <span class="subtitle">• <?php esc_html_e( 'Εγγραφή στο newsletter', 'ruined' ); ?></span>
                <div class="newsletter-wrapper">
                    <form class="newsletter-form">
                        <input type="email" placeholder="<?php esc_attr_e( 'Εισάγετε το email σας', 'ruined' ); ?>" required>
                        <button type="submit"><?php esc_html_e( 'Εγγραφή', 'ruined' ); ?></button>
                    </form>
                </div>
                <p class="copyright-top">Copyright © <?php echo date('Y'); ?> ΣΗΜΑ Α.Β.Ε.Ε. - <?php esc_html_e( 'Με Επιφύλαξη Παντός Δικαιώματος', 'ruined' ); ?></p>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-main">
            <div class="footer-brand" data-animate="fade-up">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php endif; ?>

                <?php if (has_nav_menu('footer-social')) :
                    $footer_social_menu_id = get_nav_menu_locations()['footer-social'];
                    $footer_social_items   = wp_get_nav_menu_items($footer_social_menu_id);
                    if ($footer_social_items) : ?>
                        <div class="footer-social">
                            <p class="footer-social-title"><?php esc_html_e('Ακολουθήστε μας', 'ruined'); ?></p>
                            <div class="footer-social-icons">
                                <?php foreach ($footer_social_items as $social_item) :
                                    $icon = shma_get_social_icon($social_item->url);
                                    if (!$icon) continue; ?>
                                    <a href="<?php echo esc_url($social_item->url); ?>" class="footer-social-icon" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social_item->title); ?>">
                                        <?php echo $icon; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif;
                endif; ?>
            </div>

            <div class="footer-links-grid" data-animate="stagger-fade" data-animate-stagger="0.12">
                <?php
                $footer_locations = ['footer-col-1', 'footer-col-2', 'footer-col-3', 'footer-col-4'];
                foreach ($footer_locations as $location) :
                    if (has_nav_menu($location)) :
                        echo '<div class="footer-col">';
                        $menu_obj = wp_get_nav_menu_object(get_nav_menu_locations()[$location]);
                        echo '<h4 class="col-title">' . esc_html($menu_obj->name) . '</h4>';
                        wp_nav_menu([
                                'theme_location' => $location,
                                'container' => false,
                                'menu_class' => 'footer-list',
                                'fallback_cb' => false,
                                'depth' => 1
                        ]);
                        echo '</div>';
                    endif;
                endforeach;
                ?>
            </div>
        </div>

        <div class="footer-bottom" data-animate="fade-in" data-animate-delay="0.25" data-animate-start="top bottom">
            <div class="footer-copyright">
                Copyright © <?php echo date('Y'); ?> ΣΗΜΑ Α.Β.Ε.Ε. - <?php esc_html_e( 'Με Επιφύλαξη Παντός Δικαιώματος', 'ruined' ); ?>
            </div>

            <div class="footer-partners">
                <a href="/protypa-iso-poiotita-ypiresion/">
                <img style="mix-blend-mode: difference"
                     src="<?php echo get_template_directory_uri(); ?>/src/img/Certifications_Shma.png" alt="ISO">
                </a>
            </div>

            <button id="back-to-top" class="rv-back-to-top" aria-label="<?php esc_attr_e('Επιστροφή στην κορυφή', 'ruined'); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 15l-6-6-6 6"/>
                </svg>
            </button>
        </div>
    </div>
</footer>




</div><?php wp_footer(); ?>