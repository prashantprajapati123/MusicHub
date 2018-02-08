<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.me/
 *
 * @package Videoblog
 */

/**
 * Add theme support for Infinite Scroll.
 * See: https://jetpack.me/support/infinite-scroll/
 */
function videoblog_jetpack_setup() {

    /*
     * JetPack Infinite Scroll
     */
    add_theme_support( 'infinite-scroll', array(
        'container' => 'recent-posts',
        'type' => 'click',
		'wrapper' => false,
		'render' => 'videoblog_infinite_scroll_render',
        'footer' => false,
    ) );

	add_image_size( 'videoblog-logo', 380, 100 );
	add_theme_support( 'site-logo', array(
			'header-text' => array(
            'site-title',
            'site-description'
        ),'size' => 'videoblog-logo' ) );

} // end function videoblog_jetpack_setup
add_action( 'after_setup_theme', 'videoblog_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function videoblog_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', get_post_format() );
	}
} // end function videoblog_infinite_scroll_render

function videoblog_jetpack_infinite_scroll_js_settings( $settings ) {
	$settings['text'] = __( 'Load more posts', 'videoblog' );
	return $settings;
}
add_filter( 'infinite_scroll_js_settings', 'videoblog_jetpack_infinite_scroll_js_settings' );

/**
 * Disable Infinite Scroll for the Menu CPT
 * @return bool
 */
function videoblog_infinite_scroll_supported() {
	return current_theme_supports( 'infinite-scroll' ) && ! is_tax( 'nova_menu' );
}
add_filter( 'infinite_scroll_archive_supported', 'videoblog_infinite_scroll_supported' );
