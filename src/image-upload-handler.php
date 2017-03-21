<?php
/**
 * This file adds the GF image upload slideshow functionality
 *
 * @package      ForwardJump Utility
 * @since        1.0.0
 */

namespace ForwardJump\GravityFormsSlideshow\ImageUploadHandler;

add_action( 'gform_entry_created', __NAMESPACE__ . '\gform_entry_created', 10, 2 );
/**
 * Converts image upload into an item in the Media Library with alt tag and caption (optional).
 *
 * @param array $entry Gravity Forms entry data
 * @param array $form  Gravity Forms form data
 */
function gform_entry_created( $entry, $form ) {

	$gf_image_upload_forms = prepare_options();

	if ( empty( $gf_image_upload_forms[ $form['id'] ] ) ) {
		return;
	}

	$image_field_id   = $gf_image_upload_forms[ $form['id'] ]['image_fields'];
	$caption_field_id = $gf_image_upload_forms[ $form['id'] ]['caption_field'];

	// Get the array of URLs from a multi-file upload field.
	$uploaded_images = json_decode( $entry[ $image_field_id ] );

	// Convert URL from a simple file upload field into an array.
	if ( is_null( $uploaded_images ) ) {
		$uploaded_images = array( $entry[ $image_field_id ] );
	}

	$caption = apply_filters( 'gf_slideshow_caption', $entry[ $caption_field_id ] );

	$attachment_edit_links = '';
	foreach ( $uploaded_images as $file_url ) {
		$attach_id = add_upload_to_media_library( $file_url, $caption, $form );

		if ( ! $attach_id ) {
			continue;
		}

		$image_edit_link = get_image_edit_link( $attach_id );

		$attachment_edit_links .= '<br>' . $image_edit_link;
	}

	add_filter( 'gform_replace_merge_tags', function ( $text, $form, $entry ) use ( $attachment_edit_links ) {
		$merge_tag = '{image_edit_links}';

		return str_replace( $merge_tag, $attachment_edit_links, $text );
	}, 10, 3 );
}

/**
 * Adds uploaded image to the WP Media Library
 *
 * @param string $file_url User uploaded image file
 * @param string $caption  User defined caption
 * @param array  $form     Gravity Forms form data
 *
 * @return string|bool
 */
function add_upload_to_media_library( $file_url, $caption, $form ) {
	if ( ! $file_url ) {
		return false;
	}

	$file = pathinfo( $file_url );

	// Get the path to the upload directory.
	$wp_upload_dir = wp_upload_dir();

	$file_upload_path = str_replace( $wp_upload_dir['baseurl'], $wp_upload_dir['basedir'], $file_url );

	// Check the type of file. We'll use this as the 'post_mime_type'.
	$filetype = wp_check_filetype( basename( $file_upload_path ), null );

	$file_handle_upload = [
		'name'     => $file['basename'],
		'type'     => $filetype['type'],
		'tmp_name' => $file_upload_path,
	];

	// Required for wp_handle_upload() and wp_generate_attachment_metadata().
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	$wp_handle_upload = wp_handle_upload( $file_handle_upload, array(
		'action'    => 'gf_upload',
		'test_form' => false,
		'test_size' => false,
	) );

	// Prepare an array of post data for the attachment.
	$attachment = array(
		'guid'           => $wp_handle_upload['url'],
		'post_mime_type' => $wp_handle_upload['type'],
		'post_title'     => sanitize_title( $file['filename'] ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Insert the attachment.
	$attach_id = wp_insert_attachment( $attachment, $wp_handle_upload['file'] );

	// Generate the metadata for the attachment, and update the database record.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_handle_upload['file'] );

	wp_update_attachment_metadata( $attach_id, $attach_data );

	$alt_text_prefix = apply_filters( 'gf_slideshow_alt_text_prefix', 'User upload: ' );
	$alt_text = $alt_text_prefix . $caption;

	// Adds image alt text.
	update_post_meta( $attach_id, '_wp_attachment_image_alt', esc_html( $alt_text ) );

	// Mark attachment as being a GF upload.
	update_post_meta( $attach_id, '_gf_slideshow_form_source', $form['id'] );

	// Mark attachment as being a not yet approved.
	update_post_meta( $attach_id, '_gf_slideshow_is_image_approved', '0' );

	// Adds image caption.
	if ( $caption ) {
		wp_update_post( array(
			'ID'           => $attach_id,
			'post_excerpt' => esc_html( $caption ),
		) );
	}

	return $attach_id;
}

/**
 * Returns a multidimensional array of form IDs with their respective fields.
 *
 * @return array $gf_slideshow_forms_config
 */
function prepare_options() {

	$gf_slideshow_options = get_option( 'gf_slideshow_options' );

	$gf_slideshow_forms_config = $gf_slideshow_options['_gf_image_upload_form'];

	foreach ( $gf_slideshow_forms_config as $key => $form ) {

		$form_id = $form['form_id'];
		unset( $form['form_id'] );

		$gf_slideshow_forms_config[ $form_id ] = $form;

		unset( $gf_slideshow_forms_config[ $key ] );
	}

	return $gf_slideshow_forms_config;
}

/**
 * Returns the image edit URL
 *
 * @param int $attach_id Attachment ID
 *
 * @return string|bool
 */
function get_image_edit_link( $attach_id ) {

	if ( ! $post = get_post( $attach_id ) ) {
		return false;
	}

	$post_type_object = get_post_type_object( $post->post_type );

	if ( ! $post_type_object ) {
		return false;
	}

	if ( $post_type_object->_edit_link ) {
		return admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $post->ID ) );
	}
}
