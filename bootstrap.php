<?php
/**
 * Plugin Name:       Gravity Forms Slideshow
 * Description:       Allows users to contribute to a slideshow by uploading pictures through Gravity Forms form. User uploaded pictures are added to the Media Library and can be used just like any other media file.
 *
 * GitHub Plugin URI: https://github.com/ForwardJumpMarketingLLC/GravityForms-slideshow
 * GitHub Branch:     master
 * Author:            Tim Jensen
 * Author URI:        https://forwardjump.com
 * Text Domain:       gf-slideshow
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:           1.2.0
 */

namespace ForwardJump\GravityFormsSlideshow;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_DIR' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_URL' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'FJ_GRAVITYFORMS_SLIDESHOW_VERSION' ) ) {
	define( 'FJ_GRAVITYFORMS_SLIDESHOW_VERSION', '1.0.1' );
}

if ( file_exists( FJ_GRAVITYFORMS_SLIDESHOW_DIR . 'vendor/CMB2/init.php' ) ) {
	require_once FJ_GRAVITYFORMS_SLIDESHOW_DIR . 'vendor/CMB2/init.php';
}

/**
 * Loads plugin files
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

autoload();
