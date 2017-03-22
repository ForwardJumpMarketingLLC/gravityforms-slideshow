<?php
/**
 * Plugin Name:       Gravity Forms Slideshow
 * Description:       Allows users to contribute to a slideshow by uploading pictures through Gravity Forms form. User uploaded pictures are added to the Media Library and can be used just like any other media file.
 *
 * Plugin URI:        https://github.com/ForwardJumpMarketingLLC/GravityForms-slideshow
 * GitHub Plugin URI: https://github.com/ForwardJumpMarketingLLC/GravityForms-slideshow
 * GitHub Branch:     master
 * Author:            ForwardJump Marketing, LLC
 * Author URI:        https://forwardjump.com
 * Text Domain:       gf-slideshow
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:           1.2.1
 */

namespace ForwardJump\GravityFormsSlideshow;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_PLUGIN' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_PLUGIN', __FILE__ );
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_DIR' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_URL' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_VERSION' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_VERSION', '1.2.1' );
}

if ( file_exists( FJ_GRAVITYFORMS_SLIDESHOW_DIR . 'vendor/CMB2/init.php' ) ) {
	require_once FJ_GRAVITYFORMS_SLIDESHOW_DIR . 'vendor/CMB2/init.php';
}

/**
 * Autoloads plugin files
 */
function autoload() {
	$files = array(
		'enqueue-assets',
		'class-admin-options',
		'admin',
		'image-upload-handler',
		'shortcodes',
	);

	foreach ( $files as $file ) {
		include_once FJ_GRAVITYFORMS_SLIDESHOW_DIR . 'src/' . $file . '.php';
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\plugin_init' );
/**
 * Load plugin files if Gravity Forms is active.  Deactivates with an admin warning if Gravity Forms is not active.
 *
 * @return void
 */
function plugin_init() {

	if ( ! class_exists( 'GFForms' ) ) {

		include_once FJ_GRAVITYFORMS_SLIDESHOW_DIR . 'src/admin.php';

		add_action( 'admin_notices', __NAMESPACE__ . '\Admin\activation_error_notice' );

		add_action( 'admin_init', __NAMESPACE__ . '\Admin\deactivate_self' );

		return;
	}

	autoload();
}
