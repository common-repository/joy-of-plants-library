<?php
/**
 * Api
 * php version 7.2.10
 *
 * @category Api
 * @package  JoyOfPlantsLibrary
 * @author   Joy Of Plants <joyofplants@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://joyofplants.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
The code for the Joy of Plants Library plugin is the copyright of Joy of Plants. It must be used in its entirety and without modification - modification of the code
invalidates the license agreement. Any use, publication or copying in any way is expressly prohibited without consent of Joy of Plants.
*/

require_once plugin_dir_path( __FILE__ ) . 'class-wp-joy-of-plants-api.php';

global $joy_of_plants_api;
$joy_of_plants_api = new Wp_Joy_Of_Plants_Api();
