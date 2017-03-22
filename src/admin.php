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

add_action( 'plugins_loaded', __NAMESPACE__ . '\plugin_admin_init', 12 );
/**
 * Load the Admin_Options.
 */
function plugin_admin_init() {

	return Admin_Options::get_instance();
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

/**
 * Renders an error notice when Gravity Forms is inactive.
 */
function activation_error_notice() {
	?>
    <div class="notice notice-error is-dismissible">
        <p>Error activating Gravity Forms Slideshow. Please activate the Gravity Forms plugin and try again.</p>
    </div>
	<?php
}

/**
 * Deactivates this plugin.
 */
function deactivate_self() {

    deactivate_plugins( plugin_basename( FJ_GRAVITYFORMS_SLIDESHOW_PLUGIN ) );
}