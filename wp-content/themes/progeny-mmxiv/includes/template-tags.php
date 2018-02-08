<?php
/**
 * Custom template tags.
 *
 * @package Progeny_MMXIV
 * @since 1.0.0
 */

/**
 * Retrieve the title for an archive.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Optional. Post to get the archive title for. Defaults to the current post.
 * @return string
 */
function progeny_get_archive_title( $post = null, $singular = false ) {
	$post = get_post( $post );

	if ( $singular ) {
		$title = get_post_type_object( $post->post_type )->labels->singular_name;
	} else {
		$title = get_post_type_object( $post->post_type )->label;
	}

	return $title;
}

/**
 * Print archive link.
 *
 * @since 1.0.0
 */
function progeny_archive_link() {
	$post_type = get_post_type();
	$link      = get_post_type_archive_link( $post_type );
	$title     = progeny_get_archive_title();

	if ( 'audiotheme_track' == $post_type ) {
		$link = get_permalink( get_post()->post_parent );
	}

	printf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $link ),
		progeny_allowed_tags( $title )
	);
}

/**
 * Display page content on contributor page template.
 *
 * @since 1.0.0
 */
function progeny_contributor_page_content() {
	if ( ! progeny_has_content() ) {
		return;
	}
	?>
	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'progeny-mmixv' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		) );
		?>
	</div>
	<?php
}

/**
 * Determine if a post has content.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Defaults to the current global post.
 * @return bool
 */
function progeny_has_content( $post_id = null ) {
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;
	$content = get_post_field( 'post_content', $post_id );
	return empty( $content ) ? false : true;
}

if ( ! function_exists( 'progeny_allowed_tags' ) ) :
/**
 * Allow only the allowedtags array in a string.
 *
 * @since 1.0.0
 *
 * @link https://www.tollmanz.com/wp-kses-performance/
 *
 * @param  string $string The unsanitized string.
 * @return string         The sanitized string.
 */
function progeny_allowed_tags( $string ) {
	global $allowedtags;

	$theme_tags = array(
		'a' => array(
			'class' => true,
			'href' => true,
			'rel' => true,
			'title' => true,
		),
		'br' => array(),
		'h2' => array(
			'class' => true,
		),
		'p' => array(
			'class' => true,
		),
		'span' => array(
			'class' => true,
		),
		'time' => array(
			'class' => true,
			'datetime' => true,
		),
	);

	return wp_kses( $string, array_merge( $allowedtags, $theme_tags ) );
}
endif;
