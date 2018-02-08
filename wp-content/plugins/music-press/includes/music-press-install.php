<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Music_Press_Install
 */
class Music_Press_Install {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->music_press_default_terms();
	}

	/**
	 * default_terms function.
	 *
	 * @access public
	 * @return void
	 */
	public function music_press_default_terms() {

		if ( get_option( 'music_press_installed_terms' ) == 1 )
			return;

		$taxonomies = array(
			'genre' => array(
				__( 'Rock', 'music-press' ),
				__( 'Electronic', 'music-press' ),
				__( 'Guitar', 'music-press' ),
			),
			'album' => array(
				__( 'Let me love you', 'music-press' ),
				__( 'Sing Me To Sleep', 'music-press' ),
			),
			'artist' => array(
				__( 'Alan Walker ', 'music-press' ),
				__( 'Selena Gomez', 'music-press' ),
			),
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
		$terms_1 = get_term_by('name','Let me love you','album');
		$terms_2 = get_term_by('name','Sing Me To Sleep','album');

		update_option('tz_album_type' . $terms_1->term_id, 'audio');
		update_option('tz_album_type' . $terms_2->term_id, 'audio');
		update_option('permalink_structure', '/%postname%/');

		update_option( 'music_press_installed_terms', 1 );
	}
}

new Music_Press_Install();