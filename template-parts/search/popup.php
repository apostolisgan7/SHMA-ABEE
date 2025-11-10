
<div class="search-overlay"></div>

<div class="search-popup" aria-hidden="true">
    <div class="search-container" data-lenis-prevent>
        <div class="search-header">
            <div class="site-logo"><?php the_custom_logo();?></div>
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
                    <a href="/?s=t-shirt" class="search-tag">T-Shirts</a>
                    <a href="/?s=hoodie" class="search-tag">Hoodies</a>
                    <a href="/?s=jeans" class="search-tag">Jeans</a>
                </div>
            </div>
        </div>
        <div class="search-content">
            <aside class="search-help">
                <h4><?php esc_html_e('Βοήθεια', 'ruined'); ?></h4>
                <ul>
                    <li><a href="#"><?php esc_html_e('Συχνές Ερωτήσεις', 'ruined'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Επικοινωνία', 'ruined'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Ο Λογαριασμός μου', 'ruined'); ?></a></li>
                </ul>
            </aside>

            <!-- ΕΔΩ ΘΑ ΕΡΘΟΥΝ ΤΑ RESULTS του FiboSearch -->
            <section class="search-results" id="rv-fibo-results"></section>
        </div>
    </div>
</div>
