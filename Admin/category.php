<?php
include 'includes/session.php';
check_login(); 

include 'includes/header.php';
?>

<style>
    #category-search-input::placeholder {
        font-size: 12px;
        opacity: 0.7; 
    }
</style>

<button class="btn btn-primary btn-float" id="add-category-btn" title="Add New Category">
    <i class="bi bi-plus-lg"></i>
</button>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="mb-4">
        <input type="text" id="category-search-input" class="form-control" 
            placeholder="Search by category name..." title="Search by category name...">
    </div>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="category-list">
    </div>

<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="category-form" enctype="multipart/form-data">
                <div class="modal-body">
                    
                    <input type="hidden" name="action" id="category-action" value="add_category">
                    <input type="hidden" name="category_id" id="category-id" value="">
                    
                    <div class="mb-3">
                        <label for="category-name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category-name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category-description" class="form-label">Description</label>
                        <textarea class="form-control" id="category-description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category-status" class="form-label">Status</label>
                        <select class="form-select" id="category-status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category-image" class="form-label">Category Image</label>
                        <input class="form-control" type="file" id="category-image" name="image" accept="image/jpeg, image/png, image/gif, image/webp">
                    </div>
                    
                    <div class="mb-3 text-center">
                        <img id="image-preview" src="assets/images/placeholder.png" alt="Image Preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="category-submit-btn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
include 'includes/footer.php';
?>

<script src="assets/js/category.js"></script>