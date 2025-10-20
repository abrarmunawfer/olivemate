$(document).ready(function() {
    const $categoryList = $('#category-list');
    const $modal = $('#categoryModal');
    const $form = $('#category-form');
    const $submitBtn = $('#category-submit-btn');

    // --- 1. Load All Categories ---
    function loadCategories() {
        $categoryList.html('<div class="col-12 text-center"><span class="spinner-border" role="status"></span><p>Loading categories...</p></div>');
        
        $.ajax({
            url: 'ajax/category_action.php',
            type: 'POST',
            data: { action: 'fetch_all' },
            dataType: 'json',
            success: function(response) {
                $categoryList.empty();
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(function(cat) {
                        const cardHtml = `
                            <div class="col" data-id="${cat.id}">
                                <div class="card item-card">
                                    <img src="${cat.image_path}" class="card-img-top item-card-img-top" alt="${cat.name}">
                                    <div class="card-body">
                                        <h5 class="card-title">${cat.name}</h5>
                                        <p class="card-text">${cat.description || 'No description.'}</p>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span class="badge status-${cat.status}">${cat.status}</span>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${cat.id}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${cat.id}" title="Delete"><i class="bi bi-trash-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $categoryList.append(cardHtml);
                    });
                } else {
                    $categoryList.html('<div class="col-12"><div class="alert alert-info">No categories found. Click the "+" button to add one!</div></div>');
                }
            },
            error: function() {
                $categoryList.html('<div class="col-12"><div class="alert alert-danger">Failed to load categories. Please try again.</div></div>');
            }
        });
    }

    // --- 2. Show "Add" Modal ---
    $('#add-category-btn').on('click', function() {
        window.resetModalForm('categoryModal', 'category-form', 'add_category');
        $('#image-preview').attr('src', 'assets/images/placeholder.png').show(); // Show placeholder
        $modal.modal('show');
    });

    // --- 3. Image Preview ---
    $('#category-image').on('change', function() {
        window.previewImage(this, '#image-preview');
    });

    // --- 4. Form Submission (Add & Update) ---
    $form.on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: 'ajax/category_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $modal.modal('hide');
                    window.showSuccessModal(response.message);
                    loadCategories();
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

    // --- 5. "Edit" Button Click (Event Delegation) ---
    $categoryList.on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        // Reset modal and set action
        window.resetModalForm('categoryModal', 'category-form', 'update_category');
        
        $.ajax({
            url: 'ajax/category_action.php',
            type: 'POST',
            data: { action: 'fetch_single', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var cat = response.data;
                    $('#category-id').val(cat.id);
                    $('#category-name').val(cat.name);
                    $('#category-description').val(cat.description);
                    $('#category-status').val(cat.status);
                    $('#image-preview').attr('src', cat.image_path).show();
                    $modal.modal('show');
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('Failed to fetch category details.');
            }
        });
    });
    
    // --- 6. "Delete" Button Click (Event Delegation) ---
    $categoryList.on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this category? This will also delete all menu items in it.')) {
            $.ajax({
                url: 'ajax/category_action.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.showSuccessModal(response.message);
                        loadCategories();
                    } else {
                        window.showErrorModal(response.message);
                    }
                },
                error: function() {
                    window.showErrorModal('Failed to delete category.');
                }
            });
        }
    });

    // --- Initial Load ---
    loadCategories();
});