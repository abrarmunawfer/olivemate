<?php
// Include and run session check
include 'includes/session.php';
check_login(); 

// Include the header
include 'includes/header.php';

// Define possible order statuses for the dropdown
$order_statuses = ['Pending', 'Processing', 'Out for Delivery', 'Done', 'Cancelled'];
?>

<!-- Page Title -->
<div class="d-flex justify-content-between align-items-center mb-4">
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <p class="card-text">View and manage customer orders.</p>
        
        <!-- DataTables Wrapper -->
        <div class="table-responsive">
            <table id="ordersTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Price</th>
                        <th>Order Status</th>
                        <th>Payment Status</th>
                        <th>Order Date</th>
                        <th>Last Update</th>
                        <th>Shipping Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Order Details Modal (Optional but Recommended) -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details (#<span id="modal-order-id"></span>)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
         <div id="modal-order-customer"></div>
         <div id="modal-order-summary"></div>
         <hr>
         <h6>Items Ordered:</h6>
         <div id="modal-order-items">
             <!-- Items will be loaded here -->
             <p>Loading items...</p>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<?php
// Include the footer
include 'includes/footer.php';
?>

<!-- Page-specific JS -->
<script>
    // Pass PHP statuses array to JavaScript
    const availableOrderStatuses = <?php echo json_encode($order_statuses); ?>; 
</script>
<script src="assets/js/order_management.js"></script>