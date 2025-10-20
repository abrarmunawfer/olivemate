<?php
// Include and run session check
include 'includes/session.php';
check_login(); // Redirect to index.php if not logged in

// Include the header
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <p class="card-text">
            This table shows all currently active 'admin' and 'staff' sessions. You can force a user to log out by clicking the "Logout" button.
            <br>
            <span class="text-danger"><strong>Note:</strong> This will not log out your own current session.</span>
        </p>
        
        <div class="table-responsive">
            <table id="sessionsTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>IP Address</th>
                        <th>Location</th> <th>Device Info</th>
                        <th>Login Time (Your Local)</th> <th>Status</th>
                        <th>Action</th>
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

<script src="assets/js/session_management.js"></script>