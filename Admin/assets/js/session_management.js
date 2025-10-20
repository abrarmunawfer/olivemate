$(document).ready(function() {
    
    // 1. Initialize DataTables
    var sessionsTable = $('#sessionsTable').DataTable({
        "processing": true,
        "serverSide": false, 
        "ajax": {
            "url": "ajax/profilemgt_action.php",
            "type": "POST",
            "data": { "action": "fetch_sessions" },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "username" },
            { "data": "role" },
            { "data": "ip_address" },
            { "data": "location" },
            { "data": "device_info" },
            { 
                "data": "login_time",
                "render": function(data, type, row) {
                    if (!data) return '';
                    let dt = new Date(data + 'Z'); 
                    return dt.toLocaleString(undefined, {
                        year: 'numeric', month: 'numeric', day: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    }); 
                }
            },
            { 
                "data": null, 
                "render": function() {
                    return '<span class="badge status-active">Active</span>';
                }
            },
            {
                "data": "id",
                "orderable": false,
                "render": function(data, type, row) {
                    return `<button class="btn btn-sm btn-outline-danger btn-logout" data-id="${data}" title="Force Logout">
                                <i class="bi bi-power"></i> Logout
                            </button>`;
                }
            }
        ],
        "responsive": true,
        "language": {
            "emptyTable": "No active sessions found.",
            "zeroRecords": "No matching sessions found",
        }
    });

    // 2. Handle Logout Button Click (UPDATED)
    $('#sessionsTable tbody').on('click', '.btn-logout', function() {
        var sessionId = $(this).data('id');
        
        if (confirm('Are you sure you want to force this session to log out?')) {
            $.ajax({
                url: 'ajax/profilemgt_action.php',
                type: 'POST',
                data: {
                    action: 'force_logout',
                    id: sessionId
                },
                dataType: 'json',
                success: function(response) {
                    
                    // **NEW LOGIC HERE**
                    if (response.status === 'self_logout') {
                        // Special case: User logged themselves out
                        alert(response.message); // Show simple alert
                        window.location.href = 'index.php'; // Redirect to login
                    
                    } else if (response.status === 'success') {
                        // Normal case: Logged out another user
                        window.showSuccessModal(response.message);
                        sessionsTable.ajax.reload(); // Refresh the table data
                    
                    } else {
                        // Error case
                        window.showErrorModal(response.message);
                    }
                },
                error: function() {
                    window.showErrorModal('An error occurred. Please try again.');
                }
            });
        }
    });
});