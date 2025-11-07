$(document).ready(function() {

    // Initialize DataTables for Transactions
    var transactionsTable = $('#transactionsTable').DataTable({
        "processing": true,
        "serverSide": false, // Use client-side processing
        "ajax": {
            "url": "ajax/transaction_action.php",
            "type": "POST",
            "data": { "action": "fetch_transactions" },
            "dataSrc": "data" // Specifies the key holding the array in the JSON response
        },
        "columns": [
            { "data": "transaction_id" },
            { "data": "order_id" },
            { "data": "customer_name" },
            { "data": "stripe_charge_id" },
            {
                "data": "amount",
                "render": function(data) {
                    // Format amount as currency
                    return '€' + parseFloat(data).toFixed(2);
                }
            },
            {
                "data": "transaction_status",
                 "render": function(data){
                     // Add badges based on status (assuming 'Success', 'Failed', etc.)
                     let badgeClass = 'secondary'; // Default
                     let statusText = data || 'Unknown';
                     if (statusText.toLowerCase() === 'success') badgeClass = 'success';
                     else if (statusText.toLowerCase() === 'failed') badgeClass = 'danger';
                     else if (statusText.toLowerCase() === 'pending') badgeClass = 'warning text-dark';
                     return `<span class="badge bg-${badgeClass}">${statusText}</span>`;
                 }
            },
            {
                "data": "transaction_date",
                "render": function(data) {
                    // Format date and time
                    return data ? new Date(data + 'Z').toLocaleString() : 'N/A'; // Add Z for UTC
                }
            }
        ],
        "responsive": true,
        "order": [[ 6, "desc" ]], // Default sort by Transaction Date descending
        "language": {
            "emptyTable": "No transactions found.",
            "zeroRecords": "No matching transactions found",
            "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>' // Optional: Custom loading indicator
        }
    });

});