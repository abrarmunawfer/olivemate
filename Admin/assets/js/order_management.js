$(document).ready(function() {
    
    // 1. Initialize DataTables
    var ordersTable = $('#ordersTable').DataTable({
        "processing": true,
        "serverSide": false, // Use client-side for moderate data
        "ajax": {
            "url": "ajax/order_action.php",
            "type": "POST",
            "data": { "action": "fetch_orders" },
            "dataSrc": "data" 
        },
        "columns": [
            { "data": "id" },
            { "data": "customer_name" },
            { 
                "data": "total_price",
                "render": function(data) {
                    return '$' + parseFloat(data).toFixed(2);
                }
            },
            { 
                "data": "order_status",
                 "render": function(data){
                    let badgeClass = 'secondary'; // Default
                    if (data === 'Pending') badgeClass = 'warning text-dark';
                    else if (data === 'Processing') badgeClass = 'info text-dark';
                    else if (data === 'Out for Delivery') badgeClass = 'primary';
                    else if (data === 'Done') badgeClass = 'success';
                    else if (data === 'Cancelled') badgeClass = 'danger';
                    return `<span class="badge bg-${badgeClass}">${data}</span>`;
                 }
            },
            { 
                "data": "payment_status",
                 "render": function(data){
                     let badgeClass = data === 'Success' ? 'success' : (data === 'Failed' ? 'danger' : 'warning text-dark');
                     return `<span class="badge bg-${badgeClass}">${data}</span>`;
                 }
            },
            { 
                "data": "created_at",
                "render": function(data) {
                    return data ? new Date(data + 'Z').toLocaleString() : 'N/A'; // Add Z for UTC
                }
            },
             { 
                "data": "updated_at",
                "render": function(data) {
                    return data ? new Date(data + 'Z').toLocaleString() : 'N/A'; // Add Z for UTC
                }
            },
            { 
                "data": "shipping_address",
                "render": function(data) {
                    // Truncate long addresses for display
                    return data ? (data.length > 30 ? data.substring(0, 30) + '...' : data) : 'N/A';
                }
            },
            {
                "data": "id",
                "orderable": false,
                "render": function(data, type, row) {
                    // 'data' is the order id
                    let statusOptions = '';
                    // Use the PHP array passed to JS
                    availableOrderStatuses.forEach(status => {
                        const selected = (status === row.order_status) ? 'selected' : '';
                        statusOptions += `<option value="${status}" ${selected}>${status}</option>`;
                    });

                    return `
                        <div class="d-flex flex-column gap-2">
                             <button class="btn btn-sm btn-outline-info btn-view-details" data-id="${data}" title="View Details">
                                <i class="bi bi-eye-fill"></i> Details
                            </button>
                            <div class="input-group input-group-sm">
                                <select class="form-select status-select" data-id="${data}">
                                    ${statusOptions}
                                </select>
                                <button class="btn btn-primary btn-update-status" data-id="${data}" title="Update Status">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
            }
        ],
        "responsive": true,
        "order": [[ 5, "desc" ]], // Default sort by Order Date descending
        "language": {
            "emptyTable": "No orders found.",
            "zeroRecords": "No matching orders found",
        }
    });

    // 2. Handle "Update Status" Button Click
    $('#ordersTable tbody').on('click', '.btn-update-status', function() {
        var orderId = $(this).data('id');
        var $select = $(this).closest('.input-group').find('.status-select');
        var newStatus = $select.val();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        
        $.ajax({
            url: 'ajax/order_action.php',
            type: 'POST',
            data: {
                action: 'update_status',
                id: orderId,
                status: newStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.showSuccessModal(response.message);
                    ordersTable.ajax.reload(null, false); // Reload data without resetting page
                } else if (response.status === 'info') {
                     window.showSuccessModal(response.message); // Show info message as success
                     ordersTable.ajax.reload(null, false);
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('An error occurred while updating status.');
            },
            complete: function(){
                 $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i>');
            }
        });
    });
    
    // 3. Handle "View Details" Button Click
    var detailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    $('#ordersTable tbody').on('click', '.btn-view-details', function() {
        var orderId = $(this).data('id');
        $('#modal-order-id').text(orderId);
        $('#modal-order-customer').html('Loading...');
        $('#modal-order-summary').html('Loading...');
        $('#modal-order-items').html('<p><i class="fa fa-spinner fa-spin"></i> Loading items...</p>');
        
        detailsModal.show();

         $.ajax({
            url: 'ajax/order_action.php',
            type: 'POST',
            data: { action: 'fetch_details', id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const order = response.order;
                    const items = response.items;
                    
                    $('#modal-order-customer').html(`
                        <p><strong>Customer:</strong> ${order.username} (${order.email})</p>
                    `);
                    $('#modal-order-summary').html(`
                        <p><strong>Total:</strong> $${parseFloat(order.total_price).toFixed(2)}</p>
                        <p><strong>Order Status:</strong> ${order.order_status}</p>
                        <p><strong>Payment Status:</strong> ${order.payment_status}</p>
                        <p><strong>Address:</strong> ${order.shipping_address || 'N/A'}</p>
                        <p><strong>Ordered On:</strong> ${new Date(order.created_at + 'Z').toLocaleString()}</p>
                    `);

                    let itemsHtml = '<ul class="list-group list-group-flush">';
                    if (items.length > 0) {
                        items.forEach(item => {
                            itemsHtml += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${item.quantity} x ${item.food_name}
                                <span>$${parseFloat(item.price_per_item * item.quantity).toFixed(2)}</span>
                            </li>`;
                        });
                    } else {
                        itemsHtml += '<li class="list-group-item">No items found for this order.</li>';
                    }
                    itemsHtml += '</ul>';
                    $('#modal-order-items').html(itemsHtml);
                    
                } else {
                     $('#modal-order-items').html(`<p class="text-danger">${response.message}</p>`);
                }
            },
            error: function() {
                $('#modal-order-items').html('<p class="text-danger">Failed to load order details.</p>');
            }
        });
    });

});
