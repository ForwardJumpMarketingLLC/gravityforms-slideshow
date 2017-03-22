<?php
/**
 * Enqueues assets
 *
 * @package     ForwardJump\GravityFormsSlideshow
 * @since       1.0.0
 * @author      Tim Jensen
 * @license     GNU General Public License 2.0+
 */
namespace ForwardJump\GravityFormsSlideshow\Enqueue;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_styles' );
/**
 * Enqueue our styles. These load on every page.
 */
function enqueue_styles() {

	wp_enqueue_style( 'slick-theme-css', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick-theme.css' );

	wp_enqueue_style( 'gf-slideshow-styles', FJ_GRAVITYFORMS_SLIDESHOW_URL . 'assets/css/gf-slideshow-styles.min.css', array(), FJ_GRAVITYFORMS_SLIDESHOW_VERSION );
	
}

add_action( 'gf_slideshow_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts' );
/**
 * Enqueues our scripts.  These load only on pages where the shortcode exists.
 */
function enqueue_scripts() {

	wp_enqueue_script( 'slick-js' );

	wp_enqueue_script( 'slick-lightbox-js' );

	wp_enqueue_script( 'gf-slideshow-scripts' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_assets' );
/**
 * Registers our assets.  These are enqueued when the shortcode [gf-slideshow] runs.
 */
function register_assets() {

	wp_register_script( 'gf-slideshow-scripts', FJ_GRAVITYFORMS_SLIDESHOW_URL . 'assets/js/gf-slideshow-scripts.min.js', array( 'jquery' ), FJ_GRAVITYFORMS_SLIDESHOW_VERSION, true );

	wp_register_script( 'slick-js', '//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js', array( 'jquery' ), null, true );

	wp_register_script( 'slick-lightbox-js', FJ_GRAVITYFORMS_SLIDESHOW_URL . 'assets/js/slick-lightbox.min.js', array( 'jquery' ), FJ_GRAVITYFORMS_SLIDESHOW_VERSION, true );
}