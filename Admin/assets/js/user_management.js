$(document).ready(function() {
    
    // 1. Initialize DataTables
    var usersTable = $('#usersTable').DataTable({
        "processing": true,
        "serverSide": false, // Client-side for this small table
        "ajax": {
            "url": "ajax/user_action.php",
            "type": "POST",
            "data": { "action": "fetch_all" },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            { "data": "username" },
            { "data": "email" },
            { "data": "role" },
            { "data": "created_datetime" },
            {
                "data": "id",
                "orderable": false,
                "render": function(data, type, row) {
                    // 'data' is the user id
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item btn-edit" href="#" data-id="${data}">
                                    <i class="bi bi-pencil-fill text-primary"></i> Update
                                </a></li>
                                <li><a class="dropdown-item btn-delete" href="#" data-id="${data}">
                                    <i class="bi bi-trash-fill text-danger"></i> Delete
                                </a></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ],
        "responsive": true,
        "language": {
            "emptyTable": "No users found.",
        }
    });

    const $modal = $('#userModal');
    const $form = $('#user-form');
    const $submitBtn = $('#user-submit-btn');

    // 2. Show "Add" Modal
    $('#add-user-btn').on('click', function() {
        window.resetModalForm('userModal', 'user-form', 'add_user');
        $('#passwordHelp').text('Password is required.');
        $('#user-password').prop('required', true);
        $modal.modal('show');
    });

    // 3. Form Submission (Add & Update)
    $form.on('submit', function(e) {
        e.preventDefault();
        
        // Client-side validation for 'add' action
        if ($('#user-action').val() === 'add_user' && $('#user-password').val() === '') {
            window.showErrorModal('Password is required when adding a new user.');
            return;
        }

        var formData = new FormData(this);
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: 'ajax/user_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $modal.modal('hide');
                    window.showSuccessModal(response.message);
                    usersTable.ajax.reload(); // Refresh table
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('An unknown error occurred.');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('Save changes');
            }
        });
    });

    // 4. "Edit" Button Click
    $('#usersTable tbody').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        window.resetModalForm('userModal', 'user-form', 'update_user');
        $('#passwordHelp').text("Leave blank if you don't want to change the password.");
        $('#user-password').prop('required', false);

        $.ajax({
            url: 'ajax/user_action.php',
            type: 'POST',
            data: { action: 'fetch_single', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var user = response.data;
                    $('#user-id').val(user.id);
                    $('#user-username').val(user.username);
                    $('#user-email').val(user.email);
                    $('#user-role').val(user.role);
                    $modal.modal('show');
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('Failed to fetch user details.');
            }
        });
    });

    // 5. "Delete" Button Click
    $('#usersTable tbody').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            $.ajax({
                url: 'ajax/user_action.php',
                type: 'POST',
                data: { action: 'delete_user', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.showSuccessModal(response.message);
                        usersTable.ajax.reload();
                    } else {
                        window.showErrorModal(response.message);
                    }
                },
                error: function() {
                    window.showErrorModal('Failed to delete user.');
                }
            });
        }
    });

});