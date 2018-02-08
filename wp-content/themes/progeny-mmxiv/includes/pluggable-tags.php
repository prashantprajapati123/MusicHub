<?php
/**
 * Pluggable template tags from parent theme.
 *
 * @package Progeny_MMXIV
 * @since 1.1.0
 */

/**
 * Print a list of all site contributors who published at least one post.
 *
 * @since 1.0.0
 */
function twentyfourteen_list_authors() {
	$contributor_ids = get_post_meta( get_the_ID(), 'member_ids', true );
	$contributor_ids = array_filter( wp_parse_id_list( $contributor_ids ) );

	if ( empty( $contributor_ids ) ) {
		$contributor_ids = get_users( array(
			'fields'  => 'ID',
			'orderby' => 'post_count',
			'order'   => 'DESC',
			'who'     => 'authors',
		) );
	}

	// Display page content before contributor list.
	progeny_contributor_page_content();

	foreach ( $contributor_ids as $contributor_id ) :
		$post_count = count_user_posts( $contributor_id );

		// Move on if user has not published a post (yet).
		if ( empty( $member_ids ) && ! $post_count ) {
			continue;
		}

		include( locate_template( 'templates/parts/content-contributor.php' ) );

	endforeach;
}
