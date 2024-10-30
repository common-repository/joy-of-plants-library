<?php
/**
 * Settings
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

add_action( 'admin_init', 'joyofplants_admin_init' );
/**
 * Joyofplants Admin Init
 */
function joyofplants_admin_init() {

	$form_fields = array(
		'joyofplants_api_username'           => 'Username',
		'joyofplants_api_password'           => 'Password',
		'joyofplants_api_clientid'           => 'Client Id',
		'joyofplants_api_clientsecret'       => 'Client Secret',
		'joyofplants_api_accesstoken'        => 'Access Token',
		'joyofplants_api_accesstoken_expire' => 'Access Token Expire',
		'joyofplants_export_index'           => 'Export Index',
		'joyofplants_export_date'            => 'Export Index Date',
		'joyofplants_dummy_image'            => 'Dummy Image',
		'joyofplants_plantfinder_page'       => 'Plant Finder Page',
		'joyofplants_pflink_style'           => 'Plant Finder Link Style',
	);

	foreach ( $form_fields as $key => $name ) {
		register_setting( 'joyofplants-settings', $key );
	}
	attach_jop_placeholder_image();
}

add_filter( 'woocommerce_product_data_tabs', 'joyofplants_product_data_tab' );
/**
 * Joyofplants product data tab
 *
 * @param array $product_data_tabs product data tabs.
 */
function joyofplants_product_data_tab( $product_data_tabs ) {
	$product_data_tabs['joyofplants-tab'] = array(
		'label'    => __( 'Joy Of Plants', 'joyofplants' ),
		'target'   => 'joyofplants_product_data',
		'priority' => 11,
	);
	return $product_data_tabs;
}

add_action( 'woocommerce_product_data_panels', 'joyofplants_product_data_fields' );
/**
 * Joyofplants product data fields
 */
function joyofplants_product_data_fields() {
	global $woocommerce, $post;
	?>
	<div id="joyofplants_product_data" class="panel woocommerce_options_panel">
		<?php
		woocommerce_wp_text_input(
			array(
				'id'          => 'jop_product_pid',
				'label'       => __( 'Plant ID', 'joyofplants' ),
				'description' => __( 'Using for get images and description from Joy Of Plants API', 'joyofplants' ),
				'default'     => '0',
				'type'        => 'number',
				'desc_tip'    => true,
			)
		);
		woocommerce_wp_checkbox(
			array(
				'id'          => 'jop_product_display_image',
				'label'       => __( 'Display Image', 'joyofplants' ),
				'description' => __( 'Turn off if you want to hide JoyOfPlants image', 'joyofplants' ),
				'value'       => get_post_meta( get_the_ID(), 'jop_product_display_image', true ),
				'default'     => false,
				'type'        => 'boolean',
				'desc_tip'    => true,
			)
		);
		woocommerce_wp_checkbox(
			array(
				'id'          => 'jop_product_display_text',
				'label'       => __( 'Display Text', 'joyofplants' ),
				'description' => __( 'Turn off if you want to hide JoyOfPlants iframe text', 'joyofplants' ),
				'value'       => get_post_meta( get_the_ID(), 'jop_product_display_text', true ),
				'default'     => false,
				'type'        => 'number',
				'desc_tip'    => true,
			)
		);
		?>
	</div>
	<?php
}

add_action( 'woocommerce_process_product_meta', 'joyofplants_process_product_meta_fields_save' );
/**
 * Joyofplants process product meta fields save
 *
 * @param int $post_id post id.
 */
function joyofplants_process_product_meta_fields_save( $post_id ) {
	if ( ! ( isset( $_POST['woocommerce_meta_nonce'] ) || wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) ) {
		return;
	}
	update_post_meta( $post_id, 'jop_product_pid', sanitize_text_field( wp_unslash( isset( $_POST['jop_product_pid'] ) ? $_POST['jop_product_pid'] : '' ) ) );
	update_post_meta( $post_id, 'jop_product_image_expire', 0 );
	update_post_meta( $post_id, 'jop_product_display_text', isset( $_POST['jop_product_display_text'] ) ? 'yes' : '' );
	update_post_meta( $post_id, 'jop_product_display_image', isset( $_POST['jop_product_display_image'] ) ? 'yes' : '' );
}

add_action( 'wp_enqueue_scripts', 'joyofplants_script_handlers' );
/**
 * Joyofplants script handlers
 */
function joyofplants_script_handlers() {
	wp_enqueue_script( 'joyofplants_text_handler', plugins_url( '/js/jop-handlers.js', __FILE__ ), array(), '1.0', false );
}

add_filter( 'the_content', 'joyofplants_content_filter' );
/**
 * Joyofplants content filter
 *
 * @param int $content content.
 */
