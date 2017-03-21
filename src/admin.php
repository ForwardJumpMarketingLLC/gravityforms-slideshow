<?php
/**
 * WP Admin functions
 *
 * Description
 *
 * @package     ForwardJump\GravityFormsSlideshow
 * @since       1.0.0
 * @author      Tim Jensen
 * @link        https://www.timjensen.us
 * @license     GNU General Public License 2.0+
 */
namespace ForwardJump\GravityFormsSlideshow\Admin;

use \ForwardJump\GravityFormsSlideshow\OptionsPage\Admin_Options;

add_action( 'plugins_loaded', __NAMESPACE__ . '\plugin_init' );
/**
 * Load get the Admin_Options object if Gravity Forms is active
 */
function plugin_init() {
	if ( class_exists( 'GFFormsModel' ) ) {
		return Admin_Options::get_instance();
	}
}

/**
 * Get the GF fields from the selected form
 *
 * @param $field    CMB2 field
 *
 * @return array|bool
 */
function get_form_fields( $field ) {

	$forms = cmb2_get_option( 'gf_slideshow_options', '_gf_image_upload_form' );

	$form_id = $forms[ $field->group->index ]['form_id'];

	if ( 'null' == $form_id || null == $form_id ) {
		return false;
	}

	$form = \GFFormsModel::get_form_meta( $form_id );

	$fields_list = [ 'null' => 'None selected' ];
	foreach ( $form['fields'] as $field ) {
		$fields_list[ $field->id ] = $field->label;
	}

	return $fields_list;
}

add_action( 'wp_ajax_gf_slideshow_get_gform_fields', __NAMESPACE__ . '\gf_slideshow_ajax_get_gform_fields' );
/**
 * Gets an array of fields for the selected Gravity Form to pass to the AJAX request.
 */
function gf_slideshow_ajax_get_gform_fields() {

	$form_id = intval( $_POST['formID'] );

	$fields = \GFFormsModel::get_form_meta( $form_id );

	$options = [ 'null' => 'None selected' ];
	foreach ( $fields['fields'] as $field ) {
		$options[ $field['id'] ] = $field['label'];
	}

	echo json_encode( $options );

	wp_die();
}

add_action( 'cmb2_admin_init', __NAMESPACE__ . '\register_attachments_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function register_attachments_metabox() {
	$prefix = '_gf_slideshow_';

	$cmb_demo = new_cmb2_box( array(
		'id'           => $prefix . 'attachment_approval_metabox',
		'title'        => esc_html__( 'Approve user upload', 'gf-slideshow' ),
		'object_types' => array( 'attachment', ), // Post type
		'show_on_cb'   => __NAMESPACE__ . '\is_gravityform_upload', // function should return a bool value
		'context'      => 'side',
		'priority'     => 'low',
		'show_names'   => false, // Show field names on the left
		'cmb_styles'   => false, // false to disable the CMB stylesheet
	) );

	$cmb_demo->add_field( array(
		'name'    => esc_html__( 'Test Radio inline', 'gf-slideshow' ),
		'desc'    => esc_html__( 'Approve this image for display in slideshows.', 'gf-slideshow' ),
		'id'      => $prefix . 'is_image_approved',
		'type'    => 'radio_inline',
		'options' => array(
			'1' => esc_html__( 'Yes', 'gf-slideshow' ),
			'0' => esc_html__( 'No', 'gf-slideshow' ),
		),
	) );
}

/**
 * Checks if the post (attachment) came from a Gravity Forms upload
 *
 * @return bool
 */
function is_gravityform_upload() {
	$gf_upload = get_post_meta( get_the_ID(), '_gf_slideshow_form_source', true );

	return ! empty( $gf_upload );
}