<?php
/**
 * AudioTheme Compatibility File
 *
 * @package Progeny_MMXIV
 * @since 1.0.0
 * @link https://audiotheme.com/
 */

/**
 * Set up theme defaults and register support for various AudioTheme features.
 *
 * @since 1.1.0
 */
function progeny_audiotheme_setup() {
	// Add support for AudioTheme widgets.
	add_theme_support( 'audiotheme-widgets', array(
		'record',
		'track',
		'upcoming-gigs',
		'video',
	) );

	add_image_size( 'record-thumbnail', 672, 672, true );
	add_image_size( 'video-thumbnail', 672, 378, true );
}
add_action( 'after_setup_theme', 'progeny_audiotheme_setup', 11 );

/**
 * Add additional HTML classes to posts.
 *
 * @since 1.1.0
 *
 * @param array $classes List of HTML class names.
 * @param string $class One or more classes added to the class list.
 * @param int $post_id The post ID.
 * @return array
 */
function progeny_audiotheme_post_class( $classes, $class, $post_id ) {
	$post = get_post( $post_id );

	if ( 'audiotheme_track' == $post->post_type && ( has_post_thumbnail( $post_id ) || has_post_thumbnail( $post->post_parent ) ) ) {
		$classes[] = 'has-post-thumbnail';
	}

	return array_unique( $classes );
}
add_filter( 'post_class', 'progeny_audiotheme_post_class', 10, 3 );


/*
 * Admin hooks.
 * -----------------------------------------------------------------------------
 */

/**
 * Add Tags metabox on AudioTheme post types. This allows theme to be included
 * in the featured content section.
 *
 * @since 1.1.0
 */
function progeny_audiotheme_admin_init() {
	register_taxonomy_for_object_type( 'post_tag', 'audiotheme_record' );
}
add_action( 'admin_init', 'progeny_audiotheme_admin_init' );


/*
 * AudioTheme hooks.
 * -----------------------------------------------------------------------------
 */

/**
 * HTML to display before main AudioTheme content.
 *
 * @since 1.1.0
 */
function progeny_audiotheme_before_main_content() {
	echo '<div id="main-content" class="main-content">';
	echo '<div id="primary" class="content-area">';
	echo '<div id="content" class="site-content" role="main">';
}
add_action( 'audiotheme_before_main_content', 'progeny_audiotheme_before_main_content' );

/**
 * HTML to display after main AudioTheme content.
 *
 * @since 1.1.0
 */
function progeny_audiotheme_after_main_content() {
	echo '</div><!-- #content -->';
	echo '</div><!-- #primary -->';
	echo '</div><!-- #main-content -->';
	get_sidebar( 'content' );
	get_sidebar();
}
add_action( 'audiotheme_after_main_content', 'progeny_audiotheme_after_main_content' );

/**
 * Adjust AudioTheme widget image sizes.
 *
 * @since 1.1.0
 *
 * @param array $size Image size (width and height).
 * @return array
 */
function progeny_audiotheme_widget_image_size( $size ) {
	return array( 612, 612 ); // sidebar width x 2
}
add_filter( 'audiotheme_widget_record_image_size', 'progeny_audiotheme_widget_image_size' );
add_filter( 'audiotheme_widget_track_image_size', 'progeny_audiotheme_widget_image_size' );
add_filter( 'audiotheme_widget_video_image_size', 'progeny_audiotheme_widget_image_size' );

/**
 * Activate default archive setting fields.
 *
 * @since 1.1.0
 *
 * @param array $fields List of default fields to activate.
 * @param string $post_type Post type archive.
 * @return array
 */
function progeny_audiotheme_archive_settings_fields( $fields, $post_type ) {
	if ( ! in_array( $post_type, array( 'audiotheme_record', 'audiotheme_video' ) ) ) {
		return $fields;
	}

	$fields['columns'] = array(
		'choices' => range( 1, 2 ),
		'default' => 2,
	);

	$fields['posts_per_archive_page'] = true;

	return $fields;
}
add_filter( 'audiotheme_archive_settings_fields', 'progeny_audiotheme_archive_settings_fields', 10, 2 );


/*
 * Parent theme hooks.
 * -----------------------------------------------------------------------------
 */

/**
 * Add AudioTheme Post Types to featured posts query.
 *
 * @since 1.1.0
 *
 * @param array $posts List of featured posts.
 * @return array
 */
function progeny_audiotheme_get_featured_posts( $posts ) {
	if ( ! class_exists( 'Featured_Content' ) ) {
		return;
	}

	$tag_id = Featured_Content::get_setting( 'tag-id' );

	// Return early if a tag id hasn't been set.
	if ( empty( $tag_id ) ) {
		return $posts;
	}

	// Query for featured posts.
	$featured = get_posts( array(
		'post_type' => array( 'audiotheme_record', 'audiotheme_video' ),
		'tax_query' => array(
			array(
				'field'    => 'term_id',
				'taxonomy' => 'post_tag',
				'terms'    => $tag_id,
			),
		),
	) );

	return array_merge( $posts, $featured );
}
add_filter( 'twentyfourteen_get_featured_posts', 'progeny_audiotheme_get_featured_posts', 20 );
