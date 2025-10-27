$(document).ready(function() {

    // Initialize DataTables for Testimonials
    var testimonialsTable = $('#testimonialsTable').DataTable({
        "processing": true,
        "serverSide": false, // Use client-side processing
        "ajax": {
            "url": "ajax/testimonial_action.php",
            "type": "POST",
            "data": { "action": "fetch_testimonials" },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id" },
            {
                "data": "user_id", // Display User ID and Username
                "render": function(data, type, row) {
                    return `${row.user_username || 'N/A'} (#${data})`; // Show username and ID
                }
             },
             { "data": "customer_name" }, // Display name provided by customer
            {
                "data": "testimonial_text",
                "render": function(data) {
                    // Truncate long testimonials
                    return data ? (data.length > 40 ? data.substring(0, 40) + '...' : data) : 'N/A';
                }
             },
            {
                "data": "rating",
                "render": function(data) {
                    // Display stars
                    let stars = '';
                    let rating = parseInt(data) || 0;
                    for (let i = 0; i < 5; i++) {
                        stars += `<i class="bi bi-star${i < rating ? '-fill' : ''}" style="color: #ffc107;"></i>`;
                    }
                    return `<span title="${rating}/5">${stars}</span>`;
                }
            },
            {
                "data": "created_at", // Changed from submitted_at
                "render": function(data) {
                    return data ? new Date(data + 'Z').toLocaleString() : 'N/A'; // Add Z for UTC
                }
            },
            {
                "data": "isVisible", // Boolean true/false
                "render": function(data) {
                    return data
                        ? '<span class="badge bg-success">Visible</span>'
                        : '<span class="badge bg-secondary">Hidden</span>';
                }
            },
            {
                "data": null, // Action column
                "orderable": false,
                "render": function(data, type, row) {
                    const isVisible = row.isVisible; // Boolean true/false
                    const buttonText = isVisible ? 'Hide' : 'Show';
                    const buttonClass = isVisible ? 'btn-outline-secondary' : 'btn-outline-success';
                    const iconClass = isVisible ? 'bi-eye-slash-fill' : 'bi-eye-fill';

                    return `
                        <button class="btn btn-sm ${buttonClass} btn-toggle-visibility" data-id="${row.id}" data-visible="${isVisible}">
                            <i class="bi ${iconClass}"></i> ${buttonText}
                        </button>
                    `;
                }
            }
        ],
        "responsive": true,
        "order": [[ 5, "desc" ]], // Default sort by Created At descending
        "language": {
            "emptyTable": "No testimonials found.",
            "zeroRecords": "No matching testimonials found",
            "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
        }
    });

    // Handle Toggle Visibility Button Click (Logic remains the same)
    $('#testimonialsTable tbody').on('click', '.btn-toggle-visibility', function() {
        const testimonialId = $(this).data('id');
        const currentVisibility = $(this).data('visible'); // Boolean true/false
        const newVisibility = !currentVisibility; // Toggle the boolean

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

        $.ajax({
            url: 'ajax/testimonial_action.php',
            type: 'POST',
            data: {
                action: 'toggle_visibility',
                id: testimonialId,
                isVisible: newVisibility // Send true or false
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' || response.status === 'info') { // Also reload on 'info'
                    testimonialsTable.ajax.reload(null, false); // Reload data
                    // Optionally show success/info message
                    if (response.status === 'success') {
                         // Example: You might need a function like showToast defined elsewhere
                         // showToast('success', response.message);
                    }
                } else {
                    window.showErrorModal(response.message || 'Failed to update visibility.');
                     $btn.prop('disabled', false).html('Error'); // Reset button only on definite error
                }
            },
            error: function() {
                window.showErrorModal('An error occurred while updating visibility.');
                 $btn.prop('disabled', false).html('Error');
            }
        });
    });

});