/**
 * YITH Wishlist Utility
 * Handles wishlist functionality with fallback for empty buttons
 */

// Helper function for notifications
const showNotification = (message, type = 'info') => {
    const notification = document.createElement('div');
    notification.className = `rv-notification rv-notification--${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        padding: 12px 20px;
        border-radius: 4px;
        z-index: 9999;
        font-size: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
};

// Initialize YITH Wishlist buttons
const initYITHWishlist = () => {
    const wishlistBlocks = document.querySelectorAll('.yith-add-to-wishlist-button-block');
    
    wishlistBlocks.forEach(block => {
        // If block is empty, create a fallback button
        if (!block.innerHTML.trim()) {
            const productId = block.dataset.productId;
            if (productId) {
                block.innerHTML = `<a href="#" class="add-to-wishlist" data-product-id="${productId}"></a>`;

                // Add click handler
                const button = block.querySelector('a');
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    const productId = button.dataset.productId;
                    
                    // Add loading state
                    button.style.opacity = '0.6';
                    
                    // AJAX call to add to wishlist
                    fetch(`${window.ajaxurl}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'yith_wcwl_add_to_wishlist',
                            product_id: productId,
                            wishlist_url: window.location.origin + '/wishlist/',
                            add_to_wishlist: productId,
                            nonce: window.ruined_nonce || ''
                        })
                    })
                    .then(response => {
                        console.log('Wishlist response:', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Wishlist data:', data);
                        button.style.opacity = '1';
                        
                        if (data.success || data.result === 'true') {
                            // Toggle heart icon
                            button.classList.toggle('exists');
                            
                            // Show success message
                            showNotification('Προστέθηκε στο wishlist!', 'success');
                        } else {
                            console.error('Wishlist failed:', data);
                            showNotification('Σφάλμα - ' + (data.message || 'Δεν προστέθηκε στο wishlist'), 'error');
                        }
                    })
                    .catch(error => {
                        button.style.opacity = '1';
                        console.error('Wishlist error:', error);
                        showNotification('Σφάλμα - Προσπαθήστε ξανά', 'error');
                    });
                });
            }
        }
    });
};

// Export for use in main.js
export { initYITHWishlist, showNotification };
