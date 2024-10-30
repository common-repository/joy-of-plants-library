<?php
/**
 * ImportPage
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
$category_arr  = array();
$category_asoc = array();
$res           = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'number'     => 1000,
		'hide_empty' => false,
	)
);

foreach ( $res as $res_cat ) {
	$category_arr[ $res_cat->term_id ] = array(
		'term_id' => $res_cat->term_id,
		'name'    => $res_cat->name,
		'parent'  => $res_cat->parent,
	);
	$category_asoc[ $res_cat->name ]   = $res_cat->term_id;
}
/**
 * Sanitize products
 *
 * @param array $product Products.
 */
function joyofplants_sanitize_products( $product ) {
	$accept_array   = array( 'Yes', 'yes', 'YES', 'y', 'Y' );
	$product_fields = array(
		'id'               => 'number',
		'Name'             => 'string',
		'Description'      => 'string',
		'Categories'       => 'string',
		'JOP_PID'          => 'number',
		'SKU'              => 'string',
		'Type'             => 'string',
		'Show_Image'       => 'bool',
		'Show_Description' => 'bool',
	);

	$sanitized_prod = array();
	foreach ( $product_fields as $key => $type ) {
		if ( isset( $product[ $key ] ) ) {
			$val = $product[ $key ];
			$val = sanitize_text_field( $val );
			if ( 'string' === strval( $type ) ) {
				$val = wp_strip_all_tags( $val );
				if ( 'Categories' === strval( $key ) ) {
					$val = $val; // escape dont needed, we use symbol ">" for sub-category, I do escaple later Line:102.
				} elseif ( 'SKU' === strval( $key ) ) {
					$val = preg_replace( '/[^a-zA-Z0-9]+/', '', $val ); // left only digits and characters.
				} else {
					$val = esc_html( $val );
				}
			} elseif ( 'number' === strval( $type ) ) {
				$val = preg_replace( '/[^\\d]+/', '', $val ); // left only numbers.
				$val = intval( $val );
			} elseif ( 'bool' === strval( $type ) ) {
				$val = in_array( strval( $val ), $accept_array, true ) ? 'yes' : '';
			}
			$sanitized_prod[ $key ] = $val;
		} else {
			$sanitized_prod[ $key ] = '';
		}
	}
	return $sanitized_prod;
}

if ( wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['updateProducts_nounce'] ) ? $_POST['updateProducts_nounce'] : '' ) ), 'updateProducts_nounce' ) ) {
	ob_end_clean();

	$result                  = array();
	$post_update_products    = wp_strip_all_tags( wp_unslash( isset( $_POST['updateProducts'] ) ? $_POST['updateProducts'] : '' ) );
	$products_arr            = json_decode( urldecode( base64_decode( $post_update_products ) ), true );
	$products                = array_map( 'joyofplants_sanitize_products', $products_arr ); // sanitize, validate, escape - all in one place.
	$joyofplants_dummy_image = get_option( 'joyofplants_dummy_image' );
	if ( is_array( $products ) ) {
		foreach ( $products as $product ) {
			$product_post_id = $product['id'];
			$product_name    = $product['Name'];

			$product_post = get_post( $product_post_id );
			if ( ! $product_post ) {
				$result[] = array(
					'name'  => "(ID:$product_post_id) " . $product_name,
					'error' => 'Product does not exist',
				);
				continue;
			}


			$product_description = $product['Description'];
			$product_categories  = $product['Categories'];
			$product_pid         = $product['JOP_PID'];
			$product_sku         = $product['SKU'];
			$product_type        = $product['Type'];
			$product_show_image  = $product['Show_Image'];
			$product_show_text   = $product['Show_Description'];
			$post_sku            = get_post_meta( $product_post_id, '_sku', true );
			$product_post_type   = get_product_type( $product_post_id );
			$post_content        = $product_post->post_content;

			if ( $product_categories ) {
				$prod_cat_arr = explode( ', ', $product_categories );
				foreach ( $prod_cat_arr as $cat1 ) {
					$cat_arr = explode( ' > ', $cat1 );
					$cat2    = $cat_arr[ count( $cat_arr ) - 1 ];
					if ( isset( $category_asoc[ $cat2 ] ) ) {
						wp_set_post_terms( $product_post_id, esc_html( $category_asoc[ $cat2 ] ), 'product_cat', true ); // escape category name before add in mysql.
					}
				}
			}
			if ( strval( $post_sku ) === strval( $product_sku ) && ( ! $product_type || strval( $product_post_type ) === strval( $product_type ) ) ) {

				$pos_prod = strpos( $product_description, '[JoyOfPlantsText]' );
				$pos_post = strpos( $post_content, '[JoyOfPlantsText]' );

				if ( false !== $pos_prod && false === $pos_post ) {
					$prod_desc_arr = explode( '[JoyOfPlantsText]', $product_description );
					$pos_post2     = strpos( $prod_desc_arr[0], '[JoyOfPlantsText]' );
					if ( false !== $pos_post2 ) {
						$post_content = substr_replace( $post_content, '[JoyOfPlantsText]', $pos_prod, 0 );
					} else {
						$post_content = $post_content . ' [JoyOfPlantsText]';
					}
				} elseif ( false === $pos_prod && false !== $pos_post ) {
					$post_content = str_replace( '[JoyOfPlantsText]', '', $post_content );
				}
				$post_data = array(
					'ID'           => $product_post_id,
					'post_content' => $post_content,
				);
				wp_update_post( wp_slash( $post_data ) );

				update_post_meta( $product_post_id, 'jop_product_image_expire', 0 );
				update_post_meta( $product_post_id, 'jop_product_pid', $product_pid );
				update_post_meta( $product_post_id, 'jop_product_display_image', $product_show_image );
				update_post_meta( $product_post_id, 'jop_product_display_text', $product_show_text );
				if ( $product_pid ) {
					jop_attachment_to_product( $product_post_id );
				}
			} else {
				$result[] = array(
					'name'  => "(ID:$product_post_id) " . $product['Name'],
					'error' => strval( $post_sku ) !== strval( $product_sku ) ? 'Product has different SKU' : 'Product has different Type',
				);
			}
		}
	} else {
		$result[] = array(
			'name'  => 'File',
			'error' => 'Bad File',
		);
	}
	echo wp_json_encode( $result );
	exit;
}
/**
 * Get product type
 *
 * @param int $post_id Post id.
 */
function get_product_type( $post_id ) {
	$t = get_the_terms( $post_id, 'product_type' );
	if ( $t ) {
		return $t[0]->slug;
	} else {
		return '';
	}

}