function joyofplants_content_filter( $content ) {
	global $post, $joy_of_plants_api;
	$pfpage = get_option( 'joyofplants_plantfinder_page' );

	if ( isset( $post ) ) {
		jop_attachment_to_product( $post->ID );
	}
	if ( isset( $post ) && strval( $pfpage ) === strval( $post->ID ) ) {
		wp_enqueue_script( 'joyofplants_plantfinder', 'https://joyofplants.com/plantfinder2/jopplantfinder.js', array(), '1.0', true );
		return $content . '<!--
                The code for the Plant Finder is the copyright of Joy of Plants. It must be used in its entirety and without
                modification - modification of the code invalidates the license agreement. Any use, publication or
                copying in any way is expressly prohibited without consent of Joy of Plants.
                -->
								<div id="joyofplants-plantfinder" src="https://joyofplants.com/plantfinder2/jopplantfinder.js"></div>
								';
	}

	return $content;
}

add_action( 'add_meta_boxes', 'joyofplants_image_sidebar', 30 );
/**
 * Joyofplants image sidebar
 */
function joyofplants_image_sidebar() {
	global $post, $joy_of_plants_api;
	$pid = $joy_of_plants_api->get_plant_id( $post->ID );
	if ( $pid ) {
		add_meta_box( 'woocommerce-joyofplants-image', __( 'Joy Of Plants Image', 'woocommerce' ), 'joyofplants_image_sidebar_message', 'product', 'side', 'low' );
	}

}
/**
 * Joyofplants image sidebar message
 */
function joyofplants_image_sidebar_message() {
	global $post, $joy_of_plants_api;
	$jop_image = $joy_of_plants_api->get_image( $post->ID, 'm' );
	echo '<div class="joyofplants_image_sidebar_message">';
	if ( $jop_image ) {
		echo wp_kses(
			$jop_image,
			array(
				'img' => array(
					'src'     => true,
					'width'   => true,
					'loading' => true,
				),
			)
		);
	}
	echo '<style>.joyofplants_image_sidebar_message img{width:100%}</style>';
	$error = get_post_meta( $post->ID, 'jop_product_image_error', true );
	if ( $error ) {
		echo '<div style="font-weight: bold;color: red;font-size: 16px;">' . esc_html( $error ) . '</div>';
	}
	echo '</div>';
}

add_action( 'admin_footer', 'joyofplants_admin_footer_function' );
/**
 * Joyofplants admin footer function
 */
