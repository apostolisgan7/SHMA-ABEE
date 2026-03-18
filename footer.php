</main>

<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-upper">
            <div class="footer-cta">
                <span class="subtitle">• ΟΙ ΥΠΗΡΕΣΙΕΣ ΜΑΣ</span>
                <h2 class="footer-title">The latest news,<br>articles, and resources.</h2>
            </div>
            <div class="footer-newsletter">
                <span class="subtitle">• YOUR EMAIL</span>
                <div class="newsletter-wrapper">
                    <form class="newsletter-form">
                        <input type="email" placeholder="Enter your email" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
                <p class="copyright-top">Copyright © <?php echo date('Y'); ?> ΣΗΜΑ Α.Β.Ε.Ε. - All Rights Reserved</p>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-main">
            <div class="footer-brand">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <img src="path/to/your/logo-black.png" alt="ΣΗΜΑ" class="footer-logo">
                    </a>
                <?php endif; ?>
            </div>

            <div class="footer-links-grid">
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

        <div class="footer-bottom">
            <div class="footer-copyright">
                Copyright © <?php echo date('Y'); ?> ΣΗΜΑ Α.Β.Ε.Ε. - All Rights Reserved
            </div>

            <div class="footer-partners">
                <img style="mix-blend-mode: difference"
                     src="<?php echo get_template_directory_uri(); ?>/src/img/bottom.png" alt="Partner 1">
                <a href="#" id="back-to-top" class="back-to-top">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 15l-6-6-6 6"/>
                    </svg>
                </a>
            </div>


        </div>
    </div>
</footer>

</div><?php wp_footer(); ?>