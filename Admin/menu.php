<?php
// Include and run session check
include 'includes/session.php';
check_login(); // Redirect to index.php if not logged in

// Include the header
include 'includes/header.php';
?>

<button class="btn btn-primary btn-float" id="add-menu-btn" title="Add New Menu Item">
    <i class="bi bi-plus-lg"></i>
</button>

<div class="d-flex justify-content-between align-items-center mb-4">
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="menu-list">
    </div>

<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="menuModalLabel">Add Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="menu-form" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <input type="hidden" name="action" id="menu-action" value="add_menu">
                    <input type="hidden" name="menu_id" id="menu-id" value="">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="menu-name" class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="menu-name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="menu-category" class="form-label">Category</label>
                                <select class="form-select" id="menu-category" name="category_id" required>
                                    <option value="">Loading categories...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="menu-price" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="menu-price" name="price" step="0.01" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="menu-status" class="form-label">Status</label>
                                <select class="form-select" id="menu-status" name="status" required>
                                    <option value="available">Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="menu-image" class="form-label">Item Image</label>
                                <input class="form-control" type="file" id="menu-image" name="image" accept="image/jpeg, image/png, image/gif, image/webp">
                            </div>
                            
                            <div class="mb-3 text-center">
                                <img id="menu-image-preview" src="assets/images/placeholder.png" alt="Image Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                            </div>
                            
                            <div class="d-flex justify-content-around mt-4">
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is-popular" name="is_popular" value="1">
                                    <label class="form-check-label" for="is-popular">Popular?</label>
                                </div>
                                <div class="form-check form-switch form-check-lg">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is-special" name="is_special" value="1">
                                    <label class="form-check-label" for="is-special">Special?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menu-description" class="form-label">Description</label>
                        <textarea class="form-control" id="menu-description" name="description" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="menu-submit-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>

<script src="assets/js/menu.js"></script>