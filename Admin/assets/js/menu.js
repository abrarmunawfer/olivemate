$(document).ready(function() {
    const $menuList = $('#menu-list');
    const $modal = $('#menuModal');
    const $form = $('#menu-form');
    const $submitBtn = $('#menu-submit-btn');
    const $categorySelect = $('#menu-category');

    // --- 1. Load Categories Dropdown ---
    function loadCategoriesDropdown() {
        $categorySelect.html('<option value="">Loading...</option>');
        $.ajax({
            url: 'ajax/category_action.php',
            type: 'POST',
            data: { action: 'fetch_all' },
            dataType: 'json',
            success: function(response) {
                $categorySelect.empty().append('<option value="">Select a category</option>');
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(function(cat) {
                        if (cat.status === 'active') { // Only show active categories
                            $categorySelect.append(`<option value="${cat.id}">${cat.name}</option>`);
                        }
                    });
                } else {
                    $categorySelect.html('<option value="">No categories found</option>');
                }
            },
            error: function() {
                $categorySelect.html('<option value="">Error loading categories</option>');
            }
        });
    }

    // --- 2. Load All Menu Items ---
// --- 2. Load All Menu Items (Modified for Search) ---
    function loadMenu(searchTerm = '') { // <-- Add searchTerm parameter
        $menuList.html('<div class="col-12 text-center"><span class="spinner-border" role="status"></span><p>Loading menu items...</p></div>');
        
        let ajaxData = {
            action: 'fetch_all' // Default action
        };

        // If user is searching, change the action and add the term
        if (searchTerm.length > 0) {
            ajaxData.action = 'dySearch';
            ajaxData.term = searchTerm;
        }

        $.ajax({
            url: 'ajax/menu_action.php',
            type: 'POST',
            data: ajaxData, // <-- Use the new ajaxData object
            dataType: 'json',
            success: function(response) {
                $menuList.empty();
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        const popularBadge = item.is_popular == 1 ? '<span class="badge status-popular ms-1">Popular</span>' : '';
                        const specialBadge = item.is_special == 1 ? '<span class="badge status-special ms-1">Special</span>' : '';
                        
                        const cardHtml = `
                            <div class="col" data-id="${item.id}">
                                <div class="card item-card">
                                    <img src="${item.image_path}" class="card-img-top item-card-img-top" alt="${item.name}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title">${item.name}</h5>
                                            <span class="item-price">€${item.price}</span>
                                        </div>
                                        <small class="text-muted d-block mb-2">${item.category_name || 'Uncategorized'}</small>
                                        <small class="text-primary d-block mb-2">Code: ${item.code || 'N/A'}</small>
                                        <p class="card-text">${item.description || 'No description.'}</p>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge status-${item.status}">${item.status}</span>
                                            ${popularBadge}
                                            ${specialBadge}
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${item.id}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${item.id}" title="Delete"><i class="bi bi-trash-fill"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $menuList.append(cardHtml);
                    });
                } else {
                    $menuList.html('<div class="col-12"><div class="alert alert-info">No menu items found. Click the "+" button to add one!</div></div>');
                }
            },
            error: function() {
                $menuList.html('<div class="col-12"><div class="alert alert-danger">Failed to load menu items. Please try again.</div></div>');
            }
        });
    }

    // --- Live Search ---
    $('#menu-search-input').on('keyup', function() {
        var searchTerm = $(this).val();
        loadMenu(searchTerm); // Re-load menu with the search term
    });

    // --- 3. Show "Add" Modal ---
    $('#add-menu-btn').on('click', function() {
        window.resetModalForm('menuModal', 'menu-form', 'add_menu');
        $('#menu-image-preview').attr('src', 'assets/images/placeholder.png').show();
        loadCategoriesDropdown(); // Load categories into select
        $modal.modal('show');
    });

    // --- 4. Image Preview ---
    $('#menu-image').on('change', function() {
        window.previewImage(this, '#menu-image-preview');
    });

    // --- 5. Form Submission (Add & Update) ---
    $form.on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: 'ajax/menu_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $modal.modal('hide');
                    window.showSuccessModal(response.message);
                    loadMenu();
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

    // --- 6. "Edit" Button Click (Event Delegation) ---
    $menuList.on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        window.resetModalForm('menuModal', 'menu-form', 'update_menu');
        loadCategoriesDropdown(); // Load categories first
        
        $.ajax({
            url: 'ajax/menu_action.php',
            type: 'POST',
            data: { action: 'fetch_single', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var item = response.data;
                    $('#menu-id').val(item.id);
                    $('#menu-name').val(item.name);
                    $('#menu-code').val(item.code);
                    $('#menu-price').val(item.price);
                    $('#menu-description').val(item.description);
                    $('#menu-status').val(item.status);
                    
                    // Set toggles
                    $('#is-popular').prop('checked', item.is_popular == 1);
                    $('#is-special').prop('checked', item.is_special == 1);
                    
                    // Set image preview
                    $('#menu-image-preview').attr('src', item.image_path).show();
                    
                    // Set category (needs a small delay for dropdown to load)
                    setTimeout(function() {
                        $('#menu-category').val(item.category_id);
                    }, 500); // Adjust delay if needed
                    
                    $modal.modal('show');
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('Failed to fetch menu item details.');
            }
        });
    });
    
    // --- 7. "Delete" Button Click (Event Delegation) ---
    $menuList.on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this menu item?')) {
            $.ajax({
                url: 'ajax/menu_action.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.showSuccessModal(response.message);
                        loadMenu();
                    } else {
                        window.showErrorModal(response.message);
                    }
                },
                error: function() {
                    window.showErrorModal('Failed to delete menu item.');
                }
            });
        }
    });

    // --- Initial Load ---
    loadMenu();
});