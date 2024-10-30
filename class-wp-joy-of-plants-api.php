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

/**
 * Wp_Joy_Of_Plants_Api
 */
class Wp_Joy_Of_Plants_Api {
	/**
	 * Username
	 *
	 * @var mixed
	 */
	private $username;
	/**
	 * Password
	 *
	 * @var mixed
	 */
	private $password;
	/**
	 * Clientid
	 *
	 * @var mixed
	 */
	private $clientid;
	/**
	 * Clientsecret
	 *
	 * @var mixed
	 */
	private $clientsecret;
	/**
	 * Accesstoken
	 *
	 * @var mixed
	 */
	private $accesstoken;
	/**
	 * Accesstoken_expire
	 *
	 * @var mixed
	 */
	private $accesstoken_expire;
	/**
	 * Connection
	 *
	 * @var mixed
	 */
	public $connection;
	/**
	 * Current_connection_reject
	 *
	 * @var bool
	 */
	private $current_connection_reject = false;
	/**
	 * Apiurl
	 *
	 * @var string
	 */
	private $apiurl = 'https://api.joyofplants.com/api';
	/**
	 * Expiretime
	 *
	 * @var int
	 */
	private $expiretime = 604800;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->get_jop_options();
	}
	/**
	 * Get_jop_options
	 *
	 * @return void
	 */
	public function get_jop_options() {
		$this->username           = get_option( 'joyofplants_api_username' );
		$this->password           = get_option( 'joyofplants_api_password' );
		$this->clientid           = get_option( 'joyofplants_api_clientid' );
		$this->clientsecret       = get_option( 'joyofplants_api_clientsecret' );
		$this->accesstoken        = get_option( 'joyofplants_api_accesstoken' );
		$this->accesstoken_expire = get_option( 'joyofplants_api_accesstoken_expire' );
		$this->connection         = $this->accesstoken && $this->accesstoken_expire > time();
	}

	/**
	 * Get_access_token
	 *
	 * @param  mixed $retry retry.
	 * @return mixed
	 */
	public function get_access_token( $retry = 0 ) {

		$client_base_64 = 'Basic ' . base64_encode( $this->clientid . ':' . $this->clientsecret );

		$headers = array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/x-www-form-urlencoded',
			'Authorization' => $client_base_64,
		);
		$args    = array(
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'headers'     => $headers,
			'blocking'    => true,
			'body'        => array(
				'grant_type' => 'password',
				'username'   => $this->username,
				'password'   => $this->password,
			),
		);

		$r = wp_remote_post( $this->apiurl . '/auth/token', $args );
		if ( is_wp_error( $r ) ) {
			if ( $retry < 3 ) {
				return $this->get_access_token( $retry + 1 );
			} else {
				$response = array( 'error' => 'WordPress Error' );
			}
		} else {
			$response = json_decode( $r['body'], true );
		}
		if ( isset( $response['error'] ) ) {
			update_option( 'joyofplants_api_accesstoken', false );
			update_option( 'joyofplants_api_accesstoken_expire', 0 );
			$this->current_connection_reject = true;
			return $response;
		} else {
			update_option( 'joyofplants_api_accesstoken', $response['access_token'] );
			update_option( 'joyofplants_api_accesstoken_expire', time() + $response['expires_in'] - 60 );
			$this->accesstoken_expire        = time() + $response['expires_in'] - 60;
			$this->accesstoken               = $response['access_token'];
			$this->current_connection_reject = false;
			return '';
		}
	}

	/**
	 * Get_images
	 *
	 * @param  mixed $pids pids.
	 * @param  mixed $sizes sizes.
	 * @param  mixed $retry retry.
	 * @return mixed
	 */
	public function get_images( $pids, $sizes = array( '600' ), $retry = 0 ) {

		$postfiled = array(
			'pids'  => $pids,
			'sizes' => $sizes,
		);
		$headers   = array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->accesstoken,
		);
		$args      = array(
			'timeout'     => 0,
			'redirection' => 3,
			'httpversion' => '1.1',
			'headers'     => $headers,
			'body'        => wp_json_encode( $postfiled ),
		);
		$r         = wp_remote_post( $this->apiurl . '/v2/plants/images/', $args );
		if ( is_wp_error( $r ) ) {
			if ( $retry < 3 ) {
				return $this->get_images( $pids, $sizes, $retry + 1 );
			} else {
				return array(
					'error' => 'WordPress Error',
				);
			}
		}

		$response = json_decode( $r['body'], true );
		if ( ! isset( $response['plants'] ) && ! isset( $response['error'] ) ) {
			if ( $retry >= 1 ) {
				return array( 'error' => $response['detail'] );
			}
			$r = $this->get_access_token();
			if ( $r ) {
				return array( 'error' => $response['detail'] );
			} else {
				$response = $this->get_images( $pids, $sizes, 1 );
			}
		}
		return $response;
	}

	/**
	 * Get_plant_id
	 *
	 * @param  mixed $post_id post id.
	 * @return mixed
	 */
	public function get_plant_id( $post_id = false ) {
		global $post;
		if ( ! $post_id ) {
			$post_id = $post->ID;
		}
		$r = get_post_meta( $post_id, 'jop_product_pid' );
		if ( $r && count( $r ) !== 0 && $r[0] ) {
			return $r[0];
		} else {
			return false;
		}
	}

	/**
	 * Generate_image
	 *
	 * @param  mixed $post_id post id.
	 * @return mixed
	 */
	private function generate_image( $post_id = false ) {
		$plant_id = $this->get_plant_id( $post_id );
		if ( ! $plant_id ) {
			return 'plantId is empty';
		}
		$res = $this->get_images( array( $plant_id ), array( '600', '250', '48' ) );
		if ( ! $res ) {
			return 'Connection not made';
		}
		if ( ( is_array( $res ) && isset( $res['error'] ) ) || isset( $res['plants'][0]['error'] ) ) {
			update_post_meta( $post_id, 'jop_product_image_l', false );
			update_post_meta( $post_id, 'jop_product_image_m', false );
			update_post_meta( $post_id, 'jop_product_image_s', false );
			update_post_meta( $post_id, 'jop_product_image_expire', time() + 20 );
			if ( is_array( $res ) && isset( $res['error'] ) ) {
				update_post_meta( $post_id, 'jop_product_image_error', $res['error'] );
				return $res['error'];
			} else {
				update_post_meta( $post_id, 'jop_product_image_error', $res['plants'][0]['error'] );
				return $res['plants'][0]['error'];
			}
		} else {
			$images = $res['plants'][0]['images'];
			foreach ( $images as $i ) {
				$size = 'l';
				if ( strval( $i['size'] ) === '250' ) {
					$size = 'm';
				}
				if ( strval( $i['size'] ) === '48' ) {
					$size = 's';
				}
				update_post_meta( $post_id, 'jop_product_image_' . $size, $i['url'] );
			}
			update_post_meta( $post_id, 'jop_product_image_error', false );
			update_post_meta( $post_id, 'jop_product_image_expire', time() + $this->expiretime );
		}
		return '';
	}
	/**
	 * Convert_size
	 *
	 * @param  mixed $size size.
	 * @return mixed
	 */
	private function convert_size( $size ) {
		$size_f = array(
			'l'                     => array( 'l', 600 ),
			'm'                     => array( 'm', 250 ),
			's'                     => array( 's', 45 ),
			'large'                 => array( 'l', 600 ),
			'medium'                => array( 'm', 250 ),
			'small'                 => array( 's', 45 ),
			'woocommerce_single'    => array( 'l', 600 ),
			'woocommerce_thumbnail' => array( 'm', 250 ),
			'full'                  => array( 'l', 600 ),
		);
		if ( is_array( $size ) ) {
			$s1 = $size[0];
			if ( $size[0] <= 100 ) {
				$s = 's';
			} elseif ( $size[0] <= 250 ) {
				$s = 'm';
			} else {
				$s = 'l';
			}
		} elseif ( isset( $size_f[ $size ] ) ) {
			$s = $size;
		} else {
			$s = 'l';
		}
		$res = $size_f[ $s ];
		if ( isset( $s1 ) ) {
			$res[1] = $s1;
		}
		return $res;

	}
	/**
	 * _get_image_url
	 *
	 * @param  mixed $post_id post id.
	 * @param  mixed $size size.
	 * @return mixed
	 */
	private function _get_image_url( $post_id, $size = 'l' ) {
		global $post;
		if ( ! $post_id && null === $post ) {
			return '';
		}
		$post_id = intval( $post_id ) === 0 ? $post->ID : $post_id;

		if ( $this->current_connection_reject ) {
			update_post_meta( $post_id, 'jop_product_image_l', false );
			update_post_meta( $post_id, 'jop_product_image_m', false );
			update_post_meta( $post_id, 'jop_product_image_s', false );
			update_post_meta( $post_id, 'jop_product_image_error', 'Connection Refused' );
			update_post_meta( $post_id, 'jop_product_image_expire', false );
			return '';
		}

		$pid = $this->get_plant_id( $post_id );

		if ( ! $pid ) {
			return '';
		}
		$expire = get_post_meta( $post_id, 'jop_product_image_expire', true );
		if ( ! $expire || $expire <= time() || ! $this->connection ) {
			$r = $this->generate_image( $post_id );
			if ( $r ) {
				return '';
			}
		}
		$s   = $this->convert_size( $size );
		$url = get_post_meta( $post_id, 'jop_product_image_' . $s[0], true );
		return $url;
	}
	/**
	 * Get_image
	 *
	 * @param  mixed $post_id post id.
	 * @param  mixed $size size.
	 * @return mixed
	 */
	public function get_image( $post_id, $size = 'l' ) {
		$url = $this->_get_image_url( $post_id, $size );
		$s   = $this->convert_size( $size );

		if ( ! $url ) {
			return false;
		}
		$image = "<img src='$url' width='$s[1]' loading='lazy'/>";
		return $image;
	}

	/**
	 * __call
	 *
	 * @param  mixed $method method.
	 * @param  mixed $arguments arguments.
	 * @return mixed
	 */
	public function __call( $method, $arguments ) {
		if ( strval( $method ) === 'get_image_url' ) {
			if ( count( $arguments ) === 1 ) {
				return call_user_func_array( array( $this, '_get_image_url' ), array( 0, $arguments[0] ) );
			} elseif ( count( $arguments ) === 2 ) {
				return call_user_func_array( array( $this, '_get_image_url' ), $arguments );
			}
		}
	}
}
