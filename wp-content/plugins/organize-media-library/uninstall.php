<?php
/**
 * Uninstall
 *
 * @package Organize Media Library by Folders
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;
$option_names = array();
$wp_options = $wpdb->get_results(
	"
		SELECT option_name
		FROM $wpdb->options
		WHERE option_name LIKE '%%organizemedialibrary_settings%%'
	"
);
foreach ( $wp_options as $wp_option ) {
	$option_names[] = $wp_option->option_name;
}

$wp_terms = $wpdb->get_results(
	"
		SELECT term_id
		FROM $wpdb->term_taxonomy
		WHERE taxonomy = 'oml_folders'
	"
);
$termids = array();
foreach ( $wp_terms as $value ) {
	$termids[] = $value->term_id;
}

$option_name2 = 'organizemedialibrary';
$option_name3 = 'oml_notice';

/* For Single site */
if ( ! is_multisite() ) {
	if ( ! empty( $option_names ) ) {
		foreach ( $option_names as $option_name ) {
			delete_option( $option_name );
		}
	}
	delete_option( 'oml_dirs' );
	$blogusers = get_users( array( 'fields' => array( 'ID' ) ) );
	foreach ( $blogusers as $user ) {
		delete_user_option( $user->ID, $option_name2, false );
		delete_user_option( $user->ID, $option_name3, false );
	}
	if ( ! empty( $termids ) ) {
		foreach ( $termids as $termid ) {
			$where_format = array( '%d' );
			$delete_line = array( 'term_id' => $termid );
			$delete_line_relationships = array( 'term_taxonomy_id' => $termid );
			$wpdb->delete( $wpdb->terms, $delete_line, $where_format );
			$wpdb->delete( $wpdb->term_taxonomy, $delete_line, $where_format );
			$wpdb->delete( $wpdb->term_relationships, $delete_line_relationships, $where_format );
		}
	}
} else {
	/* For Multisite */
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ( $blog_ids as $blogid ) {
		switch_to_blog( $blogid );
		if ( ! empty( $option_names ) ) {
			foreach ( $option_names as $option_name ) {
				delete_option( $option_name );
			}
		}
		delete_option( 'oml_dirs' );
		$blogusers = get_users(
			array(
				'blog_id' => $blogid,
				'fields' => array( 'ID' ),
			)
		);
		foreach ( $blogusers as $user ) {
			delete_user_option( $user->ID, $option_name2, false );
			delete_user_option( $user->ID, $option_name3, false );
		}
		if ( ! empty( $termids ) ) {
			foreach ( $termids as $termid ) {
				$where_format = array( '%d' );
				$delete_line = array( 'term_id' => $termid );
				$delete_line_relationships = array( 'term_taxonomy_id' => $termid );
				$wpdb->delete( $wpdb->terms, $delete_line, $where_format );
				$wpdb->delete( $wpdb->term_taxonomy, $delete_line, $where_format );
				$wpdb->delete( $wpdb->term_relationships, $delete_line_relationships, $where_format );
			}
		}
	}
	switch_to_blog( $original_blog_id );

	/* For site options. */
	if ( ! empty( $option_names ) ) {
		foreach ( $option_names as $option_name ) {
			delete_site_option( $option_name );
		}
	}
	delete_site_option( 'oml_dirs' );

}


