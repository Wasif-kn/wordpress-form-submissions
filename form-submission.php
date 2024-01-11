<?php
/*
Version: 1.0
Author: Wasif Khan
*/

/**
 * Plugin Name: Form Submission
 * Plugin URI: https://github.com/Wasif-kn/WP-Fire-Crackers
 * Description: Submits Form and Shows Submitted Data
 * Version: 1.0.0
 * Author: Wasif Khan
 * Author URI: https://github.com/Wasif-kn
 * License: GPL-2.0+
 * Text Domain: form_submission
 * Domain Path: /languages
 */



if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue custom script and localize with AJAX URL
// Enqueue jQuery and custom script



function enqueue_custom_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-script', plugins_url('/', __FILE__) . 'custom-script.js', array('jquery'), '1.0.0', true);

    // Localize the script with new data
    $script_data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('custom-script', 'custom_vars', $script_data);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


function activate_form_submission()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'form_submission_data';


    // Check if the table already exists in the database
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        return; // Table already exists, no need to create a new table
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        father_name varchar(100) NOT NULL,
        user_email varchar(100) DEFAULT '' NOT NULL,
        user_contact varchar(20) DEFAULT '' NOT NULL,
        user_amount varchar(20) DEFAULT '' NOT NULL,
        user_private varchar(20) DEFAULT '' NOT NULL,
        user_address varchar(100) NOT NULL,
        user_date DATE,
        date_added DATE,
        date_updated DATE,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $result = $wpdb->query($sql);

    if ($result === false) {
        error_log("Error creating table: " . $wpdb->last_error);
    }
}

// Register plugin activation hook
register_activation_hook(__FILE__, 'activate_form_submission');



// Function to register a menu page in WordPress admin
function register_form_submission_page()
{
    add_menu_page(
        'Form Submit',
        'Submissions',
        'manage_options',
        'form_submission_page',
        'render_form_submission_page',
        'dashicons-welcome-widgets-menus',
        90
    );
}

// Add action to create the menu page
add_action('admin_menu', 'register_form_submission_page');

function get_form_submission_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'form_submission_data';

    // Query to fetch all data from the table in alphabetical order by 'full_name'
    $query = "SELECT * FROM $table_name ORDER BY first_name ASC";

    // Get results from the database
    $results = $wpdb->get_results($query, ARRAY_A);

    return $results;
}

