<?php
/**
 * ExportPage
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
	exit; // Exit if accessed directly.
}
global $product_categories_global;
$product_categories = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'number'     => 100000,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

foreach ( $product_categories as $cat1 ) {
	$product_categories_global[ $cat1->term_id ] = $cat1;
}

if ( wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['jop_export_nounce'] ) ? $_POST['jop_export_nounce'] : '' ) ), 'jop_export_nounce' ) ) {

	$type_exist = isset( $_POST['types'] ) && is_array( $_POST['types'] );
	$cat_exist  = isset( $_POST['categories'] ) && is_array( $_POST['categories'] );
	$args       = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'tax_query'      => array(
			'relation' => ( $type_exist && $cat_exist ) ? 'AND' : 'OR',
		),
	);
	if ( $type_exist ) {
		$tax_query  = array(
			'taxonomy' => 'product_type',
			'field'    => 'slug',
			'terms'    => array(),
		);
		$temp_types = explode( ',', sanitize_text_field( wp_unslash( isset( $_POST['types_list'] ) ? $_POST['types_list'] : '' ) ) );
		foreach ( $temp_types as $term1 ) {
			$tax_query['terms'][] = wp_strip_all_tags( $term1 );
		}
		$args['tax_query'][] = $tax_query;
	}
	if ( $cat_exist ) {
		$tax_query = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array(),
		);
		$temp_cats = explode( ',', sanitize_text_field( wp_unslash( isset( $_POST['categories_list'] ) ? $_POST['categories_list'] : '' ) ) );
		foreach ( $temp_cats as $cat1 ) {
			$tax_query['terms'][] = wp_strip_all_tags( $cat1 );
		}
		$args['tax_query'][] = $tax_query;
	}
	$query     = new WP_Query( $args );
	$new_posts = $query->get_posts();


	ob_end_clean();

	$inc              = get_option( 'joyofplants_export_index', 1 );
	$last_export_date = get_option( 'joyofplants_export_date' );
	$current_date     = gmdate( 'd', time() );
	if ( $last_export_date !== $current_date ) {
		$inc = 1;
	}

	update_option( 'joyofplants_export_date', $current_date );
	update_option( 'joyofplants_export_index', $inc + 1 );

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename="jopwc_export_' . date_format( new DateTime(), 'Y-m-d' ) . '_' . str_pad( $inc, 2, '0', STR_PAD_LEFT ) . '.csv"' );

	$csv_array = array(
		array( 'id', 'Type', 'SKU', 'Name', 'Description', 'Categories', 'Slug', 'JOP_PID', 'Show_Image', 'Show_Description' ),
	);

	if ( isset( $new_posts ) ) {
		foreach ( $new_posts as $new_post ) :

			$sku           = get_post_meta( $new_post->ID, '_sku', true );
			$pid           = get_post_meta( $new_post->ID, 'jop_product_pid', true );
			$display_image = get_post_meta( $new_post->ID, 'jop_product_display_image', true );
			$display_text  = get_post_meta( $new_post->ID, 'jop_product_display_text', true );

			$csv_array[] = array(
				$new_post->ID,
				jop_get_product_type( $new_post->ID ),
				$sku,
				$new_post->post_title,
				$new_post->post_content,
				jop_get_category( $new_post->ID ),
				$new_post->post_name,
				$pid,
				$display_image ? 'yes' : 'no',
				$display_text ? 'yes' : 'no',
			);

		endforeach;

		$fp = fopen( 'php://output', 'wb' );
		fwrite( $fp, "\xEF\xBB\xBF" );
		foreach ( $csv_array as $line ) {
			fputcsv( $fp, $line, ',' );
		}
		fclose( $fp );
		exit;
	}
}

/**
 * Get category
 *
 * @param int     $post_id post id.
 * @param boolean $first is first.
 */
