<?php
/**
 * SettingsPage
 * php version 7.2.10
 *
 * @category Page
 * @package  JoyOfPlantsLibrary
 * @author   Joy Of Plants <joyofplants@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://joyofplants.com/
 */

?>
<!-- The code for the Joy of Plants Library plugin is the copyright of Joy of Plants.
It must be used in its entirety and without modification - modification of the code
invalidates the license agreement. Any use, publication or copying in any way is 
expressly prohibited without consent of Joy of Plants. -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $joy_of_plants_api;

$form_fields = array(
	'joyofplants_api_username'     => 'Username',
	'joyofplants_api_password'     => 'Password',
	'joyofplants_api_clientid'     => 'Client Id',
	'joyofplants_api_clientsecret' => 'Client Secret',
);

if ( wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['_nounce'] ) ? $_POST['_nounce'] : '' ) ), 'savejop_settings_nounce' ) ) {
	foreach ( $form_fields as $key => $name ) {
		update_option( $key, sanitize_text_field( wp_unslash( isset( $_POST[ $key ] ) ? $_POST[ $key ] : '' ) ) );
	}
	$joy_of_plants_api->get_jop_options();
	$api_test = $joy_of_plants_api->get_access_token();
}
$api_test = array();
$api_test = $joy_of_plants_api->get_access_token();



?>
<h1 class="jop-title" style="order: 1">Joy of Plants Plugin API Settings</h1>
<div class="jop-content" style="order: 2">
	<p>You'll be given the settings to add to this page by Joy of Plants when
		your account is created.</p>
	<p>You should only need to set them up once. Don't change or delete them, or
		the Joy of Plants Images & texts will no longer be shown in your
		webshop pages.</p>
	<p>The box below will say "Connection is Successful" if the settings are
		correct and your webshop is connected to the Joy of Plants Library.</p>
	<p>If instead it says "Connection not made", check your credentials below
		and contact Joy of Plants if you need help correcting them.</p>
</div>
<div class="wrap">
	<div style="margin-bottom: 0; margin-top: 20px"
		class="notice notice-<?php echo isset( $api_test['error'] ) ? 'error' : 'success'; ?> settings-error">
		<p>
			<?php
			if ( ! isset( $api_test['error'] ) ) {
				echo 'Connection is Successful';
			}
			?>

			<?php
			if ( isset( $api_test['error'] ) ) {
				$api_error = false;

				echo '<div><b>Connection to library not made:</b></div>';

				foreach ( $form_fields as $key => $name ) {
					$val = get_option( $key );
					if ( ! $val ) {
						echo '<div> - "' . esc_html( $name ) . '" API setting is missing </div>';
						$api_error = true;
					}
				}

				if ( ! $api_error && isset( $api_test['error_description'] ) ) {
					$api_error = true;
					echo '<div>- ' . esc_html( $api_test['error_description'] ) . '</div>';
				}

				if ( ! $api_error ) {
					echo '<div>- ' . ( 'invalid_client' === $api_test['error'] ) ?
						'Connection not made' : esc_html( $api_test['error'] ) . '</div>';
				}
			}
			?>
		</p>
	</div>
	<form method="post" action="" style="order: 4">
		<?php
		settings_fields( 'joyofplants-settings' );
		?>
		<table class="form-table">
			<?php
			foreach ( $form_fields as $key => $name ) {
				echo '<tr valign="top">';
				echo '<th scope="row"><label for="jop_api">' . esc_attr( $name ) . '</label></th>';
				echo '<td><input type="text" class="regular-text" id="' .
					esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( get_option( $key ) ) . '" /></td>';
				echo '</tr>';
			}
			?>
		</table>
		<p class="submit">
			<input type="hidden" name="_nounce" value="<?php echo esc_attr( wp_create_nonce( 'savejop_settings_nounce' ) ); ?>">
			<input type="submit" name="savejopsettings" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>
