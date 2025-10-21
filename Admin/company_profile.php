<?php
include 'includes/session.php';
check_login(); 

include 'includes/header.php';
?>

<style>
    
.glass-card {
    background: rgba(255, 255, 255, 0.15); 
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px); 
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1); 
    color: var(--c-brown); 
}

.glass-card .card-body {
    background: transparent !important;
}

.form-section-title {
    color: var(--c-brown);
    font-weight: 600;
    border-bottom: 2px solid var(--c-green-light-rgba);
    padding-bottom: 10px;
}

.form-floating > .form-control {
    background-color: rgba(255, 255, 255, 0.3);
    border: none;
    border-bottom: 1px solid rgba(74, 64, 51, 0.3);
    border-radius: 8px 8px 0 0; 
    color: var(--c-brown);
    height: calc(3.5rem + 2px);
    line-height: 1.25;
}
.form-floating > .form-control::placeholder { 
  color: transparent;
}
.form-floating > .form-control:focus,
.form-floating > .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem;
    padding-bottom: .625rem;
    background-color: rgba(255, 255, 255, 0.4);
     border-bottom: 1px solid var(--c-green-dark); 
}

.form-floating > label {
    padding: 1rem .75rem;
    color: rgba(74, 64, 51, 0.6);
    font-weight: 500;
}
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    opacity: .75;
    transform: scale(.85) translateY(-.5rem) translateX(.15rem);
    color: var(--c-green-dark); 
}

.glass-card .form-control:not(textarea) { 
     background-color: rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: var(--c-brown);
    padding: 12px 15px;
}
.glass-card .form-control:focus {
    background-color: rgba(255, 255, 255, 0.4);
    border-color: var(--c-green-light);
    box-shadow: 0 0 0 3px rgba(148, 161, 102, 0.2); 
    color: var(--c-brown);
}
.glass-card ::placeholder {
  color: rgba(74, 64, 51, 0.5);
  opacity: 1;
}
.glass-card :-ms-input-placeholder { 
  color: rgba(74, 64, 51, 0.5);
}
.glass-card ::-ms-input-placeholder { 
  color: rgba(74, 64, 51, 0.5);
}

.image-upload-box {
    position: relative;
    border: 2px dashed rgba(74, 64, 51, 0.3);
    border-radius: 12px;
    padding: 15px;
    background-color: rgba(255, 255, 255, 0.1);
    min-height: 150px; 
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.image-upload-box.small-box {
    min-height: 120px;
    padding: 10px;
}

.image-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 10;
}

.image-upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--c-brown);
    opacity: 0.7;
    transition: opacity 0.3s ease;
    text-align: center;
    padding: 10px;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 1;
}

.image-preview-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 10px; 
    overflow: hidden; 
    z-index: 5; 
}


.image-upload-label i {
    font-size: 2rem;
    margin-bottom: 8px;
}
.image-upload-label span {
    font-weight: 500;
    font-size: 0.9rem;
}

.image-upload-box:hover .image-upload-label {
    opacity: 1;
     color: var(--c-green-dark);
}

.image-preview-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 10px; 
    overflow: hidden; 
    z-index: 5; 
}

.image-preview-container img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    background-color: rgba(255, 255, 255, 0.2); 
}
.small-box .image-preview-container img {
    object-fit: cover; 
}

.remove-image-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 15;
    line-height: 1;
    padding: 0;
    transition: background-color 0.2s ease;
}
.remove-image-btn:hover {
    background-color: rgba(217, 83, 79, 0.9);
}

#save-profile-btn {
     padding: 12px 30px;
     font-size: 1.1rem;
     font-weight: 600;
     border-radius: 50px;
}

.glass-card .alert {
    background-color: rgba(255, 255, 255, 0.3); 
    border: 1px solid transparent;
}
.glass-card .alert-success {
    color: #0f5132;
    border-color: rgba(186, 219, 204, 0.5); 
    background-color: rgba(209, 231, 221, 0.6);
}
.glass-card .alert-danger {
    color: #721c24;
    border-color: rgba(245, 198, 203, 0.5); 
    background-color: rgba(248, 215, 218, 0.6);
}

</style>