function render_form_submission_page()
{
    enqueue_custom_scripts();

    ?>

    <head>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    </head>

    <!-- HTML form for adding Data -->
    <div class="data_form">
        <form method="post" id="form">

            <p><label>First Name</label><input type="text" id="first_name" name="first_name" required></p>

            <p><label>Last Name</label><input type="text" id="last_name" name="last_name" required></p>

            <p><label>Father Name</label><input type="text" id="father_name" name="father_name" required></p>

            <p><label>Email</label><input type="email" name="user_email" id="user_email" required></p>

            <p><label>Contact</label><input type="number" id="user_contact" name="user_contact" required></p>

            <p><label>Amount</label><input type="number" name="user_amount" id="user_amount" required></p>


            <p><label>User Private?</label>
                <select name="user_private" id="user_private" required>
                    <option value="Yes">Yes</option>
                    <option value="No" selected="selected">No</option>
                </select>
            </p>

            <p><label>Address</label><input type="text" name="user_address" id="user_address" required></p>

            <p style="width: 95.5%;"><label>Date</label><input type="date" name="user_date" id="user_date" required></p>

            <p><input type="hidden" value="" name="user_hidden" id="user_hidden"></p>

            <p style="width: 95.5%;"><input type="submit" value="Submit Data" id="submit" class="btn"></p>

        </form>
    </div>

    <!-- Display the list in a table -->
    <div class="wrap" style="width: 80%; margin: auto; margin-top: 30px;">
        <table id="form_data">
            <thead>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Father Name</th>

                <th>Email</th>
                <th>Contact</th>
                <th>Amount</th>
                <th>Address</th>
                <th>Date</th>

                <th>Privacy Status</th>
                <th>Edit Data</th>

            </thead>
            <?php
            // Fetch and display data in the table
            $data = get_form_submission_data();
            foreach ($data as $key => $value) {
                $id = $value['id'];
                $user_amount = $value['user_amount'];
                $first_name = $value['first_name'];
                $last_name = $value['last_name'];
                $father_name = $value['father_name'];
                $user_email = $value['user_email'];
                $user_contact = $value['user_contact'];
                $user_address = $value['user_address'];
                $user_date = $value['user_date'];


                if ($value['user_private'] == 'Yes') {
                    $privacy_status = 'User Private';
                } else {
                    $privacy_status = 'User Not Private';
                }

                ?>
                <tr>
                    <td>
                        <?php esc_html_e($first_name, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($last_name, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($father_name, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_email, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_contact, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_amount, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_address, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_date, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($privacy_status, 'form_submission_data'); ?>
                    </td>

                    <td class="buttons-wrap">
                        <button class="editbtn admin-btns"
                            data-fs-first-name="<?php esc_html_e($first_name, 'form_submission_data'); ?>"
                            data-fs-last-name="<?php esc_html_e($last_name, 'form_submission_data'); ?>"
                            data-fs-father-name="<?php esc_html_e($father_name, 'form_submission_data'); ?>"
                            data-fs-user-email="<?php esc_html_e($user_email, 'form_submission_data'); ?>"
                            data-fs-user-contact="<?php esc_html_e($user_contact, 'form_submission_data'); ?>"
                            data-fs-user-amount="<?php esc_html_e($user_amount, 'form_submission_data'); ?>"
                            data-fs-privacy-status="<?php esc_html_e($privacy_status, 'form_submission_data'); ?>"
                            data-fs-user-address="<?php esc_html_e($user_address, 'form_submission_data'); ?>"
                            data-fs-user-date="<?php esc_html_e($user_date, 'form_submission_data'); ?>" onclick="clickk(event)"
                            id="<?php esc_html_e($id, 'form_submission_data'); ?>">
                            <span class="dashicons dashicons-edit"></span>
                        </button>

                        <button class="admin-btns" onclick="deleteRow(event)"
                            data-id="<?php esc_html_e($id, 'form_submission_data'); ?>" >
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </td>
                </tr>

                <?php

            } ?>
        </table>
    </div>

    <!-- call datatables -->
    <script>
        new DataTable('#form_data', {
            responsive: true
        });    
    </script>

    <?php

    wp_enqueue_style('form-style', plugin_dir_url(__FILE__) . 'form_submission.css', array(), '1.0');

}

// Handle form data submission
add_action('wp_ajax_submit_form_data', 'submit_form_data');
add_action('wp_ajax_nopriv_submit_form_data', 'submit_form_data');
function submit_form_data()
{
    global $wpdb;
    // Retrieve form data
    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $father_name = isset($_POST['father_name']) ? sanitize_text_field($_POST['father_name']) : '';

    $user_email = isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '';
    $user_contact = isset($_POST['user_contact']) ? sanitize_text_field($_POST['user_contact']) : '';
    $user_amount = isset($_POST['user_amount']) ? sanitize_text_field($_POST['user_amount']) : '';
    $user_private = isset($_POST['user_private']) ? sanitize_text_field($_POST['user_private']) : '';
    $user_address = isset($_POST['user_address']) ? sanitize_text_field($_POST['user_address']) : '';
    $user_date = isset($_POST['user_date']) ? sanitize_text_field($_POST['user_date']) : '';
    $user_hidden = isset($_POST['user_hidden']) ? sanitize_text_field($_POST['user_hidden']) : '';

    $table_name = $wpdb->prefix . 'form_submission_data';

    // Prepare data to be inserted into the table
    $data = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'father_name' => $father_name,
        'user_email' => $user_email,
        'user_contact' => $user_contact,
        'user_amount' => $user_amount,
        'user_private' => $user_private,
        'user_address' => $user_address,
        'user_date' => $user_date

    );

    // Insert data into the table
    if ($user_hidden != "") {
        // Update existing row
        $wpdb->update(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'father_name' => $father_name,
                'user_email' => $user_email,
                'user_contact' => $user_contact,
                'user_amount' => $user_amount,
                'user_private' => $user_private,
                'user_address' => $user_address,
                'user_date' => $user_date
            ),
            array('id' => $user_hidden)
        );
    } else {
        // Insert new row
        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'father_name' => $father_name,
            'user_email' => $user_email,
            'user_contact' => $user_contact,
            'user_amount' => $user_amount,
            'user_private' => $user_private,
            'user_address' => $user_address,
            'user_date' => $user_date
        );
        $wpdb->insert($table_name, $data);
    }

    // Process form data (send email, etc.)

    // Send success response
    echo 'Form data processed successfully!';

    // Flush POST request
    $_POST = array();

    // Exit to avoid extra execution
    wp_die();
}


