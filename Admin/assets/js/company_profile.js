$(document).ready(function() {
    const $form = $('#company-profile-form');
    const $alert = $('#profile-alert');
    const $submitBtn = $('#save-profile-btn');
    const profileId = $('#profile_id').val();
    const placeholderSrc = 'assets/images/placeholder.png'; // Store placeholder path

    // Store initial image paths to know if remove button should delete server image
    let initialImagePaths = {
        logo_image: null,
        cover_image_1: null,
        cover_image_2: null,
        cover_image_3: null
    };

    // --- 1. Load Profile Data ---
    function loadProfileData() {
        $alert.removeClass('alert-success alert-danger').addClass('d-none');
        $.ajax({
            url: `ajax/company_action.php?action=fetch_profile`, // Use GET for fetch
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    $('#company_name').val(data.company_name);
                    $('#address').val(data.address);
                    $('#contact_number').val(data.contact_number);
                    $('#email').val(data.email);
                    $('#owner_name').val(data.owner_name);

                    // Store initial paths and set image previews
                    initialImagePaths.logo_image = (data.logo_path !== placeholderSrc) ? data.logo_path : null;
                    initialImagePaths.cover_image_1 = (data.cover1_path !== placeholderSrc) ? data.cover1_path : null;
                    initialImagePaths.cover_image_2 = (data.cover2_path !== placeholderSrc) ? data.cover2_path : null;
                    initialImagePaths.cover_image_3 = (data.cover3_path !== placeholderSrc) ? data.cover3_path : null;

                    setImagePreview('logo_image', data.logo_path);
                    setImagePreview('cover_image_1', data.cover1_path);
                    setImagePreview('cover_image_2', data.cover2_path);
                    setImagePreview('cover_image_3', data.cover3_path);

                } else {
                    $alert.text(response.message).addClass('alert-danger').removeClass('d-none');
                }
            },
            error: function() {
                $alert.text('Failed to load company profile data.').addClass('alert-danger').removeClass('d-none');
            }
        });
    }

    // --- 2. Set Image Preview and Show/Hide Elements ---
    function setImagePreview(inputId, src) {
        const previewId = $('#' + inputId).data('preview');
        const $previewContainer = $('#' + previewId + '-container');
        const $previewImg = $('#' + previewId);
        const $uploadLabel = $(`label[for="${inputId}"]`);
        const $removeFlag = $('#remove_' + inputId);

        if (src && src !== placeholderSrc) {
            $previewImg.attr('src', src);
            $previewContainer.show();
            $uploadLabel.hide(); // Hide the upload prompt when preview is shown
            $removeFlag.val('0'); // Ensure remove flag is off initially
        } else {
            $previewImg.attr('src', '#'); // Clear src
            $previewContainer.hide();
            $uploadLabel.show(); // Show the upload prompt
            $removeFlag.val('0'); // Ensure remove flag is off
        }
    }

    // --- 3. Image Input Change (Preview) ---
    $('.image-input').on('change', function() {
        const inputId = $(this).attr('id');
        const previewId = $(this).data('preview');
        const removeFlagId = '#remove_' + inputId;
        
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                setImagePreview(inputId, e.target.result); // Show preview
            }
            reader.readAsDataURL(this.files[0]);
            $(removeFlagId).val('0'); // Reset remove flag if user uploads new image
        }
    });

    // --- 4. Remove Image Button Click ---
    $('.remove-image-btn').on('click', function() {
        const inputId = $(this).data('input');
        const removeFlagId = $(this).data('remove-flag');

        // Reset the file input
        $('#' + inputId).val(''); 
        
        // Hide preview, show upload label
        setImagePreview(inputId, null); 

        // Set the remove flag *only if* there was an initial image loaded
        if (initialImagePaths[inputId]) { 
            $('#' + removeFlagId).val('1'); 
            // console.log("Setting remove flag for " + inputId); // For debugging
        } else {
             $('#' + removeFlagId).val('0'); // No initial image, so nothing to remove on server
        }
    });


    // --- 5. Form Submission ---
    $form.on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        $alert.removeClass('alert-success alert-danger').addClass('d-none');
        
         // For debugging: Log FormData contents
        // for (var pair of formData.entries()) {
        //     console.log(pair[0]+ ': '+ (pair[1] instanceof File ? pair[1].name : pair[1]));
        // }

        $.ajax({
            url: 'ajax/company_action.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    $alert.text(response.message).addClass('alert-success').removeClass('d-none');
                    // Reload data to show updated images and reset initial paths
                    loadProfileData(); 
                } else {
                    $alert.text(response.message).addClass('alert-danger').removeClass('d-none');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                 console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                 $alert.text('An unknown error occurred during submission. Check console for details.').addClass('alert-danger').removeClass('d-none');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('<i class="bi bi-check-lg me-2"></i> Save Changes');
            }
        });
    });

    // --- Initial Load ---
    loadProfileData();
});