function jop_get_category( $post_id, $first = false ) {
	$terms = get_the_terms( $post_id, 'product_cat' );
	if ( ! $terms ) {
		return '';
	}
	$category_arr = array();
	foreach ( $terms as $term ) {
		$category_arr[] = jop_get_category_name( $term->term_id, $first );
	}
	return implode( ', ', $category_arr );
}
/**
 * Get category name
 *
 * @param int     $term_id term id.
 * @param boolean $first is first.
 */
function jop_get_category_name( $term_id, $first = false ) {
	global $product_categories_global;
	$term = $product_categories_global[ $term_id ];
	$name = $term->name;
	if ( ! $first && $term->parent ) {
		$name = jop_get_category_name( $term->parent ) . ' > ' . $name;
	}
	return $name;
}
/**
 * Get product type
 *
 * @param int $post_id post id.
 */
function jop_get_product_type( $post_id ) {
	$t = get_the_terms( $post_id, 'product_type' );
	if ( $t ) {
		return $t[0]->slug;
	} else {
		return '';
	}

}
?>

<div class="wrap woocommerce">
	<div class="woocommerce-exporter-wrapper">
		<form class="woocommerce-exporter" action="" method="post">
			<input type="hidden" name="export" value="export">
			<header>
				<span class="spinner is-active"></span>
				<h3>Export a plant list to add Joy of Plants images &amp; texts to.</h3>
				<p>After the Export, go to <a href="https://hub.joyofplants.com/">hub.joyofplants.com</a> to use the "Plant Name
					List Matcher", then the "Ecommerce Image & Text Library" tool to create your import file. See <a
						href="/wp-admin/admin.php?page=joyofplants">Overview</a> for more info.</p>
			</header>
			<section>
				<table class="form-table woocommerce-exporter-options">
					<tbody>
						<tr>
							<th scope="row">
								<label for="woocommerce-exporter-category">Which product category should be exported?</label>
							</th>
							<td>
								<select id="woocommerce-exporter-category" class="woocommerce-exporter-category wc-enhanced-select"
									style="width:100%;" name="categories[]" multiple data-placeholder="Export all categories">
									<?php
									foreach ( $product_categories as $cat1 ) {
										echo '<option value="' . esc_attr( $cat1->slug ) . '">' . esc_html( $cat1->name ) . '</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="woocommerce-exporter-types">Which product types should be exported?</label>
							</th>
							<td>
								<select id="woocommerce-exporter-types" class="woocommerce-exporter-types wc-enhanced-select"
									name="types[]" style="width:100%;" multiple data-placeholder="Export all products">
									<option value="simple">Simple product</option>
									<option value="grouped">Grouped product</option>
									<option value="external">External/Affiliate product</option>
									<option value="variable">Variable product</option>
									<option value="variation">Product variations</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="padding: 0 10px; opacity: 0.7;">
									We recommend leaving "Export all products" as the type to export unless you actively use "product
									types" in your webshop
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<progress class="woocommerce-exporter-progress" max="100" value="0"></progress>
			</section>
			<div class="wc-actions">
				<input type="hidden" id="types_list" name="types_list" value="" />
				<input type="hidden" id="categories_list" name="categories_list" value="" />
				<input type="hidden" name="jop_export_nounce"
					value="<?php echo esc_attr( wp_create_nonce( 'jop_export_nounce' ) ); ?>" />
				<button type="submit" class="woocommerce-exporter-button button button-primary" onclick="updateJopLists()"
					value="Generate CSV">Generate
					CSV</button>
			</div>
		</form>
	</div>
	<script>
		function updateJopLists() {
			document.getElementById('types_list').value = getSelectValues(document.getElementById('woocommerce-exporter-types')).join(',');
			document.getElementById('categories_list').value = getSelectValues(document.getElementById('woocommerce-exporter-category')).join(',');
		}
		function getSelectValues(select) {
			var result = [];
			var options = select && select.options;
			var opt;

			for (var i = 0, iLen = options.length; i < iLen; i++) {
				opt = options[i];

				if (opt.selected) {
					result.push(opt.value || opt.text);
				}
			}
			return result;
		}
	</script>
</div>
