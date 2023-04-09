<?php
/*
Plugin Name: Local Privacy
Plugin URI: https://example.com/
Description: A plugin to log user location data locally
Version: 1.0
Author: John Doe
Author URI: https://example.com/
*/

function lp_create_log_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . "lp_user_location_log";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip_address varchar(45) DEFAULT '' NOT NULL,
        user_agent varchar(255) DEFAULT '' NOT NULL,
        latitude varchar(255) DEFAULT '' NOT NULL,
        longitude varchar(255) DEFAULT '' NOT NULL,
        accuracy varchar(255) DEFAULT '' NOT NULL,
        timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook( __FILE__, 'lp_create_log_table' );

function lp_log_user_location() {
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        global $wpdb;

        $table_name = $wpdb->prefix . "lp_user_location_log";

        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $latitude = sanitize_text_field($_POST['latitude']);
        $longitude = sanitize_text_field($_POST['longitude']);
        $accuracy = sanitize_text_field($_POST['accuracy']);
        $timestamp = current_time('mysql');

        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accuracy' => $accuracy,
                'timestamp' => $timestamp
            )
        );
    }
}

add_action( 'wp_ajax_lp_log_user_location', 'lp_log_user_location' );
add_action( 'wp_ajax_nopriv_lp_log_user_location', 'lp_log_user_location' );

function lp_settings_page_content() {
    global $wpdb;
    $table_name = $wpdb->prefix . "lp_user_location_log";

    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");

    ?>

    <div class="wrap">
        <h1>Local Privacy Settings</h1>

        <h2>Location Log</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Accuracy</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) { ?>
                    <tr>
                        <td><?php echo $row->id; ?></td>
                        <td><?php echo $row->ip_address; ?></td>
                        <td><?php echo $row->user_agent; ?></td>
                        <td><?php echo $row->latitude; ?></td>
                        <td><?php echo $row->longitude; ?></td>
                        <td><?php echo $row->accuracy; ?></td>
                        <td><?php echo $row->timestamp; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if (count($results) == 0) { ?>
            <p><?php _e('No location data found.', 'local-privacy'); ?></p>
        <?php } ?>
    </div>
<?php
}
add_action('admin_menu', 'lp_add_admin_menu');
add_action('admin_init', 'lp_settings_init');

function lp_add_admin_menu()
{
    add_options_page('Local Privacy Settings', 'Local Privacy', 'manage_options', 'local-privacy-settings', 'lp_settings_page_content');
}

function lp_settings_init()
{
    register_setting('lp_settings', 'lp_settings');
}
function get_location_info() {
    $location_info = array();

    // Get user's IP address
    $ip = $_SERVER['REMOTE_ADDR'];
    $location_info['ip'] = $ip;

    // Use an external service to get user's location information
    $url = 'http://ip-api.com/json/' . $ip;
    $response = wp_remote_get( $url );
    if ( !is_wp_error( $response ) && $response['response']['code'] == 200 ) {
        $location_data = json_decode( $response['body'], true );
        if ( $location_data['status'] == 'success' ) {
            $location_info['city'] = $location_data['city'];
            $location_info['region'] = $location_data['regionName'];
            $location_info['country'] = $location_data['country'];
            $location_info['lat'] = $location_data['lat'];
            $location_info['lon'] = $location_data['lon'];
        }
    }

    return $location_info;
}
$log_file = 'logs/log.txt';
if (!file_exists($log_file)) {
    $file = fopen($log_file, 'w');
    fclose($file);
}
