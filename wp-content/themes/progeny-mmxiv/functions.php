<?php
/**
 * Theme functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package Progeny_MMXIV
 * @since 1.0.0
 */

/**
 * Load helper functions and libraries.
 */
require( get_stylesheet_directory() . '/includes/hooks.php' );
require( get_stylesheet_directory() . '/includes/pluggable-tags.php' );
require( get_stylesheet_directory() . '/includes/template-tags.php' );

/**
 * Load AudioTheme support or display a notice that it's needed.
 */
if ( function_exists( 'audiotheme_load' ) ) {
	require( get_stylesheet_directory() . '/includes/plugins/audiotheme.php' );
} else {
	require( get_stylesheet_directory() . '/includes/vendor/class-audiotheme-themenotice.php' );
	new Audiotheme_ThemeNotice();
}

/**
 * Set up theme defaults and register support for various WordPress features.
 *
 * @since 1.0.0
 */
function progeny_setup() {
	// Add support for translating strings in this theme.
	// @link http://codex.wordpress.org/Function_Reference/load_theme_textdomain
	load_child_theme_textdomain( 'progeny-mmxiv', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'progeny_setup' );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.1.0
 */
function progeny_enqueue_assets() {
	// Load parent stylesheet.
	// @link http://kovshenin.com/2014/child-themes-import/
	wp_enqueue_style( 'progeny-parent-theme', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'progeny_enqueue_assets' );






// costom sidebar function

// function my_custom_sidebar()
// {
// 	register_sidebar( array(
// 		'name'          => __( 'Vinay Sidebar', 'twentyfourteen' ),
// 		'id'            => 'vinay-sidebar',
// 		'description'   => __( 'Appears in the vinays custom place.', 'twentyfourteen' ),
// 		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
// 		'after_widget'  => '</aside>',
// 		'before_title'  => '<h1 class="widget-title">',
// 		'after_title'   => '</h1>',
// 	) );
// }



// add_action( 'widgets_init', 'my_custom_sidebar' );
// costom sidebar function