<div class="glass-card"> 
    <div class="card-body p-lg-5"> 
    
        <div id="profile-alert" class="alert d-none mx-auto mb-4" role="alert" style="max-width: 80%;"></div>

        <form id="company-profile-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_profile">
            <input type="hidden" name="profile_id" id="profile_id" value="1"> 
            
            <div class="row g-5"> 
                <div class="col-lg-6">
                    <h4 class="mb-4 form-section-title">Company Details</h4>
                    
                    <div class="mb-4 form-floating">
                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" required>
                        <label for="company_name">Company Name</label>
                    </div>
                    
                    <div class="mb-4 form-floating">
                        <textarea class="form-control" id="address" name="address" placeholder="Address" style="height: 100px"></textarea>
                        <label for="address">Address</label>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 form-floating">
                            <input type="tel" class="form-control" id="contact_number" name="contact_number" placeholder="Contact Number">
                             <label for="contact_number">Contact Number</label>
                        </div>
                        <div class="col-md-6 form-floating">
                             <input type="email" class="form-control" id="email" name="email" placeholder="Public Email">
                             <label for="email">Public Email</label>
                        </div>
                    </div>
                   
                    <div class="mb-4 form-floating">
                        <input type="text" class="form-control" id="owner_name" name="owner_name" placeholder="Owner Name">
                        <label for="owner_name">Owner Name</label>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h4 class="mb-4 form-section-title">Branding Images</h4>
                    
                    <div class="mb-4 image-upload-box">
                        <label class="form-label">Company Logo</label>
                        <div class="image-input-wrapper">
                            <input class="image-input" type="file" id="logo_image" name="logo_image" accept="image/png, image/jpeg, image/gif, image/webp" data-preview="logo-preview">
                            <input type="hidden" name="remove_logo_image" id="remove_logo_image" value="0">
                            
                            <label for="logo_image" class="image-upload-label">
                                <i class="bi bi-upload"></i>
                                <span>Choose Logo</span>
                            </label>
                            <div class="image-preview-container" id="logo-preview-container" style="display: none;">
                                <img id="logo-preview" src="#" alt="Logo Preview">
                                <button type="button" class="remove-image-btn" data-input="logo_image" data-preview="logo-preview" data-remove-flag="remove_logo_image">&times;</button>
                            </div>
                        </div>
                         <small class="form-text text-muted d-block mt-1">Transparent PNG or WebP recommended.</small>
                    </div>
                    
                    <hr class="my-5">

                    <label class="form-label mb-3">Homepage Cover Images (Max 3)</label>
                    <div class="row g-3">
                        <div class="col-md-4 image-upload-box small-box">
                            <div class="image-input-wrapper">
                                <input class="image-input" type="file" id="cover_image_1" name="cover_image_1" accept="image/jpeg, image/png, image/gif, image/webp" data-preview="cover1-preview">
                                <input type="hidden" name="remove_cover_image_1" id="remove_cover_image_1" value="0">
                                
                                <label for="cover_image_1" class="image-upload-label">
                                    <i class="bi bi-upload"></i> <span class="d-none d-lg-inline">Cover 1</span>
                                </label>
                                <div class="image-preview-container" id="cover1-preview-container" style="display: none;">
                                    <img id="cover1-preview" src="#" alt="Cover 1 Preview">
                                     <button type="button" class="remove-image-btn" data-input="cover_image_1" data-preview="cover1-preview" data-remove-flag="remove_cover_image_1">&times;</button>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-4 image-upload-box small-box">
                            <div class="image-input-wrapper">
                                <input class="image-input" type="file" id="cover_image_2" name="cover_image_2" accept="image/jpeg, image/png, image/gif, image/webp" data-preview="cover2-preview">
                                 <input type="hidden" name="remove_cover_image_2" id="remove_cover_image_2" value="0">
                                 
                                <label for="cover_image_2" class="image-upload-label">
                                     <i class="bi bi-upload"></i> <span class="d-none d-lg-inline">Cover 2</span>
                                </label>
                                 <div class="image-preview-container" id="cover2-preview-container" style="display: none;">
                                    <img id="cover2-preview" src="#" alt="Cover 2 Preview">
                                     <button type="button" class="remove-image-btn" data-input="cover_image_2" data-preview="cover2-preview" data-remove-flag="remove_cover_image_2">&times;</button>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-4 image-upload-box small-box">
                           <div class="image-input-wrapper">
                                <input class="image-input" type="file" id="cover_image_3" name="cover_image_3" accept="image/jpeg, image/png, image/gif, image/webp" data-preview="cover3-preview">
                                <input type="hidden" name="remove_cover_image_3" id="remove_cover_image_3" value="0">
                                
                                <label for="cover_image_3" class="image-upload-label">
                                   <i class="bi bi-upload"></i> <span class="d-none d-lg-inline">Cover 3</span>
                                </label>
                                <div class="image-preview-container" id="cover3-preview-container" style="display: none;">
                                    <img id="cover3-preview" src="#" alt="Cover 3 Preview">
                                     <button type="button" class="remove-image-btn" data-input="cover_image_3" data-preview="cover3-preview" data-remove-flag="remove_cover_image_3">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <div class="mt-5 text-center"> 
                <button type="submit" class="btn btn-primary btn-lg" id="save-profile-btn">
                    <i class="bi bi-check-lg me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include 'includes/footer.php';
?>

<script src="assets/js/company_profile.js"></script>