<?php
/**
 * Shortcodes
 *
 * This file is for adding shortcodes
 *
 * @package      ForwardJump Utility
 * @since        1.0.0
 */

namespace ForwardJump\GravityFormsSlideshow\Shortcodes;

add_shortcode( 'gf-slideshow', __NAMESPACE__ . '\image_slider' );
/**
 * Adds image slider/lightbox of images uploaded using the specified Gravity Form
 *
 * @param $atts
 *
 * @return string
 */
function image_slider( $atts ) {

	$atts = shortcode_atts(
		array(
			'gform_id'        => null,
			'image_size'      => 'large',
			'max_slides'      => - 1,
			'show_unapproved' => false,
			'show_captions'   => false,
			'slider_syncing'  => false,
		),
		$atts,
		'gf-slideshow' );

	if ( ! $atts['gform_id'] ) {
		return false;
	}

	$show_approved = $atts['show_unapproved'] ? array( 0, 1 ) : array( 1 );

	$args = array(
		'order'          => 'DESC',
		'orderby'        => 'ID',
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'posts_per_page' => esc_sql( $atts['max_slides'] ),
		'meta_query'     => array(
			array(
				'key'     => '_gf_slideshow_form_source',
				'value'   => esc_sql( $atts['gform_id'] ),
				'compare' => 'IN',
			),
			array(
				'key'     => '_gf_slideshow_is_image_approved',
				'value'   => $show_approved,
				'compare' => 'IN',
			),
		),
	);

	$attachments = ( new \WP_Query( $args ) )->posts;

	// Return early if there are no images
	if ( empty( $attachments ) ) {
		return false;
	}

	do_action( 'gf_slideshow_enqueue_scripts' );

	ob_start();

	do_slider_slides(
		$attachments,
		$atts['image_size'],
		filter_var( $atts['show_captions'], FILTER_VALIDATE_BOOLEAN ),
		filter_var( $atts['slider_syncing'], FILTER_VALIDATE_BOOLEAN )
	);

	return ob_get_clean();
}

/**
 * Builds the slides for the slider.
 *
 * @param array  $attachments    Array of attachment post objects
 * @param string $image_size     The size image to show in the slideshow.  Must be a registered image size.
 * @param bool   $show_captions  Whether or not the slides should include captions.
 * @param bool   $slider_syncing Display the slider in Slider Syncing format.
 *
 * @see http://kenwheeler.github.io/slick/
 */
function do_slider_slides( $attachments, $image_size, $show_captions, $slider_syncing ) {

	$view = $slider_syncing ? 'slider_syncing.php' : 'slides.php';

	if ( $theme_view_file = locate_template( "gf-slideshow/{$view}" ) ) {
		include $theme_view_file;
	} else {
		include FJ_GRAVITYFORMS_SLIDESHOW_DIR . "views/{$view}";
	}
}
