<?php
/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 30/6/15
 * Time: 4:17 PM
 */

//Don't proceed any further if it ain't called by uninstall
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Get all posts using page builder
$pootlepb_unin_args = array(
	'post_type'  => get_post_types(),
	'meta_query' => array(
		array(
			'key'     => 'panels_data',
			'compare' => 'EXISTS',
		),
	)
);

//Run the query with args
$pootlepb_unin_query = new WP_Query( $pootlepb_unin_args );

//Loop through the posts
foreach ( $pootlepb_unin_query->posts as $post ) {

	//Delete page builder data
	delete_post_meta( $post->ID, 'panels_data' );

}

//Remove the options we used
delete_option( 'pootlepb_add_ons' );
delete_option( 'siteorigin_panels_display' );

//Removing usermeta for visit count to new page
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'pootlepb_visit_count';" );