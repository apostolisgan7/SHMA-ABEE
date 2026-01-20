<?php
defined('ABSPATH') || exit;
global $product;
?>
<form class="variations_form cart">
    <div class="rv-product-header">
        <?php
        $terms = get_the_terms($product->get_id(), 'product_cat');
        if ($terms && !is_wp_error($terms)) :
            $primary = $terms[0];
            ?>
            <div class="rv-product-category">
                <?php echo esc_html($primary->name); ?>
            </div>
        <?php endif; ?>

        <!-- TITLE -->
        <h1 class="rv-product-title">
            <?php echo get_the_title(); ?>
        </h1>

        <!-- PRICE + SKU ROW -->
        <div class="rv-product-price-row">
            <div class="summary entry-summary"
                 data-original-price="<?php echo esc_attr($product->get_price_html()); ?>">
                <?php wc_get_template('single-product/price.php'); ?>
            </div>

            <?php
            $stock_status = $product->get_stock_status();

            $status_label = '';
            $status_class = '';

            switch ($stock_status) {
                case 'instock':
                    $status_label = __('In stock', 'ruined');
                    $status_class = 'stock-in';
                    break;

                case 'outofstock':
                    $status_label = __('Out of stock', 'ruined');
                    $status_class = 'stock-out';
                    break;

                case 'onbackorder':
                    $status_label = __('Available on backorder', 'ruined');
                    $status_class = 'stock-backorder';
                    break;
            }
            ?>

            <?php if ($status_label) : ?>
                <div class="rv-product-stock dot_icon <?php echo esc_attr($status_class); ?>">
                    <span><?php echo esc_html($status_label); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($product->get_sku()) : ?>
                <div class="rv-product-sku dot_icon" data-sku="<?php echo esc_attr($product->get_sku()); ?>">
                    <span>Κωδικός: <?php echo esc_html($product->get_sku()); ?></span>
                    <button class="copy-sku" aria-label="Copy SKU">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_0_2086)">
                                <path d="M12.3885 5.57532H6.81318C6.12892 5.57532 5.57422 6.13002 5.57422 6.81428V12.3896C5.57422 13.0739 6.12892 13.6286 6.81318 13.6286H12.3885C13.0728 13.6286 13.6275 13.0739 13.6275 12.3896V6.81428C13.6275 6.13002 13.0728 5.57532 12.3885 5.57532Z"
                                      stroke="black" stroke-width="1.48675" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                <path d="M3.09672 9.29201H2.47724C2.14865 9.29201 1.83351 9.16148 1.60116 8.92913C1.36881 8.69678 1.23828 8.38165 1.23828 8.05305V2.47773C1.23828 2.14914 1.36881 1.834 1.60116 1.60165C1.83351 1.3693 2.14865 1.23877 2.47724 1.23877H8.05256C8.38116 1.23877 8.69629 1.3693 8.92864 1.60165C9.16099 1.834 9.29152 2.14914 9.29152 2.47773V3.09721"
                                      stroke="black" stroke-width="1.48675" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_0_2086">
                                    <rect width="14.8675" height="14.8675" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- ACTIONS -->
        <div class="rv-product-actions">
            <?php if ( get_field('video_link') ): ?>
                <a href="#video-section" class="rv-video-trigger">
                    <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="15.3979" cy="15.3979" r="15.3979" fill="black"/>
                        <path d="M12.7305 18.9425V13.3772C12.7305 12.7606 13.4057 12.3819 13.9319 12.7034L18.4853 15.4861C18.9891 15.794 18.9891 16.5257 18.4853 16.8336L13.9319 19.6163C13.4057 19.9378 12.7305 19.5592 12.7305 18.9425Z" fill="#F7F7F9"/>
                    </svg>
                    Video Tutorial
                </a>
            <?php endif; ?>

            <a href="#tabdetails" class="rv-more-details">
                Περισσότερες Λεπτομέρειες
                <svg width="7" height="7" viewBox="0 0 7 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.63459 6.61281L0.411409 6.60198L0.411409 5.15171L4.12367 5.18418L0.000139153 1.06065L1.06078 6.0387e-06L5.17349 4.11271L5.16267 0.400454L6.62376 0.389631L6.63459 6.61281Z" fill="black"/>
                </svg>
            </a>
        </div>

    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const moreDetailsBtn = document.querySelector('.rv-more-details');
        const targetSection = document.getElementById('tabdetails');

        if (moreDetailsBtn && targetSection) {
            moreDetailsBtn.addEventListener('click', function(e) {
                // Σταματάμε τη φόρμα ή το default anchor link
                e.preventDefault();

                // Υπολογίζουμε τη θέση με ένα μικρό offset (π.χ. 100px) για να μην κολλάει στο πάνω μέρος
                const offset = 50;
                const targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - offset;

                // Ομαλό scroll στην ακριβή θέση
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                // Προαιρετικά: Αν θες να ανοίγει και το πρώτο tab στο Alpine
                window.dispatchEvent(new CustomEvent('open-tech-tab'));
            });
        }
    });
</script>