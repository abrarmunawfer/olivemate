$(document).ready(function() {

    // --- Mobile Menu Toggle ---
    $('.menu-toggle').click(function() {
        // ... (your existing menu toggle code) ...
    });

    // --- Popular Food Slider ---
    // ... (your existing slider code) ...

    
    // ===================================
    // === NEW CUSTOMER/AUTH FUNCTIONS ===
    // ===================================

    // --- Customer Auth Forms (Login/Register) ---
    $('#login-form, #register-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const $alert = $('#auth-alert');
        const originalBtnHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        $alert.hide().removeClass('alert-danger alert-success');

        $.ajax({
            url: 'ajax/auth_action.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if ($form.attr('id') === 'login-form') {
                        window.location.href = 'profile.php'; // Redirect to profile
                    } else {
                        window.location.href = 'login.php'; // Redirect to login
                    }
                } else {
                    $alert.addClass('alert-danger').html(response.message).slideDown();
                    $btn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function() {
                $alert.addClass('alert-danger').html('An error occurred. Please try again.').slideDown();
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
    
    // --- Customer Logout ---
    $('#customer-logout-btn').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/auth_action.php',
            type: 'POST',
            data: { action: 'logout' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = 'index.php'; // Redirect to home
                }
            }
        });
    });

    // ===================================
    // === NEW CART FUNCTIONS ===
    // ===================================

    // --- Add to Cart ---
    $('body').on('click', '.add-to-cart-btn', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fa-solid fa-check"></i> Added');
        
        $.ajax({
            url: 'ajax/cart_action.php',
            type: 'POST',
            data: {
                action: 'add',
                id: $btn.data('id'),
                name: $btn.data('name'),
                price: $btn.data('price'),
                image: $btn.data('image')
            },
            dataType: 'json',
            success: function(response) {
                $('#cart-count').text(response.cart_count); // Update cart count in header
                setTimeout(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }, 1500); // Reset button after 1.5s
            }
        });
    });

    // --- Load Cart (on cart.php and checkout.php) ---
    if ($('#cart-container').length > 0) { // If on cart.php
        loadCart();
    }
    if ($('#summary-items').length > 0) { // If on checkout.php
        loadCartSummary();
    }

    function loadCart() {
        $.ajax({
            url: 'ajax/cart_action.php',
            type: 'POST',
            data: { action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#cart-container').html(response.cart_html);
                }
            }
        });
    }

    function loadCartSummary() {
         $.ajax({
            url: 'ajax/cart_action.php',
            type: 'POST',
            data: { action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#summary-items').html(response.summary_html);
                    $('#summary-total-price').text('$' + response.total_price);
                }
            }
        });
    }

    // --- Update Quantity ---
    $('body').on('change', '.quantity-input', function() {
        const id = $(this).data('id');
        const quantity = $(this).val();

        $.ajax({
            url: 'ajax/cart_action.php',
            type: 'POST',
            data: { action: 'update_quantity', id: id, quantity: quantity },
            dataType: 'json',
            success: function(response) {
                loadCart(); // Reload the whole cart to update totals
                $('#cart-count').text(response.cart_count);
            }
        });
    });

    // --- Remove from Cart ---
    $('body').on('click', '.remove-item-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/cart_action.php',
            type: 'POST',
            data: { action: 'remove', id: id },
            dataType: 'json',
            success: function(response) {
                loadCart(); // Reload cart
                $('#cart-count').text(response.cart_count);
            }
        });
    });

    // --- Checkout / Payment Form ---
    $('#payment-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#pay-btn');
        const $alert = $('#payment-alert');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
        $alert.hide();
        
        // --- Stripe Simulation ---
        // In a real app, you would use Stripe.js to create a token/paymentMethod
        // and send that to the server.
        // Here, we just simulate a 1.5s delay.
        
        setTimeout(function() {
            $.ajax({
                url: 'ajax/order_action.php',
                type: 'POST',
                data: {
                    action: 'place_order',
                    address: $('#shipping-address').val()
                    // In real app, you'd also send payment_method_id: 'pm_...'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = response.redirect_url;
                    } else {
                        $alert.addClass('alert-danger').html(response.message).slideDown();
                        $btn.prop('disabled', false).html('Pay Now');
                    }
                },
                error: function() {
                    $alert.addClass('alert-danger').html('An error occurred. Please try again.').slideDown();
                    $btn.prop('disabled', false).html('Pay Now');
                }
            });
        }, 1500); // 1.5s simulated payment processing
    });


    // ===================================
    // === NEW PROFILE PAGE FUNCTIONS ===
    // ===================================
    if ($('.profile-container').length > 0) {
        
        // --- Tab Switching ---
        $('.tab-link').on('click', function() {
            const tabId = $(this).data('tab');

            // Update button active state
            $('.tab-link').removeClass('active');
            $(this).addClass('active');

            // Show content
            $('.tab-content').removeClass('active');
            $('#' + tabId).addClass('active');
        });

        // --- Load Order History ---
        function loadOrderHistory() {
            $('#order-history-container').html('<i class="fa-solid fa-spinner fa-spin"></i> Loading history...');
            $.ajax({
                url: 'ajax/order_action.php?action=get_history',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.orders.length > 0) {
                        let html = '';
                        response.orders.forEach(function(order) {
                            html += `
                                <div class="order-history-item">
                                    <div class="order-history-details">
                                        <strong>Order ID: #${order.id}</strong><br>
                                        <span>Date: ${new Date(order.created_at + 'Z').toLocaleString()}</span>
                                    </div>
                                    <strong>$${parseFloat(order.total_price).toFixed(2)}</strong>
                                    <span class="order-history-status status-${order.order_status.toLowerCase()}">
                                        ${order.order_status}
                                    </span>
                                </div>
                            `;
                        });
                        $('#order-history-container').html(html);
                    } else {
                        $('#order-history-container').html('<p class="no-orders">You have no past orders.</p>');
                    }
                }
            });
        }
        
        // --- Load Live Order Tracking ---
        let ordersToHide = {}; // To manage the 5-min auto-hide
        
        function loadLiveOrders() {
            $.ajax({
                url: 'ajax/order_action.php?action=get_live_orders',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    let html = '';
                    if (response.status === 'success' && response.orders.length > 0) {
                        
                        response.orders.forEach(function(order) {
                            
                            // Check if this order is 'Done' and should be hidden
                            if (ordersToHide[order.id] && new Date() > ordersToHide[order.id]) {
                                return; // Skip this order
                            }
                            
                            // If order just became 'Done', set its 5-min timer
                            if (order.order_status === 'Done' && !ordersToHide[order.id]) {
                                let hideTime = new Date(new Date(order.updated_at + 'Z').getTime() + 5 * 60000); // 5 minutes
                                ordersToHide[order.id] = hideTime;
                            }

                            html += buildTrackerHtml(order);
                        });
                        
                        if (html === '') {
                             $('#live-order-container').html('<p class="no-orders">You have no active orders.</p>');
                        } else {
                            $('#live-order-container').html(html);
                        }

                    } else {
                        $('#live-order-container').html('<p class="no-orders">You have no active orders.</p>');
                    }
                }
            });
        }
        
        // --- Helper to build the tracker HTML ---
        function buildTrackerHtml(order) {
            let p1 = '', p2 = '', p3 = '';
            let lineWidth = '0%';
            
            if (order.order_status === 'Pending') {
                p1 = 'completed';
                lineWidth = '0%';
            } else if (order.order_status === 'Processing') {
                p1 = 'completed';
                p2 = 'completed';
                lineWidth = '50%';
            } else if (order.order_status === 'Out for Delivery') {
                p1 = 'completed';
                p2 = 'completed';
                p3 = 'completed';
                lineWidth = '100%';
            } else if (order.order_status === 'Done') {
                p1 = 'completed';
                p2 = 'completed';
                p3 = 'completed';
                lineWidth = '100%';
            }

            return `
                <div class="order-track-item">
                    <div class="order-track-header">
                        <div>
                            <strong>Order ID: #${order.id}</strong><br>
                            <span>Total: $${parseFloat(order.total_price).toFixed(2)}</span>
                        </div>
                        <span class="order-track-status">${order.order_status}</span>
                    </div>
                    <div class="order-progress-bar">
                        <div class="progress-line"><div class="progress-line-inner" style="width: ${lineWidth};"></div></div>
                        <div class="progress-step ${p1}">
                            <div class="step-icon"><i class="fa-solid fa-receipt"></i></div>
                            <div class="step-label">Order Placed</div>
                        </div>
                        <div class="progress-step ${p2}">
                            <div class="step-icon"><i class="fa-solid fa-kitchen-set"></i></div>
                            <div class="step-label">Processing</div>
                        </div>
                        <div class="progress-step ${p3}">
                            <div class="step-icon"><i class="fa-solid fa-truck"></i></div>
                            <div class="step-label">Out for Delivery</div>
                        </div>
                        <div class="progress-step ${order.order_status === 'Done' ? 'completed' : ''}">
                            <div class="step-icon"><i class="fa-solid fa-check-double"></i></div>
                            <div class="step-label">Done</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Initial load
        loadOrderHistory();
        loadLiveOrders();
        
        // Set polling for live orders every 10 seconds
        setInterval(loadLiveOrders, 10000); 
    }

});