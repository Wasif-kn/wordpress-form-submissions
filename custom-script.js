jQuery(document).ready(function ($) {

    $('#form').submit(function (e) {
        e.preventDefault();

        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var father_name = $('#father_name').val();

        var user_contact = $('#user_contact').val();
        var user_email = $('#user_email').val();

        var user_amount = $('#user_amount').val();
        var user_address = $('#user_address').val();
        var user_date = $('#user_date').val();
        console.log(user_date);
        var user_private = $('#user_private').val();
        var user_hidden = $('#user_hidden').val();

        $.ajax({
            type: 'POST',
            url: custom_vars.ajax_url,
            data: {
                action: 'submit_form_data',
                first_name: first_name,
                last_name: last_name,
                father_name: father_name,
                user_email: user_email,
                user_contact: user_contact,
                user_amount: user_amount,
                user_address: user_address,
                user_date: user_date,
                user_private: user_private,
                user_hidden: user_hidden
 
                
            },
            success: function (response) {
                // Handle success, if needed
                console.log('Form submitted successfully!');


                $('#first_name').val('');
                $('#last_name').val('');
                $('#father_name').val('');
                $('#user_email').val('');
                $('#user_contact').val('');
                $('#user_amount').val('');
                $('#user_address').val('');
                $('#user_date').val('');
                $('#user_private').val('')
                $('#user_hidden').val('');


                // Update the table with new data

                // Update the table with new data
                $.ajax({
                    type: 'GET',
                    url: window.location.href, // or the specific URL where your table content is generated
                    success: function (newData) {
                        $('#form_data').html($(newData).find('#form_data').html());
                        // Check if DataTable is already initialized
                        if (!$.fn.DataTable.isDataTable('#form_data')) {
                            // If DataTable is not initialized, initialize it
                            new DataTable('#form_data', {
                                responsive: true,
                            });
                        }
                    },
                    error: function (error) {
                        // Handle errors, if any
                        console.error('Error occurred while fetching updated data:', error);
                    }
                });
            },
            error: function (error) {
                // Handle errors, if any
                console.error('Error occurred:', error);
            }
        });
    });
});

function clickk(event) {
    const button = event.currentTarget;

    const attribute_first_name = button.getAttribute("data-fs-first-name");
    const attribute_last_name = button.getAttribute("data-fs-last-name");
    const attribute_father_name = button.getAttribute("data-fs-father-name");
    const attribute_user_contact = button.getAttribute("data-fs-user-contact");
    const attribute_user_email = button.getAttribute("data-fs-user-email");
    const attribute_user_amount = button.getAttribute("data-fs-user-amount");
    const attribute_user_address = button.getAttribute("data-fs-user-address");
    const attribute_user_date = button.getAttribute("data-fs-user-date");
    const attribute_privacy_status = button.getAttribute("data-fs-privacy-status");
    const attribute_user_hidden = button.id;

    jQuery('#first_name').val(attribute_first_name);
    jQuery('#last_name').val(attribute_last_name);
    jQuery('#father_name').val(attribute_father_name);

    jQuery('#user_contact').val(attribute_user_contact);
    jQuery('#user_email').val(attribute_user_email);
    jQuery('#user_amount').val(attribute_user_amount);
    jQuery('#user_address').val(attribute_user_address);
    jQuery('#user_date').val(attribute_user_date);

    const desiredSelectValue = attribute_privacy_status === "User Not Private" ? "No" : "Yes";
    jQuery('#user_private').val(desiredSelectValue);
    jQuery('#user_hidden').val(attribute_user_hidden);
}





function deleteRow(event) {
    const button = event.currentTarget;
    const rowId = button.getAttribute("data-id");

    
    // Confirm deletion with the user (optional)
    const confirmDelete = confirm("Are you sure you want to delete this row?");
    if (!confirmDelete) {
        return;
    }

    // Make an AJAX request to delete the row
    jQuery.ajax({
        type: 'POST',
        url: custom_vars.ajax_url,
        data: {
            action: 'delete_form_data',
            row_id: rowId
        },
        success: function (response) {
            // Handle success, if needed
            console.log('Row deleted successfully!');
            // Update the table with new data
            jQuery.ajax({
                type: 'GET',
                url: window.location.href, // or the specific URL where your table content is generated
                success: function (newData) {
                    jQuery('#form_data').html(jQuery(newData).find('#form_data').html());
                    // Check if DataTable is already initialized
                    if (!jQuery.fn.DataTable.isDataTable('#form_data')) {
                        // If DataTable is not initialized, initialize it
                        new DataTable('#form_data', {
                            responsive: true,
                        });
                    }
                },
                error: function (error) {
                    // Handle errors, if any
                    console.error('Error occurred while fetching updated data:', error);
                }
            });
        },
        error: function (error) {
            // Handle errors
            console.error('Error occurred:', error);
            // Display an error message to the user, if needed
        }
    });
}
