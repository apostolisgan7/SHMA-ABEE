/**
 * Off-Canvas Cart
 * Handles the off-canvas cart functionality
 */
export function initOffcanvasCart() {
    const cart = document.getElementById('offcanvas-cart');
    const cartToggle = document.querySelector('.header-cart .cart-contents');
    const cartClose = document.querySelector('.offcanvas-cart__close');
    const cartOverlay = document.querySelector('.offcanvas-cart__overlay');

    if (!cart || !cartToggle) {
        console.warn('Offcanvas cart elements not found');
        return;
    }
    
    // Initialize cart state
    let isCartOpen = false;

    // Toggle cart
    function toggleCart(show = true) {
        isCartOpen = show;
        
        if (isCartOpen) {
            document.body.classList.add('offcanvas-cart-open');
            cart.classList.add('is-open');
            // Prevent body scroll when cart is open
            document.body.style.overflow = 'hidden';
            // Focus on close button for better accessibility
            setTimeout(() => {
                if (cartClose) cartClose.focus();
            }, 100);
        } else {
            document.body.classList.remove('offcanvas-cart-open');
            cart.classList.remove('is-open');
            // Restore body scroll
            document.body.style.overflow = '';
        }
    }

    // Event Listeners
    cartToggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleCart(!cart.classList.contains('is-open'));
        return false;
    });

    cartClose.addEventListener('click', () => toggleCart(false));
    cartOverlay.addEventListener('click', () => toggleCart(false));

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && cart.classList.contains('is-open')) {
            toggleCart(false);
        }
    });

    // Handle AJAX add to cart
    function handleAddedToCart() {
        // Update cart count
        const cartCount = document.querySelector('.header-cart .header-link .count');
        if (cartCount) {
            // Get the current count and increment by 1
            const currentCount = parseInt(cartCount.textContent) || 0;
            cartCount.textContent = currentCount + 1;
        }

        // Refresh cart fragments
        updateCartFragments();

        // Open the cart
        toggleCart(true);
    }

    // Update cart fragments via AJAX
    function updateCartFragments() {
        if (typeof wc_cart_fragments_params === 'undefined') {
            return false;
        }

        const data = {
            url: wc_cart_fragments_params.ajax_url,
            data: {
                wc_ajax: 'get_refreshed_fragments',
                _wpnonce: wc_cart_fragments_params.wc_ajax_nonce
            },
            type: 'POST',
            success: function(response) {
                if (response && response.fragments) {
                    // Update cart fragments
                    Object.keys(response.fragments).forEach(key => {
                        const element = document.querySelector(key);
                        if (element) {
                            element.outerHTML = response.fragments[key];
                        }
                    });

                    // Reinitialize the cart to attach event listeners to the new elements
                    initOffcanvasCart();
                }
            }
        };

        // Use jQuery.ajax if available, otherwise use fetch
        if (typeof jQuery !== 'undefined' && jQuery.ajax) {
            jQuery.ajax(data);
        } else {
            fetch(data.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    'wc-ajax': 'get_refreshed_fragments',
                    _wpnonce: wc_cart_fragments_params.wc_ajax_nonce
                })
            })
            .then(response => response.json())
            .then(response => {
                if (response && response.fragments) {
                    Object.keys(response.fragments).forEach(key => {
                        const element = document.querySelector(key);
                        if (element) {
                            element.outerHTML = response.fragments[key];
                        }
                    });
                    initOffcanvasCart();
                }
            });
        }
    }

    // Listen for added to cart events
    document.body.addEventListener('added_to_cart', handleAddedToCart);
    document.body.addEventListener('wc_fragments_refreshed', updateCartFragments);

    // Initialize cart fragments on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateCartFragments();
    });

    // Expose functions for other scripts
    return {
        toggleCart,
        updateCartFragments
    };
}
