<?php
/*
Plugin Name: Caregiver Job Application
Description: Plugin for caregiver job application form and search.
Version: 1.0
Author: Your Name
*/

// Activation hook
register_activation_hook(__FILE__, 'caregiver_job_application_install');

function caregiver_job_application_install() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'caregiver_applications';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        full_name varchar(255) NOT NULL,
        date_of_birth date NOT NULL,
        gender varchar(10) NOT NULL,
        phone_number varchar(20) NOT NULL,
        email_address varchar(100) NOT NULL,
        address_street varchar(255) NOT NULL,
        address_city varchar(100) NOT NULL,
        address_state varchar(100) NOT NULL,
        address_zip varchar(20) NOT NULL,
        desired_position varchar(50) NOT NULL,
        start_date date NOT NULL,
        availability varchar(255) NOT NULL,
        caregiving_experience varchar(10) NOT NULL,
        experience_details text,
        certifications_training text,
        reference_1_name varchar(255),
        reference_1_relationship varchar(100),
        reference_1_phone varchar(20),
        reference_1_email varchar(100),
        reference_2_name varchar(255),
        reference_2_relationship varchar(100),
        reference_2_phone varchar(20),
        reference_2_email varchar(100),
        picture_url varchar(255),
        why_work_as_caregiver text,
        additional_information text,
        applicant_declaration tinyint(1) NOT NULL,
        signature varchar(255),
        submission_date datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'caregiver_job_application_uninstall');

function caregiver_job_application_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'caregiver_applications';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Function to output caregiver job application form
function output_caregiver_job_application_form() {
    ob_start();
    ?>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="submit_caregiver_job_application_form">
        <!-- Personal Information -->
        <label>Full Name:</label>
        <input type="text" name="full_name" required>
        <!-- Add other fields as per your form -->
        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean();
}

// Shortcode to display job application form
add_shortcode('caregiver_job_application_form', 'output_caregiver_job_application_form');

// Function to handle form submission
function handle_caregiver_job_application_form_submission() {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "submit_caregiver_job_application_form") {
        global $wpdb;
        $table_name = $wpdb->prefix . 'caregiver_applications';

        // Sanitize and validate form data
        $full_name = sanitize_text_field($_POST['full_name']);
        // Sanitize and validate other fields similarly

        // Insert data into database
        $wpdb->insert(
            $table_name,
            array(
                'full_name' => $full_name,
                // Add other fields here
                'submission_date' => current_time('mysql')
            )
        );

        // Redirect after submission
        wp_redirect(home_url('/thank-you'));
        exit;
    }
}

// Form submission handler
add_action('admin_post_submit_caregiver_job_application_form', 'handle_caregiver_job_application_form_submission');
add_action('admin_post_nopriv_submit_caregiver_job_application_form', 'handle_caregiver_job_application_form_submission');

// Function to display search form
function output_caregiver_search_form() {
    ob_start();
    ?>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="search_caregiver_applications">
        <label>Enter ZIP Code:</label>
        <input type="text" name="zip_code" required>
        <input type="submit" value="Search">
    </form>
    <?php
    return ob_get_clean();
}

// Shortcode to display search form
add_shortcode('caregiver_search_form', 'output_caregiver_search_form');
// Function to handle search
function handle_caregiver_search() {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == "search_caregiver_applications") {
        global $wpdb;
        $table_name = $wpdb->prefix . 'caregiver_applications';

        // Sanitize and validate search input
        $zip_code = sanitize_text_field($_POST['zip_code']);

        // Perform search query
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE address_zip = %s", $zip_code));

        // Display search results
        if ($results) {
            echo "<h2>Search Results for ZIP Code: $zip_code</h2>";
            echo "<table>";
            echo "<tr><th>Name</th><th>Date of Birth</th><th>Gender</th><th>Phone Number</th><th>Email Address</th><th>Desired Position</th><th>Start Date</th><th>Availability</th><th>Experience</th><th>Reference #1 Name</th><th>Reference #2 Name</th><th>Additional Information</th><th>Submission Date</th></tr>";
            foreach ($results as $result) {
                echo "<tr>";
                echo "<td>{$result->full_name}</td>";
                echo "<td>{$result->date_of_birth}</td>";
                echo "<td>{$result->gender}</td>";
                echo "<td>{$result->phone_number}</td>";
                echo "<td>{$result->email_address}</td>";
                echo "<td>{$result->desired_position}</td>";
                echo "<td>{$result->start_date}</td>";
                echo "<td>{$result->availability}</td>";
                echo "<td>{$result->caregiving_experience}</td>";
                echo "<td>{$result->reference_1_name}</td>";
                echo "<td>{$result->reference_2_name}</td>";
                echo "<td>{$result->additional_information}</td>";
                echo "<td>{$result->submission_date}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No results found.";
        }
    }
}
// Search handler
add_action('admin_post_search_caregiver_applications', 'handle_caregiver_search');
add_action('admin_post_nopriv_search_caregiver_applications', 'handle_caregiver_search');