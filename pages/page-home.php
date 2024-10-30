<?php
/**
 * HomePage
 * php version 7.2.10
 *
 * @category Page
 * @package  JoyOfPlantsLibrary
 * @author   Joy Of Plants <joyofplants@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://joyofplants.com/
 */

?>
<!--The code for the Joy of Plants Library plugin is the copyright of Joy of Plants.
It must be used in its entirety and without modification - modification of the code
invalidates the license agreement. Any use, publication or copying in any way is 
expressly prohibited without consent of Joy of Plants.-->
<div class="jop-home">
	<div class="jop-title">Joy of Plants Library <div class="flex1"></div>version: <?php echo esc_html( JOP_LIBRARY_PLUGIN_VERSION ); ?></div>
	<h1>Overview</h1>

	<p>Adds images &amp; text descriptions from the Joy of Plants' Library to your
		plant products. A picture sells a thousand plants!</p>

	<p>Adds the code for a fully-featured Plant Finder with images, data and
		descriptions for 15,000+ plants to your
		website.</p>

	<h3>Follow these steps to use images &amp; text descriptions:</h3>
	<ol>
		<li><b>First use the Joy of Plants “API Settings” option to add the
				credentials</b> <span>you were given by Joy of
				Plants. You will only need to do this once to connect your webshop to the
				Joy of Plants library.</span> <b>See
				the instructions at the bottom of the page for registering to get your
				credentials.</b></li>
		<li><b>Add your plant products to the WooCommerce </b><a target="_blank"
				href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>">
				<b>Products</b></a><b> page</b><span>, if they
				aren't there already. (When "importing" plant product names from an EPOS
				tool "export", you'll need to "tidy"
				names and replace abbreviations before importing them. You can log into
				</b><a href="https://hub.joyofplants.com/"><b>hub.joyofplants.com</b></a><b>
					and
					use the "Plant Name List
					Matcher"</b><span> tool to match abbreviated EPOS names to full plant
					names.)</span></li>
		<li><b>Use the Joy of Plants </b><a target="_blank"
				href="<?php echo esc_url( admin_url( 'admin.php?page=joyofplants_export' ) ); ?>">
				<b>Export</b></a> <span>to extract the
				plant
				products that you want to add images &amp; texts to - WooCommerce will
				export a CSV file. </span></li>
		<li><b>Log into </b><a target="_blank" href="https://hub.joyofplants.com/">
				<b>hub.joyofplants.com</b></a><b> and use
				the "Plant Name List Matcher"</b><span> tool to match the names in the
				CSV export to our database - you then
				download a new file with added columns matching the plant names to our
				library.</span></li>
		<li><b>Open and check the matched file (eg in Excel or Libre Office):</b><span>
				if you think a matched name, in the
				'JOP Plant Name' column, is wrong, delete the plant ID number ("PID") from
				the "JOP PID" column; or, if there's
				a plant mentioned in the 'Comments' column that you think is correct, copy
				its PID into the "JOP PID"
				column.</span></li>
		<li><b>In </b><a target="_blank" href="https://hub.joyofplants.com/">
				<b>hub.joyofplants.com</b></a><b> use the
				"Ecommerce Image &amp; Text Library"</b><span> tool to upload the checked
				matched file, and it will generate the
				"toWooCommerce...csv" file for you to Import into WooCommerce (and the
				file to upload into the Joy of Plants
				Plant Finder if you have one).</span></li>
		<li><b>In WooCommerce use the Joy of Plants </b><a target="_blank"
				href="<?php echo esc_url( admin_url( 'admin.php?page=joyofplants_import' ) ); ?>">
				<b>Import</b></a> <span>to import the
				"toWooCommerce...csv" file – this will add the images &amp; texts to your
				plant products.</span></li>
	</ol>

	<h3>Editing a product after the import</h3>

	<p>In WooCommerce Admin you will see [JoyOfPlantsText] in the product's
		"Description". <span style="color:red">Don't
			edit this code!</span></p>

	<p>You can add your own text before or after the [JoyOfPlantsText]. Better still,
		add your text to the "Product short
		description" to keep it separate.</p>

	<p>If you add your own "Product image", the Joy of Plants image will appear in
		the "Product gallery" on the product's
		webshop page. If you remove your image, the Joy of Plants image will appear as
		the main "Product image" on the
		product's webshop page.</p>

	<p>The Joy of Plants image isn't shown as the main "Product image" in the Admin
		page. Instead in the "Joy of Plants"
		tab there's a "Product id (PID)"  that creates links to the images in our
		Library, and the image is shown in the
		"Joy of Plants image" box.</p>

	<p>You can use the "Display Image" and "Display Text" checkboxes to turn off
		display of Joy of Plants images and texts
		if you need to, for any reason.</p>

	<p>(If you use the WooCommerce "Export" on the Products page you will see extra
		fields in the exported file called
		"Meta: jop_product_pid" etc. Leave the contents of these "Meta: jop" fields
		alone and don't change them – these are
		the links to our library).</p>

	<br /><br />
	<h3>Follow these steps to use the Plant Finder:</h3>
	<ol>
		<li><b>First use the WordPress “Pages” option to add a new page to your website
				to contain the Plant Finder.</b>
		</li>
		<li><b>Use the Joy of Plants </b><a target="_blank"
				href="<?php echo esc_url( admin_url( 'admin.php?page=joyofplants_plantfinder' ) ); ?>">
				<b>Plant Finder</b></a> <span>to
				select
				the page. This adds the Plant Finder code to that page.</span></li>
	</ol>
	<br /><br />

	<h2>REGISTER FOR A "PAID FOR" ACCOUNT WITH JOY OF PLANTS TO USE THE JOY OF PLANTS
		LIBRARY AND/OR PLANT FINDER</h2>
	<ol>
		<li>Adding images & texts to your WooCommerce pages requires API CREDENTIALS
			that you add to the "API Settings"
			page. The Plant Finder does not require API credentials, however it does
			require an account with Joy of Plants.
		</li>
		<li>If you don't yet have an account with Joy of Plants, go to
			hub.joyofplants.com and click "Register here". Follow
			the on-screen prompts to create your account.</li>
		<li>After you have an account, login at hub.joyofplants.com.</li>
		<li>To use images & texts, click "Ecommerce Image & Text Library", then click
			"Upgrade to use Image Library", follow
			the on screen prompts to agree the Terms and Conditions and click
			"Request Upgrade".</li>
		<li>To add the Plant Finder to your site, click "Kiosk & Website Plant Finder",
			then click "Upgrade to use Plant
			Finder", follow the on screen prompts to agree the Terms and Conditions and
			click "Request Upgrade".</li>
		<li>Joy of Plants will contact you to set up your account in the Library and
			supply API credentials if you need
			them. We will ask how many plants you need images & texts for - your Library
			subscription fee will depend on the
			number required.</li>
	</ol>
</div>
<style>
	.jop-title{
		background: #1d2327;
		font-size: 22px;
		line-height: 22px;
		color: #f0f0f1;
		padding: 12px;
		height: auto;
		width: auto;
		display: flex;
		flex-direction: row;
	}
	.flex1{
		flex: 1;
	}
</style>
