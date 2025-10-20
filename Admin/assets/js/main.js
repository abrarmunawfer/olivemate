$(document).ready(function() {
    
    // --- Live Clock Function ---
// --- Live Clock Function (UK Time) ---
    function updateClock() {
        const now = new Date();
        
        // Options for UK Time (HH:MM:SS)
        const options = { 
            timeZone: 'Europe/London', // Set the time zone to UK
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: false // Use 24-hour format
        };

        // Format the time using toLocaleTimeString
        const formattedTime = now.toLocaleTimeString('en-GB', options);
        
        $('#live-clock').text(formattedTime);
    }
    
    // Update the clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);

    // --- Sidebar Toggle ---
    $('#sidebar-toggle-btn').on('click', function() {
        $('#sidebar').toggleClass('active');
    });

    // --- Logout ---
    $('#logout-btn').on('click', function() {
        $.ajax({
            url: 'ajax/session_action.php',
            type: 'POST',
            data: { action: 'logout' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = 'index.php';
                }
            }
        });
    });

    // --- Universal Modal Functions ---
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    // Global functions to be accessible by other scripts
    window.showSuccessModal = function(message) {
        $('#successModalMessage').text(message);
        successModal.show();
    }

    window.showErrorModal = function(message) {
        $('#errorModalMessage').text(message);
        errorModal.show();
    }
    
    // --- Image Preview ---
    // Shared function for image preview
    window.previewImage = function(input, previewElement) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewElement).attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // --- Reset Modal ---
    // Resets a Bootstrap modal form to its default state
    window.resetModalForm = function(modalId, formId, actionValue) {
        const $modal = $(`#${modalId}`);
        const $form = $(`#${formId}`);
        
        $form[0].reset(); // Reset form fields
        $form.find('input[type="hidden"]').val(''); // Clear hidden fields
        
        // Reset specific action
        $form.find(`#${modalId.replace('Modal', '-action')}`).val(actionValue);
        
        // Hide image preview
        $form.find('img[id$="-preview"]').attr('src', 'assets/images/placeholder.png').hide();
        
        // Reset toggles (if any)
        $form.find('.form-check-input').prop('checked', false);

        // Reset dropdowns
        $form.find('select').prop('selectedIndex', 0);
        
        // Set modal title
        const title = actionValue.includes('add') ? 'Add' : 'Edit';
        const label = modalId.replace('Modal', '').replace('-', ' ');
        $modal.find('.modal-title').text(`${title} ${label.charAt(0).toUpperCase() + label.slice(1)}`);
        
        // Reset submit button state
        $modal.find('button[type="submit"]').prop('disabled', false).html('Save changes');
    }

});