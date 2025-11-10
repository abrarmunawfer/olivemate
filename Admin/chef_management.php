<?php
include 'includes/session.php';
check_login(); 

include 'includes/header.php';
?>

<style>
    #chef-search-input::placeholder { font-size: 12px; opacity: 0.7; }
    
    .price-container {
        margin-bottom: 10px;
    }
    .price-container .item-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--c-green-dark);
    }
    .price-container .actual-price {
        font-size: 0.9rem;
        color: #6c757d;
        margin-right: 8px;
    }
    .item-card .card-body {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .item-card .card-text {
        flex-grow: 1;
    }
</style>

<button class="btn btn-primary btn-float" id="add-chef-btn" title="Add New Chef">
    <i class="bi bi-plus-lg"></i>
</button>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Chef Management</h1>
</div>

<div class="mb-4">
    <input type="text" id="chef-search-input" class="form-control" 
           placeholder="Search by chef name..." 
           style="font-size: 12px;">
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="chef-list">
</div>

<div class="modal fade" id="chefModal" tabindex="-1" aria-labelledby="chefModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chefModalLabel">Add Chef</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="chef-form" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <input type="hidden" name="action" id="chef-action" value="add_chef">
                    <input type="hidden" name="chef_id" id="chef-id" value="">
                    
                    <div class="mb-3">
                        <label for="chef-name" class="form-label">Chef Name</label>
                        <input type="text" class="form-control" id="chef-name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="chef-title" class="form-label">Title (e.g., Executive Chef)</label>
                        <input type="text" class="form-control" id="chef-title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="chef-bio" class="form-label">Biography</label>
                        <textarea class="form-control" id="chef-bio" name="bio" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="chef-status" class="form-label">Status</label>
                        <select class="form-select" id="chef-status" name="status" required>
                            <option value="active">Active (Visible)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="chef-image" class="form-label">Chef Image</label>
                        <input class="form-control" type="file" id="chef-image" name="image" accept="image/jpeg, image/png, image/gif, image/webp">
                    </div>
                    
                    <div class="mb-3 text-center">
                        <img id="image-preview" src="assets/images/placeholder.png" alt="Image Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="chef-submit-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>

<script>
$(document).ready(function() {
    const $chefList = $('#chef-list');
    const $modal = $('#chefModal');
    const $form = $('#chef-form');
    const $submitBtn = $('#chef-submit-btn');

    function loadChefs(searchTerm = '') {
        $chefList.html('<div class="col-12 text-center"><span class="spinner-border" role="status"></span><p>Loading chefs...</p></div>');
        
        let ajaxData = { action: 'fetch_all' };
        if (searchTerm.length > 0) {
            ajaxData.action = 'dySearch';
            ajaxData.term = searchTerm;
        }

        $.ajax({
            url: 'ajax/chef_action.php',
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(response) {
                $chefList.empty();
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        const cardHtml = `
                            <div class="col" data-id="${item.id}">
                                <div class="card item-card h-100">
                                    <img src="${item.image_path}" class="card-img-top item-card-img-top" alt="${item.name}">
                                    <div class="card-body">
                                        <h5 class="card-title">${item.name}</h5>
                                        <p class="text-primary mb-2">${item.title || 'Chef'}</p>
                                        <p class="card-text">${item.bio || 'No biography.'}</p>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span class="badge status-${item.status}">${item.status}</span>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${item.id}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${item.id}" title="Delete"><i class="bi bi-trash-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $chefList.append(cardHtml);
                    });
                } else {
                    $chefList.html('<div class="col-12"><div class="alert alert-info">No chefs found. Click the "+" button to add one!</div></div>');
                }
            },
            error: function() {
                $chefList.html('<div class="col-12"><div class="alert alert-danger">Failed to load chefs. Please try again.</div></div>');
            }
        });
    }

    $('#chef-search-input').on('keyup', function() {
        var searchTerm = $(this).val();
        loadChefs(searchTerm);
    });

    $('#add-chef-btn').on('click', function() {
        window.resetModalForm('chefModal', 'chef-form', 'add_chef');
        $('#chefModalLabel').text('Add Chef');
        $('#image-preview').attr('src', 'assets/images/placeholder.png').hide();
        $modal.modal('show');
    });

    $('#chef-image').on('change', function() {
        window.previewImage(this, '#image-preview');
    });

    $form.on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: 'ajax/chef_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $modal.modal('hide');
                    window.showSuccessModal(response.message);
                    loadChefs();
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('An unknown error occurred during submission.');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('Save changes');
            }
        });
    });

    $chefList.on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        window.resetModalForm('chefModal', 'chef-form', 'update_chef');
        $('#chefModalLabel').text('Edit Chef');
        
        $.ajax({
            url: 'ajax/chef_action.php',
            type: 'POST',
            data: { action: 'fetch_single', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var item = response.data;
                    $('#chef-id').val(item.id);
                    $('#chef-name').val(item.name);
                    $('#chef-title').val(item.title);
                    $('#chef-bio').val(item.bio);
                    $('#chef-status').val(item.status);
                    
                    $('#image-preview').attr('src', item.image_path).show();
                    
                    $modal.modal('show');
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('Failed to fetch chef details.');
            }
        });
    });
    
    $chefList.on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this chef?')) {
            $.ajax({
                url: 'ajax/chef_action.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.showSuccessModal(response.message);
                        loadChefs();
                    } else {
                        window.showErrorModal(response.message);
                    }
                },
                error: function() {
                    window.showErrorModal('Failed to delete chef.');
                }
            });
        }
    });

    loadChefs();
});
</script>