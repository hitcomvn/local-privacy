<?php
/**
 * Add a submenu page under the Settings menu
 */
function my_plugin_settings_page() {
    add_submenu_page(
        'options-general.php',
        'My Plugin Settings',
        'My Plugin',
        'manage_options',
        'my-plugin',
        'my_plugin_settings_page_html'
    );
}

add_action( 'admin_menu', 'my_plugin_settings_page' );

/**
 * Register settings and add fields to the settings page
 */
function my_plugin_register_settings() {
    add_option( 'my_plugin_api_key', '' );
    register_setting( 'my_plugin_settings', 'my_plugin_api_key' );
}

add_action( 'admin_init', 'my_plugin_register_settings' );

/**
 * Render the HTML for the settings page
 */
function my_plugin_settings_page_html() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Save settings if form is submitted
    if ( isset( $_POST['my_plugin_api_key'] ) ) {
        update_option( 'my_plugin_api_key', sanitize_text_field( $_POST['my_plugin_api_key'] ) );
    }

    // Show error/success messages
    settings_errors( 'my_plugin_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'my_plugin_settings' );
            do_settings_sections( 'my_plugin_settings' );
            ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="my_plugin_api_key"><?php esc_attr_e( 'API Key', 'my-plugin' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="my_plugin_api_key" name="my_plugin_api_key" value="<?php echo esc_attr( get_option( 'my_plugin_api_key' ) ); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button( __( 'Save Settings', 'my-plugin' ) ); ?>
        </form>
    </div>
    <?php
}
