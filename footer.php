    </main>

    <footer id="colophon" class="site-footer bg-gray-800 text-gray-300 mt-auto border-t border-gray-700">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Footer Widget 1 -->
                <div class="footer-widget">
                    <h3 class="text-xl font-bold text-white mb-4"><?php bloginfo('name'); ?></h3>
                    <p class="text-gray-400">A modern WordPress theme with beautiful UI components.</p>
                </div>
                
                <!-- Footer Widget 2 -->
                <div class="footer-widget">
                    <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'menu_class'     => 'space-y-2',
                        'container'      => false,
                        'link_class'     => 'block text-gray-400 hover:text-white transition-colors',
                    ]);
                    ?>
                </div>
                
                <!-- Footer Widget 3 -->
                <div class="footer-widget">
                    <h4 class="text-lg font-semibold text-white mb-4">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Subscribe to our newsletter for updates.</p>
                    <form class="flex flex-col space-y-2">
                        <input type="email" placeholder="Your email" class="px-4 py-2 rounded-md bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md transition-colors">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-500 text-sm">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<!-- Mobile menu toggle script -->
<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>

<?php wp_footer(); ?>

<!-- WooCommerce Notification -->
<div class="wc-notification" aria-live="polite">
    <div class="wc-notification__content"></div>
</div>

</body>
</html>
