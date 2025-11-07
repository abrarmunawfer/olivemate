$(document).ready(function() {

    // --- Mobile Menu Toggle ---
    $('.menu-toggle').click(function() {
        $('.nav-links').toggleClass('active');
        // You might need to add icon toggling logic here if it's missing
        // e.g., $(this).find('i').toggleClass('fa-bars fa-xmark');
    });

    // --- Popular Food Slider ---
    const sliderWrapper = $('.slider-wrapper');
    if (sliderWrapper.length) {
        const prevBtn = $('.prev-btn');
        const nextBtn = $('.next-btn');
        const cardWidth = $('.food-card').outerWidth(true); // Get width + margin

        nextBtn.click(function() {
            // Find the current scroll position and add card width
            let newScroll = sliderWrapper.scrollLeft() + cardWidth;
            // Get max scrollable width
            let maxScroll = sliderWrapper[0].scrollWidth - sliderWrapper[0].clientWidth;
            if (newScroll > maxScroll) {
                newScroll = 0; // Loop to start
            }
            sliderWrapper.animate({ scrollLeft: newScroll }, 300);
        });
        prevBtn.click(function() {
             // Find the current scroll position and subtract card width
            let newScroll = sliderWrapper.scrollLeft() - cardWidth;
             if (newScroll < 0) {
                 // Loop to end
                newScroll = sliderWrapper[0].scrollWidth - sliderWrapper[0].clientWidth;
            }
            sliderWrapper.animate({ scrollLeft: newScroll }, 300);
        });
    }

    // ===================================
    // === CUSTOMER/AUTH FUNCTIONS ===
    // ===================================

    // --- Customer Registration Form ---
    $('#register-form').on('submit', function(e) {
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
                    window.location.href = 'login.php';
                } else {
                    $alert.addClass('alert-danger').html(response.message).slideDown();
                    $btn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function() {
                $alert.addClass('alert-danger').html('An error occurred during registration. Please try again.').slideDown();
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
                    window.location.href = 'index.php';
                }
            }
        });
    });

    // ===================================
    // === CART FUNCTIONS ===
    // ===================================

    // --- Add to Cart ---
    $('body').on('click', '.add-to-cart-btn', function() {
        const $btn = $(this);
        // Find originalHtml once, or ensure it's reset correctly
        const originalHtml = $btn.data('original-html') || $btn.html();
        if (!$btn.data('original-html')) { // Store original html if not stored
             $btn.data('original-html', originalHtml);
        }

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
                if(response.cart_count !== undefined) {
                    $('#cart-count').text(response.cart_count); // Update cart count
                }
                setTimeout(function() {
                    $btn.prop('disabled', false).html(originalHtml);
                }, 1500); // Reset button after 1.5s
            },
            error: function() {
                // Handle error - maybe reset button sooner
                 $btn.prop('disabled', false).html(originalHtml);
                 // You could show an error message here
            }
        });
    });

    // --- Load Cart (on cart.php and checkout.php) ---
    if ($('#cart-container').length > 0) { loadCart(); }
    if ($('#summary-items').length > 0) { loadCartSummary(); }

    function loadCart() {
        $.ajax({
            url: 'ajax/cart_action.php', type: 'POST', data: { action: 'get' }, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#cart-container').html(response.cart_html);
                    if(response.cart_count !== undefined) {
                         $('#cart-count').text(response.cart_count);
                    }
                }
            }
        });
    }

    function loadCartSummary() {
         $.ajax({
            url: 'ajax/cart_action.php', type: 'POST', data: { action: 'get' }, dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#summary-items').html(response.summary_html);
                    $('#summary-total-price').text('€' + response.total_price);
                    if(response.cart_count !== undefined) {
                         $('#cart-count').text(response.cart_count);
                    }
                }
            }
        });
    }

    // --- Update Quantity ---
    $('body').on('change', '.quantity-input', function() {
        const id = $(this).data('id');
        const quantity = $(this).val();
        $.ajax({
            url: 'ajax/cart_action.php', type: 'POST', data: { action: 'update_quantity', id: id, quantity: quantity }, dataType: 'json',
            success: function(response) {
                loadCart(); // Reload cart table
                loadCartSummary(); // Reload summary (if on checkout page)
                if(response.cart_count !== undefined) {
                     $('#cart-count').text(response.cart_count);
                }
            }
        });
    });

    // --- Remove from Cart ---
    $('body').on('click', '.remove-item-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'ajax/cart_action.php', type: 'POST', data: { action: 'remove', id: id }, dataType: 'json',
            success: function(response) {
                loadCart(); // Reload cart table
                loadCartSummary(); // Reload summary (if on checkout page)
                if(response.cart_count !== undefined) {
                     $('#cart-count').text(response.cart_count);
                }
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
        setTimeout(function() {
            $.ajax({
                url: 'ajax/order_action.php', type: 'POST', data: { action: 'place_order', address: $('#shipping-address').val() }, dataType: 'json',
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
        }, 1500);
    });

    // ===================================
    // === PROFILE PAGE FUNCTIONS ===
    // ===================================
    
    // Check if we are on the profile page
    if ($('.profile-container').length > 0) { 
        
        // --- THIS IS THE TAB-SWITCHING LOGIC ---
        // This makes your data-tab buttons work
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
            $('#order-history-container').html('<p><i class="fa-solid fa-spinner fa-spin"></i> Loading history...</p>');
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
                                    <strong>€${parseFloat(order.total_price).toFixed(2)}</strong>
                                    <span class="order-history-status status-${(order.order_status || 'pending').toLowerCase().replace(' ', '-')}">
                                        ${order.order_status}
                                    </span>
                                </div>
                            `;
                        });
                        $('#order-history-container').html(html);
                    } else {
                        $('#order-history-container').html('<p class="no-orders">You have no past orders.</p>');
                    }
                },
                error: function() {
                     $('#order-history-container').html('<p class="no-orders text-danger">Failed to load order history.</p>');
                }
            });
        }
        
        // --- Load Live Order Tracking ---
        let ordersToHide = {};
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
                                // Check if updated_at is valid, otherwise use created_at
                                let doneTime = order.updated_at || order.created_at;
                                let hideTime = new Date(new Date(doneTime + 'Z').getTime() + 5 * 60000); // 5 minutes
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
                },
                 error: function() {
                     $('#live-order-container').html('<p class="no-orders text-danger">Failed to load live orders.</p>');
                }
            });
        }
        
        // --- Helper to build the tracker HTML ---
        function buildTrackerHtml(order) {
            let p1 = '', p2 = '', p3 = '', p4 = '';
            let lineWidth = '0%';
            
            if (order.order_status === 'Pending') {
                p1 = 'completed'; lineWidth = '0%';
            } else if (order.order_status === 'Processing') {
                p1 = 'completed'; p2 = 'completed'; lineWidth = '33%';
            } else if (order.order_status === 'Out for Delivery') {
                p1 = 'completed'; p2 = 'completed'; p3 = 'completed'; lineWidth = '66%';
            } else if (order.order_status === 'Done') {
                p1 = 'completed'; p2 = 'completed'; p3 = 'completed'; p4 = 'completed'; lineWidth = '100%';
            }

            if (order.order_status === 'Cancelled') {
                 return `
                    <div class="order-track-item cancelled">
                         <div class="order-track-header">
                            <div><strong>Order ID: #${order.id}</strong><br><span>Total: €${parseFloat(order.total_price).toFixed(2)}</span></div>
                            <span class="order-track-status cancelled-status">Cancelled</span>
                        </div>
                        <p class="text-center text-danger mt-3">This order has been cancelled.</p>
                    </div>`;
            }

            return `
                <div class="order-track-item">
                    <div class="order-track-header">
                        <div><strong>Order ID: #${order.id}</strong><br><span>Total: €${parseFloat(order.total_price).toFixed(2)}</span></div>
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
                        <div class="progress-step ${p4}">
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
    
    // ===================================
    // === PROFILE UPDATE (Modal Form) ===
    // ===================================
    $('#update-profile-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#update-profile-submit-btn');
        const $modalAlert = $('#modal-update-alert');
        const $pageAlert = $('#profile-update-alert');
        const originalBtnHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
        $modalAlert.hide(); $pageAlert.hide();

        $.ajax({
            url: 'ajax/auth_action.php', // Target for profile update
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#profile-display-username').text(response.new_username);
                    $('#profile-welcome-username').text(response.new_username);
                    var updateModal = bootstrap.Modal.getInstance(document.getElementById('updateProfileModal'));
                    if (updateModal) { updateModal.hide(); }
                    $pageAlert.removeClass('alert-danger').addClass('alert-success').html(response.message).slideDown();
                    $('#update-password').val('');
                } else if (response.status === 'info') {
                    $modalAlert.removeClass('alert-danger alert-success').addClass('alert-info').html(response.message).slideDown();
                } else {
                    $modalAlert.removeClass('alert-success alert-info').addClass('alert-danger').html(response.message).slideDown();
                }
            },
            error: function() {
                 $modalAlert.removeClass('alert-success alert-info').addClass('alert-danger').html('An error occurred. Please try again.').slideDown();
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

    // --- Reset modal when opened/closed ---
    const updateProfileModalElement = document.getElementById('updateProfileModal');
    if (updateProfileModalElement) {
        updateProfileModalElement.addEventListener('show.bs.modal', function () {
            $('#update-username').val($('#profile-display-username').text());
            $('#update-password').val('');
            $('#modal-update-alert').hide();
        });
        
        const triggerButton = document.getElementById('show-update-modal-btn');
        if (triggerButton) {
            updateProfileModalElement.addEventListener('hidden.bs.modal', function () {
                triggerButton.focus();
            });
        }
    }

    // ===================================
    // === TESTIMONIAL SUBMIT FUNCTION ===
    // ===================================
    $('#testimonial-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#submit-testimonial-btn');
        const $alert = $('#testimonial-alert');
        const originalBtnHtml = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');
        $alert.hide();

        $.ajax({
            url: 'ajax/testimonial_submit_action.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $alert.removeClass('alert-danger').addClass('alert-success').html(response.message).slideDown();
                    $form[0].reset();
                    $btn.html('<i class="fa-solid fa-check"></i> Submitted!');
                    setTimeout(() => $alert.slideUp(), 5000);
                } else {
                    $alert.removeClass('alert-success').addClass('alert-danger').html(response.message || 'An error occurred.').slideDown();
                    $btn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function() {
                $alert.removeClass('alert-success').addClass('alert-danger').html('An error occurred. Please try again.').slideDown();
                $btn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });

}); // End document ready