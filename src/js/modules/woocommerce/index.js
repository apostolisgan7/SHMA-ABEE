/**
 * WooCommerce Module
 * Handles WooCommerce specific functionality
 */

export function initWooCommerce() {

    let isUpdating = false;
    let updateQueue = [];
    
    // Debounce function to prevent rapid updates
    const debounce = (func, delay) => {
        let timeoutId;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(context, args), delay);
        };
    };
    
    // Process the update queue
    const processQueue = () => {
        if (isUpdating || updateQueue.length === 0) return;
        
        isUpdating = true;
        const updateFn = updateQueue.shift();
        
        updateFn().finally(() => {
            isUpdating = false;
            processQueue();
        });
    };
    
    // Add to update queue
    const queueUpdate = (updateFn) => {
        updateQueue.push(updateFn);
        processQueue();
    };
    
    // Update cart count and fragments
    const updateCartFragments = () => {
        if (typeof wc_cart_fragments_params === 'undefined') {
            console.warn('WooCommerce cart fragments not initialized');
            return;
        }

        // Get the cart count element
        const cartCount = document.querySelector('[data-cart-count]');
        const cartContent = document.querySelector('.offcanvas-cart__body');
        
        // Only proceed if we have elements to update
        if (!cartCount && !cartContent) {
            console.warn('Cart elements not found');
            return;
        }

        return new Promise((resolve, reject) => {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000); // 5s timeout
            
            fetch(wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments') + '&t=' + Date.now(), {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Cache-Control': 'no-cache',
                },
                body: 'wc-ajax=get_refreshed_fragments',
                signal: controller.signal
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Update cart count if element exists
                if (cartCount && data.fragments && data.fragments['.cart-contents-count']) {
                    cartCount.outerHTML = data.fragments['.cart-contents-count'];
                }
                
                // Update cart content if element exists
                if (cartContent && data.fragments && data.fragments['.offcanvas-cart__body']) {
                    cartContent.outerHTML = data.fragments['.offcanvas-cart__body'];
                }
                
                // Update cart hash to prevent duplicate updates
                if (data.cart_hash) {
                    window.sessionStorage.setItem('wc_cart_hash', data.cart_hash);
                }
                
                // Trigger an event to let other components know the cart was updated
                document.body.dispatchEvent(new CustomEvent('cart_updated', { detail: data }));
                resolve(data);
            })
            .catch(error => {
                clearTimeout(timeoutId);
                if (error.name !== 'AbortError') {
                    console.error('Error updating cart fragments:', error);
                    document.body.dispatchEvent(new CustomEvent('cart_update_error', { detail: error }));
                }
                reject(error);
            });
        });
    };

    // Initialize quantity buttons
    const initQuantityButtons = () => {
        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('quantity-button')) {
                e.preventDefault();
                const input = e.target.closest('.quantity').querySelector('.qty');
                const value = parseFloat(input.value);
                
                if (e.target.classList.contains('plus')) {
                    input.value = value + 1;
                } else if (e.target.classList.contains('minus') && value > 1) {
                    input.value = value - 1;
                }
                
                // Trigger change event for WooCommerce
                input.dispatchEvent(new Event('change'));
            }
        });
    };

    // Handle AJAX add to cart on single product pages
    const initAjaxAddToCart = () => {
        document.body.addEventListener('click', (e) => {
            if (e.target.closest('.single_add_to_cart_button')) {
                const button = e.target.closest('.single_add_to_cart_button');
                const form = button.closest('form.cart');
                
                if (form) {
                    // Add loading state
                    button.classList.add('loading');
                    button.disabled = true;
                    
                    // Let WooCommerce handle the form submission
                    // We'll rely on the 'added_to_cart' event to update the UI
                }
            }
        });
    };
        // Show notification function
    const showNotification = (message, type = 'message') => {
        const notification = document.querySelector('.wc-notification');
        const content = document.querySelector('.wc-notification__content');
        
        if (!notification || !content) return;
        
        // Create a temporary div to parse the message HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = message.trim();
        
        // Extract the message text and buttons
        let messageText = '';
        const buttons = [];
        
        // Get all direct children
        Array.from(tempDiv.children).forEach(child => {
            if (child.tagName === 'A' && child.classList.contains('button')) {
                // This is a button, add to buttons array
                buttons.push(child.outerHTML);
            } else {
                // This is part of the message
                messageText += child.outerHTML;
            }
        });
        
        // Clear previous content and classes
        content.className = 'wc-notification__content';
        content.classList.add(`woocommerce-${type}`);
        
        // Set the message content
        content.innerHTML = `
            <div class="wc-notification__message">${messageText}</div>
            ${buttons.length ? `<div class="wc-notification__buttons">${buttons.join('')}</div>` : ''}
        `;
        
        // Show notification
        notification.classList.add('wc-notification--visible');
        
        // Auto-hide after 5 seconds (only if there are no buttons)
        clearTimeout(window.wcNotificationTimeout);
        if (buttons.length === 0) {
            window.wcNotificationTimeout = setTimeout(() => {
                notification.classList.remove('wc-notification--visible');
            }, 5000);
        }
    };
    
    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        // Initial cart update
        queueUpdate(updateCartFragments);
        
        // Initialize components
        initQuantityButtons();
        
        // Debounced cart update function
        const debouncedUpdate = debounce(() => {
            queueUpdate(updateCartFragments);
        }, 300);
        
        // Handle AJAX add to cart messages
        document.body.addEventListener('added_to_cart', (e) => {
            const fragments = e.detail?.fragments;
            if (fragments && fragments['.woocommerce-message']) {
                showNotification(fragments['.woocommerce-message'], 'message');
            }
            debouncedUpdate();
        });
        
        // Handle other cart events
        const cartEvents = [
            'removed_from_cart',
            'cart_emptied',
            'wc_fragments_refreshed',
            'updated_cart_totals'
        ];
        
        cartEvents.forEach(event => {
            document.body.addEventListener(event, debouncedUpdate);
        });
        
        // Show any initial WooCommerce messages
        const initialNotice = document.querySelector('.woocommerce-message, .woocommerce-error, .woocommerce-info');
        if (initialNotice) {
            const type = initialNotice.classList.contains('woocommerce-error') ? 'error' : 
                        initialNotice.classList.contains('woocommerce-info') ? 'info' : 'message';
            showNotification(initialNotice.innerHTML, type);
            initialNotice.remove();
        }
        
        // Also update on page load in case of cached fragments
        if (document.readyState === 'complete') {
            queueUpdate(updateCartFragments);
        } else {
            window.addEventListener('load', () => queueUpdate(updateCartFragments));
        }
    });
    
    // Public API
    return {
        updateCartFragments,
        initQuantityButtons,
        initAjaxAddToCart
    };
}
