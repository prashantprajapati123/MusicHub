<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Template Loader
 *
 */
class TZ_Music_Press_Loader {

    /**
     * Hook in methods.
     */
    public function __construct() {
        add_filter( 'template_include', array( __CLASS__, 'tz_music_press_template_loader' ) );
    }

    static function tz_music_press_template_loader( $template ) {

        $find = array( 'music-press.php' );
        $file = '';
        if ( is_single() && get_post_type() == 'music' ) {

            $file 	= 'single-music.php';
            $find[] = $file;
            $find[] =  TZ_Music_Press::tz_music_template_path() . $file;

        } elseif ( tz_is_music_taxonomy() ) {

            $term   = get_queried_object();

            if ( is_tax( 'album' ) || is_tax( 'artist' ) ) {
                $file = 'taxonomy-' . $term->taxonomy . '.php';
            } else {
                $file = 'archive-music.php';
            }
            $find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = TZ_Music_Press::tz_music_template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
            $find[] = 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = TZ_Music_Press::tz_music_template_path() . 'taxonomy-' . $term->taxonomy . '.php';
            $find[] = $file;
            $find[] = TZ_Music_Press::tz_music_template_path() . $file;

        } elseif ( is_post_type_archive( 'music' )) {

            $file 	= 'archive-music.php';
            $find[] = $file;
            $find[] = TZ_Music_Press::tz_music_template_path() . $file;

        }

        if ( $file ) {
            $template       = locate_template( array_unique( $find ) );
            if ( ! $template ) {
                $template = TZ_MUSIC_PRESS_PLUGIN_DIR . '/templates/' . $file;
            }
        }
        return $template;
    }

}

if ( ! function_exists( 'tz_is_music_taxonomy' ) ) {

    function tz_is_music_taxonomy() {
        return is_tax( get_object_taxonomies( 'music' ) );
    }
}