function joyofplants_admin_footer_function() {
	global $post;
	$pfpage = get_option( 'joyofplants_plantfinder_page' );

	if ( $post && strval( $pfpage ) === strval( $post->ID ) ) {
		echo '<script>
        placeholderForPF();
        function placeholderForPF(){
            content = document.querySelector(".interface-interface-skeleton__content");
            console.log("placeholderForPF",content);
            if(content){
                content.className += " plantfinder-placeholder";
            }else{
                setTimeout(placeholderForPF,1000);
            }
        }
        </script>
        <style>
            .plantfinder-placeholder{
                position: relative;
            }
            .plantfinder-placeholder:before{
                content: "Plant Finder Page";
                color: #fff;
                font-size: 40px;
                font-weight: bold;
                top: 0;
                left: 0;
                background: rgba(0,0,0,.72);
                width: 100%;
                min-height: 70px;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        </style>';
	}
}

add_filter( 'woocommerce_placeholder_img_src', 'joyofplants_placeholder_img_src' );
/**
 * Joyofplants placeholder img src
 *
 * @param string $src src.
 */
function joyofplants_placeholder_img_src( $src ) {
	global $post, $joy_of_plants_api;
	$url = $joy_of_plants_api->get_image_url( 'large' );
	if ( $url ) {
		return $url;
	}

	return $src;
}
add_filter( 'wp_get_attachment_image_src', 'joyofplants_get_attachment_image_src', 10, 4 );
/**
 * Joyofplants get attachment image src
 *
 * @param string $image image.
 * @param string $attachment_id attachment_id.
 * @param string $size size.
 * @param string $icon icon.
 */
function joyofplants_get_attachment_image_src( $image, $attachment_id, $size, $icon ) {
	global $post, $joy_of_plants_api;
	$dummy_image = get_option( 'joyofplants_dummy_image' );

	if ( strval( $attachment_id ) === strval( $dummy_image ) ) {
		$url = $joy_of_plants_api->get_image_url( $size );
		if ( $url ) {
			$image[0] = $url;
		}
	}
	return $image;
}

add_filter( 'woocommerce_product_get_image', 'joyofplants_product_get_image', 10, 6 );
/**
 * Joyofplants product get image
 *
 * @param string $image image.
 * @param string $that that.
 * @param string $size size.
 * @param string $attr attr.
 * @param string $placeholder placeholder.
 * @param string $image2 image2.
 */
function joyofplants_product_get_image( $image, $that, $size, $attr, $placeholder, $image2 ) {
	global $post, $joy_of_plants_api;

	$attachments   = get_post_thumbnail_id( $that->get_id() );
	$display_image = get_post_meta( $that->get_id(), 'jop_product_display_image', true );
	jop_attachment_to_product( $that->get_id() );
	$woocommerce_placeholder_image = get_option( 'woocommerce_placeholder_image' );
	if ( ( 0 === $attachments || $attachments === $woocommerce_placeholder_image ) && $display_image ) {
		$jop_image = $joy_of_plants_api->get_image( $that->get_id(), $size );
		if ( $jop_image ) {
			return $jop_image;
		} else {
			$joyofplants_dummy_image = get_option( 'joyofplants_dummy_image' );
			$image_attributes        = wp_get_attachment_image_src( $joyofplants_dummy_image, $size );
			$image                   = "<img src='$image_attributes[0]' width='$image_attributes[1]' loading='lazy'/>";
			return $image;
		}
	}

	return $image;
}

add_shortcode( 'JoyOfPlantsText', 'joyofplants_content_shortcode' );
/**
 * Joyofplants content shortcode
 *
 * @param array  $codes codes.
 * @param string $content content.
 */
function joyofplants_content_shortcode( $codes = array(), $content = null ) {
	global $post;

	$start_pos = strpos( $content, '(' ) + 1;
	$end_pos   = strpos( $content, ')' );

	$plantid = get_post_meta( $post->ID, 'jop_product_pid', true );

	if ( ! $plantid ) {
		return 'JoyOfPlants Content - PlantId not exist';
	}
	$display_text = get_post_meta( $post->ID, 'jop_product_display_text', true );

	$new_content = '<!-- JOPstart -->';

	$pfpage = get_option( 'joyofplants_plantfinder_page' );

	if ( $pfpage ) {
		$pflink       = get_permalink( $pfpage );
		$new_content .= '<p><i>(Full plant info in our <a href="' . $pflink . ( strpos( $pflink, '?' ) ? '&' : '?' ) . 'plantid=' . $plantid . '" target="_blank" title="' . htmlspecialchars( $post->post_title ) . '" rel="noopener noreferrer">Plant Finder</a>)</i></p>';
	}

	if ( $display_text ) {
		$new_content .= '<iframe id="plantText-' . $plantid . '" src="https://imagesrv.joyofplants.com/text/get_text/' . $plantid . '/" style="width: 100%; border: none; margin: 0; padding: 0;" frameborder="0"></iframe>';
	}
	$new_content .= '<!-- JOPend -->';

	return $new_content;
}

/**
 * Attach jop placeholder image
 */
function attach_jop_placeholder_image() {
	global $wpdb;

	$joyofplants_dummy_image = get_option( 'joyofplants_dummy_image' );

	$jop_imgs = $wpdb->get_results( 'SELECT ID, post_title, post_name, post_parent FROM ' . $wpdb->prefix . "posts WHERE post_name LIKE 'joyofplants_image%'", ARRAY_A );
	if ( count( $jop_imgs ) >= 1 ) {
		$default_image     = 0;
		$dummy_image_exist = false;
		foreach ( $jop_imgs as $img ) {
			if ( 'joyofplants_image' === strval( $img['post_title'] ) && 'joyofplants_image' === strval( $img['post_name'] ) ) {
				$default_image = $img['ID'];
			}
			if ( strval( $img['ID'] ) === strval( $joyofplants_dummy_image ) ) {
				$dummy_image_exist = true;
			}
		}
		if ( ! $default_image ) {
			if ( $dummy_image_exist ) {
				$default_image = $joyofplants_dummy_image;
			} else {
				create_jop_placeholder_image();
			}
		}
		foreach ( $jop_imgs as $img ) {
			if ( strval( $img['ID'] ) !== strval( $default_image ) ) {
				wp_delete_attachment( $img['ID'] );
			}
		}
	} else {
		create_jop_placeholder_image();
	}
}

/**
 * Create Jop Placeholder Image
 */
function create_jop_placeholder_image() {
	$file     = plugin_dir_path( __FILE__ ) . 'image/joyofplants_image.jpg';
	$filename = 'joyofplants_image.jpg';

	$upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );

	if ( ! $upload_file['error'] ) {
		$filedir        = $upload_file['file'];
		$parent_post_id = false;
		$wp_filetype    = wp_check_filetype( $filename, null );
		$attachment     = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_parent'    => $parent_post_id,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$attachment_id  = wp_insert_attachment( $attachment, $filedir, $parent_post_id );
		if ( ! is_wp_error( $attachment_id ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filedir );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			update_option( 'joyofplants_dummy_image', $attachment_id );
		}
	}
}
