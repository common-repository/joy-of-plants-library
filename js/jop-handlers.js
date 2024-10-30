/**
 * Jop handler
 *
 * @category JS
 * @package  JoyOfPlantsLibrary
 * @author   Joy Of Plants <joyofplants@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://joyofplants.com/
 */

if ( ! jopMessageHandler ) {
	function jopMessageHandler( event ) {
		if ( event.data.plantid ) {
			iframe = document.getElementById( 'plantText-' + event.data.plantid );
			if ( iframe ) {
				iframe.height = event.data.iframeheight;
			}
		}
	}
	window.addEventListener( "message", jopMessageHandler )
}
