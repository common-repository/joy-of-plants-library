<?php
/**
 * Index
 * php version 7.2.10
 *
 * @category Index
 * @package  JoyOfPlantsLibrary
 * @author   Joy Of Plants <joyofplants@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://joyofplants.com/
 */

/*
Plugin Name: Joy of Plants Library
Plugin URI:  https://wordpress.org/plugins/joy-of-plants-library/
Description: Adds images & text descriptions from the Joy of Plants Library to your plant products. A picture sells a thousand plants!
Version:     1.0.24
Author:      Joy of Plants Ltd.
Author URI:  https://joyofplants.com
Requires Plugins: woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
The code for the Joy of Plants Library plugin is the copyright of Joy of Plants. It must be used in its entirety and without modification - modification of the code
invalidates the license agreement. Any use, publication or copying in any way is expressly prohibited without consent of Joy of Plants.
*/

/**
 * Joyofplants_settings_link
 *
 * @param  mixed $links links.
 * @return mixed
 */
function joyofplants_settings_link( $links ) {
	$overview_link = '<a href="' . admin_url( 'admin.php?page=joyofplants' ) . '">Overview</a>';
	array_unshift( $links, $overview_link );
	return $links;
}
$plugin_name = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin_name", 'joyofplants_settings_link' );

/**
 * Joyofplants_plugin_set_options
 *
 * @return void
 */
function joyofplants_plugin_set_options() {
	global $wpdb;
}

/**
 * Joyofplants_plugin_unset_options
 *
 * @return void
 */
function joyofplants_plugin_unset_options() {
	global $wpdb;
}

/**
 * Joyofplants_plugin_admin_menu
 *
 * @return void
 */
function joyofplants_plugin_admin_menu() {
	add_menu_page( 'Joy of Plants', 'Joy of Plants', 'manage_options', 'joyofplants', 'joyofplants_plugin_option_menu' );
	add_submenu_page( 'joyofplants', 'Overview', 'Overview', 'manage_options', 'joyofplants', 'joyofplants_plugin_option_menu' );
	add_submenu_page( 'joyofplants', 'Plant Finder', 'Plant Finder', 'manage_options', 'joyofplants_plantfinder', 'joyofplants_plugin_option_menu' );
	add_submenu_page( 'joyofplants', 'Export', 'Export', 'manage_options', 'joyofplants_export', 'joyofplants_plugin_option_menu' );
	add_submenu_page( 'joyofplants', 'Import', 'Import', 'manage_options', 'joyofplants_import', 'joyofplants_plugin_option_menu' );
	add_submenu_page( 'joyofplants', 'API Settings', 'API Settings', 'manage_options', 'joyofplants_settings', 'joyofplants_plugin_option_menu' );
}

/**
 * Joyofplants_plugin_option_menu
 *
 * @return void
 */
function joyofplants_plugin_option_menu() {
	wp_enqueue_script( 'jopadmin_select2', plugins_url( '/js/select2.min.js', __FILE__ ), array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'jopadmin_selectwoo', plugins_url( '/js/selectWoo.min.js', __FILE__ ), array( 'jquery' ), '1.0', false );
	wp_enqueue_script( 'jopadmin', plugins_url( '/js/admin.js', __FILE__ ), array( 'jquery', 'jopadmin_select2', 'jopadmin_selectwoo' ), '1.0', false );
	wp_enqueue_style( 'jopadmin', plugins_url( '/css/admin.css', __FILE__ ), '', '1.0', false );

	if ( isset( $_GET['page'] ) ) {
		if ( 'joyofplants' === $_GET['page'] ) {
			if ( is_admin() ) {
				$plugin_data    = get_plugin_data( __FILE__ );
				$plugin_version = $plugin_data['Version'];
			}
			define( 'JOP_LIBRARY_PLUGIN_VERSION', $plugin_version );
			include_once 'pages/page-home.php';
		} elseif ( 'joyofplants_settings' === $_GET['page'] ) {
			include_once 'pages/page-settings.php';
		} elseif ( 'joyofplants_export' === $_GET['page'] ) {
			include_once 'pages/page-export-v2.php';
		} elseif ( 'joyofplants_import' === $_GET['page'] ) {
			include_once 'pages/page-import-v2.php';
		} elseif ( 'joyofplants_plantfinder' === $_GET['page'] ) {
			include_once 'pages/page-plantfinder.php';
		}
	}
}



register_activation_hook( __FILE__, 'joyofplants_plugin_set_options' );
register_deactivation_hook( __FILE__, 'joyofplants_plugin_unset_options' );
add_action( 'admin_menu', 'joyofplants_plugin_admin_menu' );

global $joyofplants_dummy_image_global;
$joyofplants_dummy_image_global = get_option( 'joyofplants_dummy_image' );
/**
 * Jop_attachment_to_product
 *
 * @param  mixed $post_id post id.
 * @return void
 */
function jop_attachment_to_product( $post_id ) {
	global $joyofplants_dummy_image_global;
	if ( ! $post_id ) {
		return;
	}

	$gallery                       = get_post_meta( $post_id, '_product_image_gallery', true );
	$attachments                   = get_post_thumbnail_id( $post_id );
	$woocommerce_placeholder_image = get_option( 'woocommerce_placeholder_image' );
	$plant_id                      = get_post_meta( $post_id, 'jop_product_pid', true );
	$display_image                 = get_post_meta( $post_id, 'jop_product_display_image', true );
	if ( 0 === intval( $attachments ) || $attachments === $woocommerce_placeholder_image || ! $plant_id || ! $display_image ) {
		$split = explode( ',', $gallery );
		if ( strval( $split[0] ) === '' ) {
			$split = array();
		}
		$index = array_search( $joyofplants_dummy_image_global, $split, true );
		if ( false !== $index ) {
			array_splice( $split, $index, 1 );
			$join = implode( ',', $split );
			update_post_meta( $post_id, '_product_image_gallery', $join );
		}
	} else {
		$split = explode( ',', $gallery );
		if ( strval( $split[0] ) === '' ) {
			$split = array();
		}
		$index = array_search( $joyofplants_dummy_image_global, $split, true );
		if ( false === $index ) {
			$split[] = $joyofplants_dummy_image_global;
			$join    = implode( ',', $split );
			update_post_meta( $post_id, '_product_image_gallery', $join );
		}
	}
}


require_once 'settings.php';
require_once 'api.php';
