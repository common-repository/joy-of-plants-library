<?php
/**
 * Uninstall
 * php version 7.2.10
 *
 * @category Settings
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

global $wpdb;

$results = $wpdb->get_results( 'SELECT id, post_content FROM ' . $wpdb->prefix . "posts WHERE post_content LIKE '%[JoyOfPlantsText]%'" );

foreach ( $results as $res_post ) {
	$desc      = str_ireplace( '[JoyOfPlantsText]', '', $res_post->post_content );
	$post_data = array(
		'ID'           => $res_post->id,
		'post_content' => $desc,
	);
	$wpdb->update( $wpdb->posts, array( 'post_content' => $desc ), array( 'ID' => $post->id ) );
}
$dimg = get_option( 'joyofplants_dummy_image' );
if ( $dimg ) {
	wp_delete_attachment( $dimg, true );
}

$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "postmeta WHERE meta_key IN ('jop_product_pid', 'jop_product_image_l', 'jop_product_image_m', 'jop_product_image_s','jop_product_image_expire','jop_product_image_error','jop_product_display_text','jop_product_display_image')" );


// delete multiple options.
$options = array(
	'joyofplants_api_username',
	'joyofplants_api_password',
	'joyofplants_api_clientid',
	'joyofplants_api_clientsecret',
	'joyofplants_api_accesstoken',
	'joyofplants_api_accesstoken_expire',
	'joyofplants_export_index',
	'joyofplants_dummy_image',
	'joyofplants_plantfinder_page',
	'joyofplants_pflink_style',
);
foreach ( $options as $option ) {
	if ( get_option( $option ) ) {
		delete_option( $option );
	}
}
