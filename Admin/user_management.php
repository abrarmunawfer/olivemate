<?php
// Include and run session check
include 'includes/session.php';
check_login(); // Redirect to index.php if not logged in

// Include the header
include 'includes/header.php';
?>

<button class="btn btn-primary btn-float" id="add-user-btn" title="Add New User">
    <i class="bi bi-plus-lg"></i>
</button>

<div class="d-flex justify-content-between align-items-center mb-4">
</div>

<div class="card shadow-sm">
    <div class="card-body">        
        <div class="table-responsive">
            <table id="usersTable" class="table table-striped table-hover dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="user-form">
                <div class="modal-body">
                    
                    <input type="hidden" name="action" id="user-action" value="add_user">
                    <input type="hidden" name="user_id" id="user-id" value="">
                    
                    <div class="mb-3">
                        <label for="user-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="user-username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="user-email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user-role" class="form-label">Role</label>
                        <select class="form-select" id="user-role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="user-password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="user-password" name="password">
                        <small id="passwordHelp" class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="user-submit-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>

<script src="assets/js/user_management.js"></script>