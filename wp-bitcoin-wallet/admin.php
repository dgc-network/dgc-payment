<?php
/**
 * The field on the editing screens.
 *
 * @param $user WP_User user object
 */
function wporg_usermeta_form_field_dgc_addresses( $user )
{
    ?>
    <h3>Digitalcoin Addresses</h3>
    <table class="form-table">
		<tr>
            <th>
                <label for="receive_address">Receiving address</label>
            </th>
            <td>
                <input type="text"
                       class="regular-text ltr"
                       id="receive_address"
                       name="receive_address"
                       value="<?= esc_attr( get_user_meta( $user->ID, 'receive_address', true ) ) ?>"
                       disabled>
            </td>
        </tr>
        <tr>
            <th>
                <label for="change_address">Changing address</label>
            </th>
            <td>
                <input type="text"
                       class="regular-text ltr"
                       id="change_address"
                       name="change_address"
                       value="<?= esc_attr( get_user_meta( $user->ID, 'change_address', true ) ) ?>"
                       disabled>
            </td>
        </tr>
    </table>
    <?php
}
  
/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function wporg_usermeta_form_field_update( $user_id )
{
    // check that the current user have the capability to edit the $user_id
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
  
    // create/update user meta for the $user_id
    return update_user_meta(
        $user_id,
        'receive_address',
        $_POST['receive_address']
    );
}
  
// Add the field to user's own profile editing screen.
add_action(
    'show_user_profile',
    'wporg_usermeta_form_field_dgc_addresses'
);
  
// Add the field to user profile editing screen.
add_action(
    'edit_user_profile',
    'wporg_usermeta_form_field_dgc_addresses'
);
  
// Add the save action to user's own profile editing screen update.
add_action(
    'personal_options_update',
    'wporg_usermeta_form_field_update'
);
  
// Add the save action to user profile editing screen update.
add_action(
    'edit_user_profile_update',
    'wporg_usermeta_form_field_update'
);

/**
 * 
 */
add_action('admin_menu', 'wpbw_create_config_page', 60);

function wpbw_create_config_page() {
	//add_options_page('Bitcoin Wallet Options', 'Bitcoin Wallet', 'manage_options', 'wpbw-config-menu', 'wpbw_config_page');
	add_action('admin_init', 'wpbw_register_settings');
	//add_submenu_page( 'dgc-wallet', __( 'Digitalcoin', 'text-domain' ), __( 'Digitalcoin', 'text-domain' ), 'manage_woocommerce', 'wpbw-config-menu', 'wpbw_config_page' );
}

function wpbw_config_page() {
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}
	?>
	<div class="wrap">
	<h2>Wallet Options</h2>
	<form action="options.php" method="post">
	<?php settings_fields('wpbw_plugin_options'); ?>
	<?php $options = get_option('wpbw_plugin_options'); ?>
	<label>RPC Host:</label>
	<input id="bitcoind_rpc_host" name="wpbw_plugin_options[bitcoind_rpc_host]" size="40" type="text" value="<?php echo $options['bitcoind_rpc_host'] ?>" />
	<br />
	<label>RPC Port:</label>
	<input id="bitcoind_rpc_port" name="wpbw_plugin_options[bitcoind_rpc_port]" size="40" type="text" value="<?php echo $options['bitcoind_rpc_port'] ?>" />
	<br />
	<label>RPC Username:</label>
	<input id="bitcoind_rpc_username" name="wpbw_plugin_options[bitcoind_rpc_username]" size="40" type="text" value="<?php echo $options['bitcoind_rpc_username'] ?>" />
	<br />
	<label>RPC Password:</label>
	<input id="bitcoind_rpc_password" name="wpbw_plugin_options[bitcoind_rpc_password]" size="40" type="text" value="<?php echo $options['bitcoind_rpc_password'] ?>" />
	<br />
	<label>account prefix:</label>
	<input id="bitcoind_account_prefix" name="wpbw_plugin_options[bitcoind_account_prefix]" size="40" type="text" value="<?php echo $options['bitcoind_account_prefix'] ?>" />
	<br />
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
	</div>
	<?php
}

function wpbw_register_settings() {
	register_setting('wpbw_plugin_options', 'wpbw_plugin_options', 'wpbw_plugin_options_validate');
}

function wpbw_plugin_options_validate($input) {
	$newinput['bitcoind_rpc_host'] = trim($input['bitcoind_rpc_host']);
	$newinput['bitcoind_rpc_port'] = trim($input['bitcoind_rpc_port']);
	$newinput['bitcoind_rpc_username'] = trim($input['bitcoind_rpc_username']);
	$newinput['bitcoind_rpc_password'] = trim($input['bitcoind_rpc_password']);
	$newinput['bitcoind_account_prefix'] = trim($input['bitcoind_account_prefix']);

	//TODO: Actually validate the input.

	return $newinput;
}

?>
