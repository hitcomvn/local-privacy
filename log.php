<?php
/**
 * Plugin Name: Local Privacy
 * Description: Protects user privacy by logging only city-level location data.
 * Version: 1.0
 * Author: ChatGPT
 * License: GPL2
 */

// create table if not exists
function local_privacy_create_table() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'local_privacy_log';

  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            ip_address VARCHAR(50) NOT NULL,
            city VARCHAR(255),
            country_code VARCHAR(10),
            PRIMARY KEY  (id)
          );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

register_activation_hook(__FILE__, 'local_privacy_create_table');

// display log page
function local_privacy_log_page() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'local_privacy_log';

  $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");

  echo '<div class="wrap">';
  echo '<h2>Local Privacy Log</h2>';

  if (count($results) > 0) {
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>ID</th><th>IP Address</th><th>City</th><th>Country Code</th><th>Date/Time</th></tr></thead>';
    echo '<tbody>';

    foreach ($results as $result) {
      echo '<tr>';
      echo '<td>' . $result->id . '</td>';
      echo '<td>' . $result->ip_address . '</td>';
      echo '<td>' . $result->city . '</td>';
      echo '<td>' . $result->country_code . '</td>';
      echo '<td>' . $result->date_created . '</td>';
      echo '</tr>';
    }

    echo '</tbody></table>';
  } else {
    echo '<p>No log entries found.</p>';
  }

  echo '</div>';
}

// add menu item for log page
function local_privacy_add_menu_item() {
  add_submenu_page(
    'options-general.php',
    'Local Privacy Log',
    'Local Privacy Log',
    'manage_options',
    'local-privacy-log',
    'local_privacy_log_page'
  );
}

add_action('admin_menu', 'local_privacy_add_menu_item');
