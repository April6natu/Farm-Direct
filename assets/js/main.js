/**
 * Farm-Direct Main JavaScript File
 * Handles AJAX cart operations, notifications, and interactive features
 */

// Show toast notification
function showToast(message, type = 'success') {
    const toastHtml = `
        <div class="toast toast-custom align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const container = document.querySelector('.toast-container');
    container.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = container.lastElementChild;
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove from DOM after hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Add to cart via AJAX
function addToCart(productId, quantity = 1) {
    $.ajax({
        url: '/actions_cart_action.php',
        type: 'POST',
        data: {
            action: 'add',
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                updateCartCount(response.cart_count);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
}

// Remove from cart via AJAX
function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    $.ajax({
        url: '/actions_cart_action.php',
        type: 'POST',
        data: {
            action: 'remove',
            product_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                updateCartCount(response.cart_count);
                // Remove item from DOM
                $(`#cart-item-${productId}`).fadeOut(300, function() {
                    $(this).remove();
                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        location.reload();
                    }
                });
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
}

// Update cart quantity via AJAX
function updateCartQuantity(productId, quantity) {
    $.ajax({
        url: '/actions_cart_action.php',
        type: 'POST',
        data: {
            action: 'update',
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateCartCount(response.cart_count);
                // Update item total
                const price = parseFloat($(`#cart-item-${productId}`).data('price'));
                const itemTotal = price * quantity;
                $(`#item-total-${productId}`).text('$' + itemTotal.toFixed(2));
                // Update cart total
                updateCartTotal();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
}

// Update cart count badge
function updateCartCount(count) {
    const badge = $('.cart-badge');
    if (count > 0) {
        badge.text(count).show();
    } else {
        badge.hide();
    }
}

// Update cart total
function updateCartTotal() {
    let total = 0;
    $('.cart-item').each(function() {
        const price = parseFloat($(this).data('price'));
        const quantity = parseInt($(this).find('.quantity-input').val());
        total += price * quantity;
    });
    $('#cart-total').text('$' + total.toFixed(2));
}

// Quick view product modal (AJAX)
function quickViewProduct(productId) {
    $.ajax({
        url: '/product.php',
        type: 'GET',
        data: {
            id: productId,
            ajax: 1
        },
        success: function(response) {
            $('#quickViewModal .modal-body').html(response);
            const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            modal.show();
        },
        error: function() {
            showToast('Unable to load product details.', 'error');
        }
    });
}

// Mark notification as read
function markNotificationRead(notificationId) {
    $.ajax({
        url: '/seller/actions.php',
        type: 'POST',
        data: {
            action: 'mark_notification_read',
            notification_id: notificationId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $(`#notification-${notificationId}`).removeClass('unread');
            }
        }
    });
}

// Initialize on document ready
$(document).ready(function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Confirm delete actions
    $('.confirm-delete').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
    
    // Handle quantity changes in cart
    $('.quantity-input').on('change', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val());
        if (quantity > 0) {
            updateCartQuantity(productId, quantity);
        }
    });
});