if ( isset( $_POST['import_file'] ) ) {
	try {
		$is_test = isset( $_POST['test'] ) ? true : false;
		$file_error = isset( $_FILES['plantlistFile']['error'] ) ? intval( sanitize_text_field( wp_unslash( $_FILES['plantlistFile']['error'] ) ) ) : 0;
		if ( 0 !== intval( $file_error ) ) {
			$php_file_upload_errors = array(
				0 => 'There is no error, the file uploaded with success',
				1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
				2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
				3 => 'The uploaded file was only partially uploaded',
				4 => 'No file was uploaded',
				6 => 'Missing a temporary folder',
				7 => 'Failed to write file to disk.',
				8 => 'A PHP extension stopped the file upload.',
			);
			echo esc_html( $php_file_upload_errors[ $file_error ] );
			exit;
		}
		if ( $is_test ) {
			echo "--_FILES--\n\r";
			var_dump( $_FILES );
		}
		$fields_arr     = array();
		$data_arr       = array();
		$all_categories = array();
		// @codingStandardsIgnoreStart
		$handle         = isset( $_FILES['plantlistFile']['tmp_name'] ) ? fopen( $_FILES['plantlistFile']['tmp_name'], 'r' ) : false; // should not require sanitizing nor unslashing either, as that's set by PHP internally with random alphanumeric characters.
		// @codingStandardsIgnoreEnd
		if ( $is_test ) {
			echo "--handle--\n\r";
			var_dump( $handle );
		}
		if ( false !== $handle ) {
			$row = 0;
			do {
				$data = fgetcsv( $handle, 4000, ',' );
				if ( $is_test ) {
					echo '--data (row:' . esc_html( $row ) . ')--\n\r';
					var_dump( $data );
				}
				if ( false === $data ) {
					break;
				}
				if ( 0 === $row++ ) {
					$fields_arr    = $data;
					$fields_arr[0] = substr( $fields_arr[0], -2 );
					if ( 'id' !== strval( $fields_arr[0] ) ) {
						echo 'Incorrect File';
						exit;
					}
				} else {
					$dat    = array();
					$dat_id = 0;
					foreach ( $data as $i => $d ) {
						if ( 'id' === strval( $fields_arr[ $i ] ) ) {
							$dat_id = $d;
						}
						if ( 'Categories' === strval( $fields_arr[ $i ] ) ) {
							$all_categories = array_merge( $all_categories, explode( ', ', $d ) );
						}
						$dat[ $fields_arr[ $i ] ] = $d;
					}

					$data_arr[] = $dat;
				}
			} while ( false !== $data );
			fclose( $handle );
		}

		$all_categories = array_unique( $all_categories );
		if ( isset( $_POST['createCategory'] ) ) {
			foreach ( $all_categories as $newcat ) {
				if ( ! $newcat ) {
					continue;
				}
				$c_arr  = explode( ' > ', $newcat );
				$parent = '';
				foreach ( $c_arr as $c ) {
					if ( ! isset( $category_asoc[ $c ] ) ) {
						$product_cat_parent = array();
						if ( $parent ) {
							array(
								'parent' => $category_asoc[ $parent ],
							);
						}
						$term_data           = wp_insert_term( $c, 'product_cat', );
						$category_asoc[ $c ] = $term_data['term_id'];
					}
					$parent = $c;
				}
			}
		}
	} catch ( Exception $e ) {
		echo '!!!ERROR!!!!';
		var_dump( $e );
	}
}
?>
<div class="wrap woocommerce">
	<div class="woocommerce-progress-form-wrapper">
		<ol class="wc-progress-steps">
			<li class="
				<?php
				if ( ! isset( $_POST['import_file'] ) ) {
					echo 'active';
				} else {
					echo 'done';
				}
				?>
			">Upload CSV file
			</li>
			<li class="
				<?php
				if ( isset( $_POST['import_file'] ) ) {
					echo 'active';
				}
				?>
			">Import</li>
			<li class="">Done!</li>
		</ol>
		<?php
		if ( ! isset( $_POST['import_file'] ) && ! isset( $_POST['done'] ) ) {
			?>
			<form class="wc-progress-form-content woocommerce-importer" enctype="multipart/form-data" method="post">
				<header>
					<h3>Joy of Plants Import</h3>
					<p style="margin-bottom:10px">Import the images & data for your plants from the Joy of Plants Library.
					</p>
					<p>Click "Choose file" and find the "toWooCommerce_jopwc_export" CSV file that was generated in <a
							href="https://hub.joyofplants.com/">hub.joyofplants.com</a></p>
				</header>
				<section>
					<table class="form-table woocommerce-importer-options">
						<tbody>
							<tr>
								<th scope="row">
									<label for="upload">Choose a CSV file from your computer:</label>
								</th>
								<td>
									<input type="file" id="upload" name="plantlistFile" accept=".csv" />
									<br>
									<small>Maximum size: 2 MB</small>
								</td>
							</tr>
							<tr>
								<th><label for="woocommerce-importer-create-categories">Create new categories:</label><br />
								</th>
								<td>
									<input type="checkbox" id="woocommerce-importer-create-categories" name="createCategory" />
									<label for="woocommerce-importer-create-categories">Will create new categories from csv
										if needed</label>
								</td>
							</tr>
						</tbody>
					</table>
				</section>
				<div class="wc-actions">
					<div style="display:none">
						<input type="checkbox" name="test">debug
					</div>
					<button type="submit" class="button button-primary button-next" value="Continue"
						name="import_file">Continue</button>
				</div>
			</form>
		<?php } elseif ( isset( $_POST['import_file'] ) ) { ?>
			<form class="woocommerce-exporter woocommerce-exporter__exporting">
				<header>
					<span class="spinner is-active"></span>
					<h3>Joy of Plants Import</h3>
					<p style="margin-bottom:10px">Import the images & data for your plants from the Joy of Plants Library.
					</p>
				</header>
				<section id="progressSection">
					<div class="progressCounterBlock"><span id="progressCounter">0</span> /
						<?php echo count( $data_arr ); ?>
					</div>
					<progress class="woocommerce-exporter-progress" id="progressBar" max="100" value="0"></progress>
				</section>
				<section class="loading-message" id="slow-loading">
					Sorry our upload is slow, WordPress is very busy right now
				</section>
				<section class="loading-message" id="retry-loading">
					Retry...
				</section>
				<section id="error-loading">
					Something was wrong, try to upload later or contact with Joy of Plants
				</section>
				<section id="cancel-loading">
					<button type="button" class="button button-primary button-next" value="Stop" onclick="cancelLoading()">Stop</button>
				</section>
				<section id="canceled-loading" style="display:none">
					Import canceled, please wait the end of the process
				</section>
				<section class="woocommerce-importer-done" id="doneSection" style="display: none">
					Import complete! <strong id="countImported"></strong> products updated.
					<p style="font-size: 15px; text-align: left; line-height: 20px; margin-top: 10px;">
						Joy of Plants images & texts are now added to your plant products. To see the content added, look at
						a product in your webshop, or go to the "Products" view and edit the product.
					</p>
				</section>

				<section class="wc-importer-error-log" id="errorSection" style="display:none">
					<table class="widefat wc-importer-error-log-table">
						<thead>
							<tr>
								<th style="width: 64%">Product</th>
								<th>Reason for failure</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</section>
				<div class="wc-actions" id="doneAction" style="display: none;">
					<a class="button button-primary" href="" style="float:left;">Import another file</a>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">View
						Products</a>
				</div>
			</form>
			<script>
				var dataArr = <?php echo wp_json_encode( $data_arr ); ?>;
				var errors = [];
				var canceledCount = 0;

				var progressBar = document.getElementById('progressBar');
				var progressCounter = document.getElementById('progressCounter');
				var progress = 0;
				var loadingCanceled = false;
				var maxIntervalTime = 3;
				var progressIncrement = 2;
				var iterationHistory = [];
				var slowLoadingCount = 8;
				var errorCount = 0;
				var lastUploadTime = 0;
				var intervalCheckSlowLoading = false;

				setTimeout(() => { nextIteration() }, 500);
				intervalCheckSlowLoading = setInterval(() => { checkSlowLoading() }, 200);

				function checkSlowLoading() {
					if (errorCount < 3 && document.getElementById('retry-loading') && lastUploadTime && (new Date().valueOf() - lastUploadTime) / 1000 > maxIntervalTime) {
						document.getElementById('retry-loading').style.display = 'none';
						document.getElementById('slow-loading').style.display = 'block';
					}
				}

				function nextIteration(once = false) {
					const count = progressIncrement;
					data = dataArr.slice(progress, progress + count);
					postData(data).then((results) => {
						if (errorCount >= 3) {
							clearInterval(intervalCheckSlowLoading);
							document.getElementById('retry-loading').style.display = 'none';
							document.getElementById('slow-loading').style.display = 'none';
							document.getElementById('error-loading').style.display = 'block';
							return;
						}
						if (results.error) {
							progressIncrement = 2;
							errorCount++;
							clearInterval(intervalCheckSlowLoading);
							document.getElementById('slow-loading').style.display = 'none';
							document.getElementById('retry-loading').style.display = 'block';
							document.getElementById('retry-loading').innerText = "Retry... (" + errorCount + ")"
							if (!once) {
								nextIteration();
							}
							return;
						}
						intervalCheckSlowLoading = setInterval(() => { checkSlowLoading() }, 200);
						errorCount = 0;
						document.getElementById('retry-loading').style.display = 'none';

						progress += count;
						progressBar.value = progress / dataArr.length * 100;
						progressCounter.innerText = progress;

						if (results.response.length > 0) {
							errors = [...errors, ...results.response];
						}
						iterationHistory.push({ count, time: results.time, errors: results.response.length })
						//adaptive progress increament
						successCount = count - results.response.length;

						if (successCount) {
							nextProgressIncrement = Math.min(100, Math.max(2, Math.min(Math.floor(maxIntervalTime / (results.time / successCount)), 6 * successCount)))
							progressIncrement = nextProgressIncrement;
							document.getElementById('slow-loading').style.display = progressIncrement < slowLoadingCount ? 'block' : 'none';
						}

						if (loadingCanceled) {
							canceledCount = dataArr.length - progress < 0 ? 0 : dataArr.length - progress;
							completed();
							return;
						}
						if (progress / dataArr.length < 1) {
							if (!once) {
								nextIteration()
							}
						} else {
							completed()
						}
					});
				}


				function cancelLoading(){
					loadingCanceled = true;
					document.getElementById('cancel-loading').style.display = 'none';
					document.getElementById('canceled-loading').style.display = 'flex';
					
				}

				async function postData(data = []) {
					// Default options are marked with *
					const timeStart = lastUploadTime = new Date().valueOf()
					let fd = new FormData();
					fd.append("updateProducts", window.btoa(encodeURI(JSON.stringify(data))));
					fd.append("updateProducts_nounce", "<?php echo esc_attr( wp_create_nonce( 'updateProducts_nounce' ) ); ?>")
					let response = await fetch(window.location.href, {
						method: 'POST', // *GET, POST, PUT, DELETE, etc.
						// headers: {
						//     // 'Content-Type': 'application/json'
						//     // 'Content-Type': 'application/x-www-form-urlencoded',
						// },
						body: fd
					});
					if (response.status != 200) {
						return { error: true, status: response.status }
					}
					response = await response.json()
					const time = (new Date().valueOf() - timeStart) / 1000

					return { response, time };
				}

				function completed() {
					const li = document.querySelectorAll('.wc-progress-steps li');
					li.forEach((l, i) => {
						if (i == 2) {
							l.classList.add("active");
						} else {
							l.classList.remove("active");
							l.classList.add("done");
						}

					});
					clearInterval(intervalCheckSlowLoading);
					document.getElementById('slow-loading').remove();
					document.getElementById('retry-loading').remove();
					document.getElementById('cancel-loading').remove();
					document.getElementById('canceled-loading').remove();
					document.getElementById('progressSection').remove();
					document.getElementById('doneSection').style.display = 'block';
					document.getElementById('doneAction').style.display = 'block';
					document.querySelector(".spinner").classList.remove("is-active")
					document.getElementById('countImported').textContent = dataArr.length - errors.length - canceledCount;

					if (errors.length > 0) {
						document.getElementById('errorSection').style.display = 'block';
						const tableRef = document.querySelector('#errorSection table tbody');
						errors.forEach((er) => {
							const row = tableRef.insertRow();
							const cell1 = row.insertCell(0);
							cell1.appendChild(document.createTextNode(er['name']));
							const cell2 = row.insertCell(1);
							cell2.appendChild(document.createTextNode(er['error']));
						})
					}
				}

			</script>
			<style>
				.wc-importer-error-log {
					height: 170px;
					position: relative;
					overflow: auto;
				}

				.wc-importer-error-log-table td {
					padding: 3px !important;
				}

				.wc-importer-error-log-table th {
					padding: 6px !important;
				}

				.wc-importer-error-log-table th:last-child {
					padding-left: 3px !important;
				}

				.wc-importer-error-log-table td:first-child,
				.wc-importer-error-log-table th:first-child {
					padding-left: 20px !important;
				}

				.wc-importer-error-log-table th {
					background: #fff;
					position: sticky !important;
					top: 0 !important;
				}

				.loading-message {
					padding: 0 !important;
					text-align: center;
					color: #a16696;
					font-weight: bold;
					font-size: 16px;
					height: 0;
					position: relative;
					display: none;
					top: -20px;
				}

				#error-loading {
					padding: 0;
					text-align: center;
					color: #e00;
					font-weight: bold;
					font-size: 18px;
					margin-bottom: 20px;
					position: relative;
					display: none;
				}

				#cancel-loading,
				#canceled-loading{
					padding-top: 0;
					padding-bottom: 20px;
					display: flex;
					justify-content: center;
				}
				#canceled-loading{
					color: #f00;
					font-weight: bold;
					font-size: 20px;
				}

				#progressSection {
					position: relative;
				}

				.progressCounterBlock {
					position: absolute;
					left: 50%;
					top: 36px;
					transform: translate(-50%);
					font-size: 20px;
					font-weight: bold;
					text-shadow: 0 0 3px #fff, 0 0 3px #fff, 0 0 3px #fff, 0 0 3px #fff, 0 0 3px #fff;
					color: #000;
				}
			</style>
		<?php } ?>
	</div>
</div>
