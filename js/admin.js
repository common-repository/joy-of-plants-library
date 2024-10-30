/**
 * Adminjs
 *
 * @category JS
 * @package  JoyOfPlantsLibrary
 * @author   Joy Of Plants <joyofplants@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://joyofplants.com/
 */

jQuery( document ).ready(
	( $ ) =>
	{
		$( 'select.wc-enhanced-select' )
			.filter( ':not(.enhanced)' )
			.each(
				function() {
					var select2_args = {
						minimumResultsForSearch: 10,
						placeholder: $( this ).data( 'placeholder' )
					};

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				}
			);
	}
);
