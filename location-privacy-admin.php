<?php
/**
 * Add a menu item for the plugin.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add menu item to the admin dashboard
add_action( 'admin_menu', 'location_privacy_menu' );

function location_privacy_menu() {
    add_options_page(
        'Location Privacy Settings',
        'Location Privacy',
        'manage_options',
        'location-privacy',
        'location_privacy_options'
    );
}

// Display the plugin settings page
function location_privacy_options() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Save settings if form is submitted
    if ( isset( $_POST['submit'] ) ) {
        // TODO: Save settings
    }

    // Display the settings page
    include plugin_dir_path( __FILE__ ) . 'templates/settings-page.php';
}
