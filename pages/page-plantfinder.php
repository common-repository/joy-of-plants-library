<?php
/**
 * PlantfinderPage
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
$jop_pages = get_pages();

if ( wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['_nounce'] ) ? $_POST['_nounce'] : '' ) ), 'savejop_pf_nounce' ) ) {
	update_option( 'joyofplants_plantfinder_page', wp_strip_all_tags( wp_unslash( isset( $_POST['pfpage'] ) ? $_POST['pfpage'] : '' ) ) );
}

$pfpage = get_option( 'joyofplants_plantfinder_page' );
?>

<form action="" method="post">
	<h3>Plant Finder Setting</h3>

	<div style="margin-bottom:3px;">Choose the page you want to use for the Plant Finder. The page should contain no
		content, and be added to the menu for navigation.</div>

	<div style="margin-bottom:3px;">Joy of Plants set up the Plant Finder for you when you sign up for this service. For
		maintenance after setup, log into <a target="_blank" href="https://hub.joyofplants.com/">hub.joyofplants.com</a>
		and choose “Kiosk / Website Plant Finder”. </div>

	<select name="pfpage">
		<option value="">---</option>
		<?php
		foreach ( $jop_pages as $jop_page ) {
			echo '<option value="' . esc_attr( $jop_page->ID ) . '" ' . ( strval( $pfpage ) === strval( $jop_page->ID ) ? 'selected' : '' ) . '>' . esc_html( $jop_page->post_title ) . ' (ID:' . esc_html( $jop_page->ID ) . ')</option>';
		}
		?>
	</select>
	<input type="hidden" name="_nounce" value="<?php echo esc_attr( wp_create_nonce( 'savejop_pf_nounce' ) ); ?>">
	<p class="submit"><input type="submit" name="savejopsettings" id="submit" class="button button-primary"
			value="Save Changes"></p>
</form>
