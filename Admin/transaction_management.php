<?php
// Include and run session check
include 'includes/session.php';
check_login();

// Include the header
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <p class="card-text">View all payment transactions.</p>

        <div class="table-responsive">
            <table id="transactionsTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Stripe Charge ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>

<script src="assets/js/transaction_management.js"></script>