add_action('wp_ajax_delete_form_data', 'delete_form_data');

function delete_form_data() {
    if (isset($_POST['row_id'])) {
        $row_id = $_POST['row_id'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'form_submission_data';

        // Perform the deletion
        $wpdb->delete($table_name, array('id' => $row_id));

        // Send success response
        echo 'Row deleted successfully!';
    }

    // Flush POST request
    wp_die();
}



// function get_data_by_id()
// {
//     if (isset($_POST['buttonID'])) {
//         global $wpdb;
//         $table_name = $wpdb->prefix . 'form_submission_data';
//         $button_id = $_POST['buttonID'];
//         // Query to fetch all data from the table in alphabetical order by 'full_name'
//         $query = "SELECT * FROM $table_name WHERE id = '$button_id' ";
//         // Get results from the database
//         $results = $wpdb->get_results($query, ARRAY_A);

//         return $results;
//     }

// }

// $result = get_data_by_id();



// function for shortcode
function form_submission_data_shortcode()
{
    // initialize css and js files
    wp_enqueue_style('form-style', plugin_dir_url(__FILE__) . 'form_submission.css', array(), '1.0');

    ?>

    <head>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    </head>

    <!-- Display the list of data in a table -->
    <div class="wrap" style="width:100%; margin: auto; margin-top: 30px;">
        <table id="form_data">
            <thead>
                <th>Full Name</th>
                <th>Father Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Amount</th>
                <th>Address</th>
                <th>Date</th>
            </thead>
            <?php
            // Fetch and display form data in the table
            $data = get_form_submission_data();
            foreach ($data as $key => $value) {
                if ($value['user_private'] == 'Yes') {
                    $user_amount = $value['user_amount'];
                    $full_name = 'anonymity';
                    $father_name = '-';
                    $user_email = '-';
                    $user_contact = '-';
                    $user_address = '-';
                    $user_date = $value['user_date'];

                } else {
                    $user_amount = $value['user_amount'];
                    $full_name = $value['first_name'] . " " . $value['last_name'];
                    $father_name = $value['father_name'];
                    $user_email = $value['user_email'];
                    $user_contact = $value['user_contact'];
                    $user_address = $value['user_address'];
                    $user_date = $value['user_date'];
                }

                ?>
                <tr>

                    <td>
                        <?php esc_html_e($full_name, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($father_name, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_email, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_contact, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_amount, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_address, 'form_submission_data'); ?>
                    </td>
                    <td>
                        <?php esc_html_e($user_date, 'form_submission_data'); ?>
                    </td>
                </tr>
                <?php
            } ?>
        </table>

    </div>

    <script>
        new DataTable('#form_data', {
            responsive: true,
        });
    </script>

    <?php

}
// define shortcode
//shortcode is [display_form_submitted_data]
add_shortcode('display_form_submitted_data', 'form_submission_data_shortcode');

