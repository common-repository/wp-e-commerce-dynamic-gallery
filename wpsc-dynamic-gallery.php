<?php
/*
Plugin Name: WP e-Commerce Dynamic Gallery PRO
Description: Bring your single product page images to life and create an awesome visual product image display with WP e-Commerce Dynamic Gallery PRO.
Version: 2.0.0
Author: a3rev Software
Author URI: https://a3rev.com/
Text Domain: wp-e-commerce-dynamic-gallery
Domain Path: /languages
License: GPLv2 or later
*/

/*
	WP e-Commerce Dynamic Gallery. Plugin for the WP e-Commerce plugin.
	Copyright © 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define('WPSC_DYNAMIC_GALLERY_FILE_PATH', dirname(__FILE__));
define('WPSC_DYNAMIC_GALLERY_DIR_NAME', basename(WPSC_DYNAMIC_GALLERY_FILE_PATH));
define('WPSC_DYNAMIC_GALLERY_FOLDER', dirname(plugin_basename(__FILE__)));
define('WPSC_DYNAMIC_GALLERY_NAME', plugin_basename(__FILE__));
define('WPSC_DYNAMIC_GALLERY_URL', untrailingslashit(plugins_url('/', __FILE__)));
define('WPSC_DYNAMIC_GALLERY_DIR', WP_PLUGIN_DIR . '/' . WPSC_DYNAMIC_GALLERY_FOLDER);
define('WPSC_DYNAMIC_GALLERY_CSS_URL', WPSC_DYNAMIC_GALLERY_URL . '/assets/css');
define('WPSC_DYNAMIC_GALLERY_IMAGES_URL', WPSC_DYNAMIC_GALLERY_URL . '/assets/images');
define('WPSC_DYNAMIC_GALLERY_JS_URL', WPSC_DYNAMIC_GALLERY_URL . '/assets/js');

/**
 * Load Localisation files.
 *
 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
 *
 * Locales found in:
 * 		- WP_LANG_DIR/wp-e-commerce-dynamic-gallery/wp-e-commerce-dynamic-gallery-LOCALE.mo
 * 	 	- WP_LANG_DIR/plugins/wp-e-commerce-dynamic-gallery-LOCALE.mo
 * 	 	- /wp-content/plugins/wp-e-commerce-dynamic-gallery/languages/wp-e-commerce-dynamic-gallery-LOCALE.mo (which if not found falls back to)
 */
function wpsc_dynamic_gallery_plugin_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-e-commerce-dynamic-gallery' );

	load_textdomain( 'wp-e-commerce-dynamic-gallery', WP_LANG_DIR . '/wp-e-commerce-dynamic-gallery/wp-e-commerce-dynamic-gallery-' . $locale . '.mo' );
	load_plugin_textdomain( 'wp-e-commerce-dynamic-gallery', false, WPSC_DYNAMIC_GALLERY_FOLDER.'/languages' );
}

include ('admin/admin-ui.php');
include ('admin/admin-interface.php');

include ('admin/admin-pages/dynamic-gallery-page.php');

include ('admin/admin-init.php');
include ('admin/less/sass.php');

include ('classes/class-wpsc-dynamic-gallery-functions.php');
include ('classes/class-wpsc-dynamic-gallery-variations.php');
include ('classes/class-wpsc-dynamic-gallery-preview.php');
include ('classes/class-wpsc-dynamic-gallery-hook-filter.php');
include ('classes/class-wpsc-dynamic-gallery-metaboxes.php');
include ('classes/class-wpsc-dynamic-gallery-display.php');

include ('admin/wpsc-dynamic-gallery-admin.php');

/**
 * Call when the plugin is activated
 */
register_activation_hook(__FILE__, 'wpsc_dynamic_gallery_install');

?>