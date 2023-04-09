<?php
// Kiểm tra xem plugin Local Privacy đã được kích hoạt chưa
if (!defined('ABSPATH')) {
  die;
}

// Hàm tạo trang Settings
function local_privacy_settings_page()
{
  ?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form method="post" action="options.php">
      <?php settings_fields('local-privacy-settings'); ?>
      <?php do_settings_sections('local-privacy-settings'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row"><?php _e('Google Maps API Key', 'local-privacy'); ?></th>
          <td><input type="text" name="local_privacy_gmaps_api_key" value="<?php echo esc_attr(get_option('local_privacy_gmaps_api_key')); ?>" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

// Hàm đăng ký trang Settings vào menu
function local_privacy_register_settings_page()
{
  add_submenu_page(
    'options-general.php',
    __('Local Privacy Settings', 'local-privacy'),
    __('Local Privacy', 'local-privacy'),
    'manage_options',
    'local-privacy-settings',
    'local_privacy_settings_page'
  );
}
add_action('admin_menu', 'local_privacy_register_settings_page');

// Hàm đăng ký các giá trị mặc định cho plugin
function local_privacy_register_settings()
{
  register_setting('local-privacy-settings', 'local_privacy_gmaps_api_key');
}
add_action('admin_init', 'local_privacy_register_settings');
