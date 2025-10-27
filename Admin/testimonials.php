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
        <p class="card-text">Manage customer testimonials and choose which ones to display.</p>

        <div class="table-responsive">
            <table id="testimonialsTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User (ID)</th> <th>Provided Name</th> <th>Testimonial</th>
                        <th>Rating</th>
                        <th>Created At</th> <th>Visible</th>
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

<script src="assets/js/testimonial_management.js"></script>