<?php
// Include and run session check
include 'includes/session.php';
check_login(); // Redirect to index.php if not logged in

// Include the header
include 'includes/header.php';
?>

<!-- Floating "Add" Button -->
<button class="btn btn-primary btn-float" id="add-offer-btn" title="Add New Offer">
    <i class="bi bi-plus-lg"></i>
</button>

<!-- Page Title -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Offer Management</h1>
</div>

<!-- Search Box -->
<style>
    #offer-search-input::placeholder { font-size: 12px; opacity: 0.7; }
</style>
<div class="mb-4">
    <input type="text" id="offer-search-input" class="form-control" 
           placeholder="Search by offer title..." 
           style="font-size: 12px;">
</div>

<!-- Offer Card Grid -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="offer-list">
    <!-- Offers will be loaded here by AJAX -->
</div>

<!-- Add/Edit Offer Modal (Corrected) -->
<div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="offerModalLabel">Add Offer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="offer-form" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <input type="hidden" name="action" id="offer-action" value="add_offer">
                    <input type="hidden" name="offer_id" id="offer-id" value="">
                    
                    <div class="mb-3">
                        <label for="offer-title" class="form-label">Offer Title</label>
                        <input type="text" class="form-control" id="offer-title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="offer-description" class="form-label">Description</label>
                        <textarea class="form-control" id="offer-description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="offer-actual-price" class="form-label">Actual Price (€)</label>
                            <input type="number" class="form-control" id="offer-actual-price" name="actual_price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                             <label for="offer-percentage" class="form-label">Offer (%)</label>
                            <input type="number" class="form-control" id="offer-percentage" name="offer_percentage" min="0" max="100" placeholder="e.g., 20">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="offer-price" class="form-label">Offer Price (€)</label>
                        <input type="number" class="form-control" id="offer-price" name="offer_price" step="0.01" min="0" >
                    </div>
                    
                    <div class="mb-3">
                        <label for="offer-status" class="form-label">Status</label>
                        <select class="form-select" id="offer-status" name="status" required>
                            <option value="active">Active (Visible)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="offer-image" class="form-label">Offer Image</label>
                        <input class="form-control" type="file" id="offer-image" name="image" accept="image/jpeg, image/png, image/gif, image/webp">
                    </div>
                    
                    <div class="mb-3 text-center">
                        <img id="image-preview" src="assets/images/placeholder.png" alt="Image Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="offer-submit-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>
