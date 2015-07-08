<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 29/6/15
 * Time: 10:48 PM
 * @since 0.1.0
 */

/**
 * Store the Page Builder meta in the revision.
 *
 * @param $post_id
 * @param $post
 * @since 0.1.0
 */
function pootlepb_revisions_save_post( $post_id, $post ) {
	$parent_id = wp_is_post_revision( $post_id );

	if ( $parent_id ) {
		// If the panels data meta exists, copy it into the revision.
		$panels_data = get_post_meta( $parent_id, 'panels_data', true );
		if ( ! empty( $panels_data ) ) {
			add_metadata( 'post', $post_id, 'panels_data', $panels_data );
		}
	}

}

add_action( 'save_post', 'pootlepb_revisions_save_post', 11, 2 );

/**
 * Restore a revision.
 *
 * @param $post_id
 * @param $revision_id
 * @since 0.1.0
 */
function pootlepb_revisions_restore( $post_id, $revision_id ) {
	$panels_data = get_metadata( 'post', $revision_id, 'panels_data', true );
	if ( ! empty( $panels_data ) ) {
		update_post_meta( $post_id, 'panels_data', $panels_data );
	} else {
		delete_post_meta( $post_id, 'panels_data' );
	}
}

add_action( 'wp_restore_post_revision', 'pootlepb_revisions_restore', 10, 2 );

/**
 * Add the Page Builder content revision field.
 *
 * @param $fields
 *
 * @return mixed
 * @since 0.1.0
 */
function pootlepb_revisions_fields( $fields ) {
	// Prevent the autosave message.
	// TODO figure out how to include Page Builder data into the autosave.
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $fields;
	}

	$screen = get_current_screen();
	if ( ! empty( $screen ) && $screen->base == 'post' ) {
		return $fields;
	}

	$fields['grids'] = __( 'Page Builder', 'ppb-panels' );

	return $fields;
}

add_filter( '_wp_post_revision_fields', 'pootlepb_revisions_fields' );

/**
 * Display the Page Builder content for the revision.
 *
 * @param $value
 * @param $field
 * @param $revision
 *
 * @return string
 * @since 0.1.0
 */
function pootlepb_revisions_field( $value, $field, $revision ) {
	$parent_id   = wp_is_post_revision( $revision->ID );
	$panels_data = get_metadata( 'post', $revision->ID, 'panels_data', true );

	if ( empty( $panels_data ) ) {
		return '';
	}

	return Pootle_Page_Builder_Render_Layout::instance()->panels_render( $parent_id, false, $panels_data );
}

add_filter( '_wp_post_revision_field_panels_data_field', 'pootlepb_revisions_field', 10, 3 );
