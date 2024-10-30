=== Joy of Plants Library ===
Contributors: joyofplants
Tags: e-commerce, webshop, gardening, plants-library, images, plants-descriptions, sales, sell, woo, shop, cart, checkout, downloadable, downloads, woo commerce
Requires at least: 5.2
Tested up to: 6.6.1
Requires PHP: 7.0
Stable tag: 1.0.24
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Adds images & text descriptions from the Joy of Plants' Library to your WooCommerce webshop. A picture sells a thousand plants!
Joy of Plants supplies webshop info, Plant Finders, Kiosks and Bed Cards to the UK's top plant retailers.
(Requires a "paid-for" account with Joy of Plants.)


== Installation ==

### REGISTER FOR A "PAID FOR" ACCOUNT WITH JOY OF PLANTS TO USE THE JOY OF PLANTS LIBRARY AND/OR PLANT FINDER

Adding images & texts to your WooCommerce pages requires API CREDENTIALS that you add to the "API Settings" page.
The Plant Finder does not require API credentials, however it does require an account with Joy of Plants.

- If you don't yet have an account with Joy of Plants, go to hub.joyofplants.com and click "Register here". Follow the on-screen prompts to create your account,
- After you have an account, login at hub.joyofplants.com. 
- To use images & texts, click "Ecommerce Image & Text Library", then click "Upgrade to use Image Library", follow the on screen prompts to agree the Terms and Conditions and click "Request Upgrade".
- To add the Plant Finder to your site, click "Kiosk & Website Plant Finder", then click "Upgrade to use Plant Finder", follow the on screen prompts to agree the Terms and Conditions and click "Request Upgrade"..
- Joy of Plants will contact you to set up your account in the Library and supply your API credentials. We will ask how many plants you need images & texts for - your Library subscription fee will depend on the number required.

### INSTALL THE JOY OF PLANTS LIBRARY FROM THE WORDPRESS PLUGIN DIRECTORY

1. Click ‘Plugins’ on the WordPress menu and click ‘Add New’.
2. Click in the ‘Search plugins...’ box and type ‘Joy of Plants Library’ to find it in the Directory of plugins.
3. Click ‘Install Now’.
4. Click ‘Activate Plugin’.
5. See the instructions under ‘After activation’ below for what to do next.

### INSTALL THE JOY OF PLANTS LIBRARY MANUALLY

1. Click ‘Plugins’ on the WordPress menu and click ‘Add New’.
1. Click ‘Upload Plugin’, choose the ZIP file supplied to you by Joy of Plants.
3. Click ‘Install Now’.
4. Click ‘Activate Plugin’.
5. See the instructions under ‘After activation’ below for what to do next.

### AFTER ACTIVATION, ADD API CREDENTIALS TO USE IMAGES & TEXTS

1. Click ‘API Settings’. You need "API credentials" supplied to you by Joy of Plants to connect to the library. Contact support@joyofplants.com, if you don't have them already.
2. Add your "Username", "Password", "Client Id" and "Client Secret" EXACTLY as given by Joy of Plants - it's important to use the same capitalisation on letters.
3. Click ‘Save Changes’. 
4. The message "Connection is Successful" will appear if your credentials are correct.
5. Go to ‘Overview’ for instructions on how to add images & texts to your plant products.

### AFTER ACTIVATION, ADD PLANT FINDER TO YOUR SITE

1. Add a new page to your site where you want the Plant Finder to appear - add it to the navigation menu.
2. Go to ‘Plant Finder’ and select the page where you want to add the Plant Finder.
3. Click ‘Save Changes’.
4. Check the page in your live site. The Plant Finder should be displayed.

== Frequently Asked Questions ==

= Where I can get API credentials? =
Contact support@joyofplants.com to apply for an acount with Joy of Plants and receive API credentials, or get help with an existing account.

= Plant image doesn't appear =
1. Click ‘API Settings’.
2. Make sure that the message "Connection is Successful" is shown, if not, check your credentials as sent to you by Joy of Plants.
3. Go to ‘Products’ and ‘Edit’ the plant product, click on the ‘Joy of Plants’ tab and check that there is a number in the ‘Plant Id’ box and that ‘Display image’ is selected.
4. Contact support@joyofplants.com if you need further help.

= Plant description doesn't appear =
1. Go to ‘Products’ and ‘Edit’ the plant product, click on the ‘Joy of Plants’ tab and check that there is a number in the ‘Plant Id’ box and that ‘Display text’ is selected.
2. Contact support@joyofplants.com if you need further help.

= Plant Finder doesn't appear =
1. Go to ‘Plant Finder’ and check that the correct page is selected.
2. Look at the Plant Finder page on your live site. Is a message shown there? If the message says "Plant Finder not authorised for the current domain" the Plant Finder code has been added correctly.
3. If there is no error message, or the page is empty or looks odd, there may be conflicts between other plugins and the Plant Finder code we add to the page. The Plant Finder is added to your site page as an "iframe" code. Plugins that affect iframe code (eg lazy loading plugins, or SEO plugins) may conflict. Try turning off any such plugins on the Plant Finder page.
4. Contact support@joyofplants.com, sending a link to the  URL of the Plant Finder page on your site, if you need further help.

== Screenshots ==
1. The ‘Overview’ page where you find instructions on how to add images & texts from the library to your plant products.
2. The ‘API Settings’ page for inserting your credentials from Joy of Plants.
3. A product admin page where you can see Joy of Plants image and shortcode for display text.
4. A webshop page showing the Joy of Plants image and plant description.

== Changelog ==

= 1.0.24 =
Release Date: August 27th, 2024
Plugin tested for compatibility with WordPress version 6.6.1 and WooCommerce plugin version 9.2.3, no issues found

= 1.0.23 =
Release Date: March 27th, 2024
Plugin tested for compatibility with WordPress version 6.4.3 and WooCommerce plugin version 8.7.0, no issues found

= 1.0.22 =
Release Date: June 12th, 2023
Added "Stop" button in import

= 1.0.21 =
Release Date: May 24th, 2023
Plugin tested for compatibility with WordPress version 6.2.2 and WooCommerce plugin version 7.7.0, no issues found

= 1.0.20 =
Release Date: May 18th, 2023
Updated text on “Overview” page and “Installation” instructions

= 1.0.19 =
Release Date: July 4th, 2021
Changed count of upload plants for iteration on the "Import" Page

= 1.0.18 =
Release Date: March 2nd, 2021
Updated text on “Overview” page and “Installation” instructions

= 1.0.17 =
Release Date: January 19th, 2021
Updated to work when “short_open_tag” is turned off on the host server 

= 1.0.16 =
Release Date: November 21st, 2020
The category list was sorted by name on the Export page
Removed duplicate Joy of Plants placeholder image in Media Library

= 1.0.15 =
Release Date: November 12th, 2020
Changed filename for Exported files

= 1.0.14 =
Release Date: November 10th, 2020
Changed texts on pages ‘Overview’, ’Export’ and ’Import’
Changed links for sites with non default "wp-admin”

= 1.0.13 =
Release Date: November 3rd, 2020
Minor fix to text description iframe

= 1.0.12 =
Release Date: November 2nd, 2020
Added support for (R) trademark and other symbols in plant names in ‘Joy of Plants > Import’
Updated readme.txt with instructions for use and screenshots

= 1.0.11 =
Release Date: November 2nd, 2020
Version approved by WordPress.org review on 27th October 2020