<script>
    $(document).ready(function() {
    const $offerList = $('#offer-list');
    const $modal = $('#offerModal');
    const $form = $('#offer-form');
    const $submitBtn = $('#offer-submit-btn');

    const $actualPrice = $('#offer-actual-price');
    const $percentage = $('#offer-percentage');
    const $offerPrice = $('#offer-price');

    let isCalculating = false;

    function loadOffers(searchTerm = '') {
        $offerList.html('<div class="col-12 text-center"><span class="spinner-border" role="status"></span><p>Loading offers...</p></div>');
        
        let ajaxData = { action: 'fetch_all' };
        if (searchTerm.length > 0) {
            ajaxData.action = 'dySearch';
            ajaxData.term = searchTerm;
        }

        $.ajax({
            url: 'ajax/offer_action.php',
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(response) {
                $offerList.empty();
                if (response.status === 'success' && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        let priceHtml = '';
                        if (item.actual_price > 0) {
                            priceHtml = `
                                <div class="price-container">
                                    <span class="actual-price"><s>€${parseFloat(item.actual_price).toFixed(2)}</s></span>
                                    <span class="item-price">€${parseFloat(item.offer_price).toFixed(2)}</span>
                                    ${item.offer_percentage > 0 ? `<span class="badge bg-danger ms-2">${item.offer_percentage}% OFF</span>` : ''}
                                </div>
                            `;
                        }
                        
                        const cardHtml = `
                            <div class="col" data-id="${item.id}">
                                <div class="card item-card">
                                    <img src="${item.image_path}" class="card-img-top item-card-img-top" alt="${item.title}">
                                    <div class="card-body">
                                        <h5 class="card-title">${item.title}</h5>
                                        ${priceHtml}
                                        <p class="card-text">${item.description || 'No description.'}</p>
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
                        $offerList.append(cardHtml);
                    });
                } else {
                    $offerList.html('<div class="col-12"><div class="alert alert-info">No offers found. Click the "+" button to add one!</div></div>');
                }
            },
            error: function() {
                $offerList.html('<div class="col-12"><div class="alert alert-danger">Failed to load offers. Please try again.</div></div>');
            }
        });
    }

    $('#offer-search-input').on('keyup', function() {
        var searchTerm = $(this).val();
        loadOffers(searchTerm);
    });

    $('#add-offer-btn').on('click', function() {
        window.resetModalForm('offerModal', 'offer-form', 'add_offer');
        $('#offerModalLabel').text('Add Offer');
        $('#offer-actual-price').val('');
        $('#offer-percentage').val('');
        $('#offer-price').val('');
        $('#image-preview').attr('src', 'assets/images/placeholder.png').hide();
        $modal.modal('show');
    });

    $('#offer-image').on('change', function() {
        window.previewImage(this, '#image-preview');
    });

    $form.on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: 'ajax/offer_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $modal.modal('hide');
                    window.showSuccessModal(response.message);
                    loadOffers();
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

    $offerList.on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        window.resetModalForm('offerModal', 'offer-form', 'update_offer');
        $('#offerModalLabel').text('Edit Offer');
        
        $.ajax({
            url: 'ajax/offer_action.php',
            type: 'POST',
            data: { action: 'fetch_single', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var item = response.data;
                    $('#offer-id').val(item.id);
                    $('#offer-title').val(item.title);
                    $('#offer-description').val(item.description);
                    $('#offer-status').val(item.status);
                    
                    $('#offer-actual-price').val(item.actual_price);
                    $('#offer-percentage').val(item.offer_percentage);
                    $('#offer-price').val(item.offer_price);
                    
                    $('#image-preview').attr('src', item.image_path).show();
                    
                    $modal.modal('show');
                } else {
                    window.showErrorModal(response.message);
                }
            },
            error: function() {
                window.showErrorModal('Failed to fetch offer details.');
            }
        });
    });
    
    $offerList.on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this offer?')) {
            $.ajax({
                url: 'ajax/offer_action.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.showSuccessModal(response.message);
                        loadOffers();
                    } else {
                        window.showErrorModal(response.message);
                    }
                },
                error: function() {
                    window.showErrorModal('Failed to delete offer.');
                }
            });
        }
    });

    function calculatePriceFromPercentage() {
        if (isCalculating) return;
        isCalculating = true;
        
        const actualPrice = parseFloat($actualPrice.val());
        const percentage = parseFloat($percentage.val());
        
        if (!isNaN(actualPrice) && !isNaN(percentage) && percentage >= 0 && percentage <= 100) {
            const discount = (actualPrice * percentage) / 100;
            const offerPrice = actualPrice - discount;
            $offerPrice.val(offerPrice.toFixed(2));
        } else if (!isNaN(actualPrice)) {
            $offerPrice.val(actualPrice.toFixed(2));
        } else {
            $offerPrice.val('');
        }
        isCalculating = false;
    }

    function calculatePercentageFromPrice() {
        if (isCalculating) return;
        isCalculating = true;

        const actualPrice = parseFloat($actualPrice.val());
        const offerPrice = parseFloat($offerPrice.val());

        if (!isNaN(actualPrice) && !isNaN(offerPrice) && actualPrice > 0 && offerPrice <= actualPrice && offerPrice >= 0) {
            const discount = actualPrice - offerPrice;
            const percentage = (discount / actualPrice) * 100;
            $percentage.val(percentage.toFixed(0));
        } else {
            if(offerPrice > actualPrice || isNaN(actualPrice) || isNaN(offerPrice)){
                 $percentage.val('');
            }
        }
        isCalculating = false;
    }

    $actualPrice.on('input', calculatePriceFromPercentage);
    $percentage.on('input', calculatePriceFromPercentage);
    $offerPrice.on('input', calculatePercentageFromPrice);

    loadOffers();
});
</script>