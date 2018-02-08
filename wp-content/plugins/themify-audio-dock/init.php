<?php
/*
Plugin Name:  Themify Audio Dock
Plugin URI:   http://themify.me/
Version:      1.0.5
Author:       Themify
Description:  An slick and simple sticky music player.
Text Domain:  themify-audio-dock
Domain Path:  /languages
*/

if ( !defined( 'ABSPATH' ) ) exit;

class Themify_Player {

	var $url;
	var $dir;
	var $version;

	/* Themify_Playlist members */
	protected $type     = '';
	protected $types    = array( 'audio', 'video' );
	protected $instance = 0;

	function __construct() {
		$data = get_file_data( __FILE__, array( 'Version' ) );
		$this->version = $data[0];
		$this->url = trailingslashit( plugin_dir_url( __FILE__ ) );
		$this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );

		add_action( 'plugins_loaded', array( $this, 'i18n' ), 5 );
		add_action( 'template_redirect', array( $this, 'frontend_display' ) );

		add_shortcode( 'themify_playlist', array( $this, 'playlist_shortcode' ) );
		add_shortcode( 'themify_trac',     array( $this, 'trac_shortcode'     ) );

		if( is_admin() ) {
			include $this->dir . 'includes/admin.php';
		}
	}

	public function i18n() {
		load_plugin_textdomain( 'themify-audio-dock', false, '/languages' );
	}

	function frontend_display() {
		$playlist = $this->get_playlist();
		if( ! empty( $playlist ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'wp_footer', array( $this, 'render' ) );
		}
	}

	function enqueue() {
		wp_playlist_scripts( 'audio' );

		wp_enqueue_style( 'themify-audio-dock', $this->url . 'assets/styles.css' );
		$bar_color = get_option( 'themify_audio_dock_bar_color' );
		$track_color = get_option( 'themify_audio_dock_track_color' );
		if( $bar_color ) {
			wp_add_inline_style( 'themify-audio-dock', sprintf( '
			#themify-audio-dock,
			#themify-audio-dock.collapsed .themify-audio-dock-inner .button-switch-player {
				background-color: %s;
			}
			', $bar_color ) );
		}
		if( $track_color ) {
			wp_add_inline_style( 'themify-audio-dock', sprintf( '
			#themify-audio-dock .tracklist .wp-playlist-themify .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,
			#themify-audio-dock .mejs-container .mejs-controls .mejs-time-rail .mejs-time-current {
				background-color: %s;
			}
			', $track_color ) );
		}
		wp_enqueue_script( 'themify-audio-dock', $this->url . 'assets/scripts.js', array( 'jquery' ), $this->version, true );
	}

	function render() {
		include( $this->dir . 'includes/template.php' );
	}

	public function get_playlist() {
		$playlist = get_option( 'themify_audio_dock_playlist', array() );
		return $playlist;
	}

	/**
	 * Callback for the [themify_playlist] shortcode
	 */
	public function playlist_shortcode( $atts = array(), $content = '' ) {
		$this->instance++;
		$atts = shortcode_atts(
			array(
				'type'          => 'audio',
				'style'         => 'light',
				'tracklist'     => 'true',
				'tracknumbers'  => 'true',
				'images'        => 'true',
				'artists'       => 'true',
				'current'       => 'true',
				'loop'          => 'false',
				'autoplay'      => 'false',
				'preload'       => 'metadata', // none, auto
				'id'            => '',
				'width'         => '',
				'height'        => '',
			), $atts, 'themify_playlist_shortcode' );

		//----------
		// Input
	    //----------
		$atts['id']           = esc_attr( $atts['id'] );
		$atts['type']         = esc_attr( $atts['type'] );
		$atts['style']        = esc_attr( $atts['style'] );
		$atts['tracklist']    = filter_var( $atts['tracklist'], FILTER_VALIDATE_BOOLEAN );
		$atts['tracknumbers'] = filter_var( $atts['tracknumbers'], FILTER_VALIDATE_BOOLEAN );
		$atts['images']       = filter_var( $atts['images'], FILTER_VALIDATE_BOOLEAN );
		$atts['autoplay']     = filter_var( $atts['autoplay'], FILTER_VALIDATE_BOOLEAN );

		// Audio specific:
		$atts['artists']      = filter_var( $atts['artists'], FILTER_VALIDATE_BOOLEAN );
		$atts['current']      = filter_var( $atts['current'], FILTER_VALIDATE_BOOLEAN );

		// Video specific:
		$atts['loop']         = filter_var( $atts['loop'], FILTER_VALIDATE_BOOLEAN );

		// Nested shortcode support:
		$this->type           = ( in_array( $atts['type'], $this->types, TRUE ) ) ? $atts['type'] : 'audio';

		// Get tracs:
		$content              = strip_tags( nl2br( do_shortcode( $content ) ) );

		// Replace last comma:
	    if( false !== ( $pos = strrpos( $content, ',' ) ) ) {
			$content = substr_replace( $content, '', $pos, 1 );
		}

		// Enqueue default scripts and styles for the playlist.
		( 1 === $this->instance ) && do_action( 'wp_playlist_scripts', $atts['type'], $atts['style'] );

	    //----------
		// Output
	    //----------
		$html = '';
		$html .= sprintf( '<div class="wp-playlist wp-%s-playlist wp-playlist-%s">', $this->type, $atts['style'] );

		// Current audio item:
		if( $atts['current'] && 'audio' === $this->type ) {
			$html .= '<div class="wp-playlist-current-item"></div>';
		}

		// Video player:
		if( 'video' === $this->type ):
			$html .= sprintf( '<video controls="controls" preload="none" width="%s" height="%s"></video>',
				$atts['style'],
				$atts['width'],
				$atts['height']
			);
		// Audio player:
		else:
			$html .= sprintf(
	            '<audio controls="controls" preload="%s" %s></audio>',
	            $atts['preload'],
	            $atts['autoplay'] ? 'autoplay' : ''
	            );
		endif;

	   // Next/Previous:
	    $html .= '<div class="wp-playlist-next"></div><div class="wp-playlist-prev"></div>';

		// JSON
		$html .= sprintf( '
			<script class="wp-playlist-script" type="application/json">{
				"type":"%s",
				"tracklist":%b,
				"tracknumbers":%b,
				"images":%b,
				"artists":%b,
				"tracks":[%s]
			}</script></div>',
			$atts['type'],
			$atts['tracklist'],
			$atts['tracknumbers'],
			$atts['images'],
			$atts['artists'],
			$content
		);
		return $html;
	}

	/**
	 * Callback for the [themify_trac] shortcode
	 */
	public function trac_shortcode( $atts = array(), $content = '' ) {
		$atts = shortcode_atts(
			array(
				 'src'                   => '',
				 'type'                  => ( 'video' === $this->type ) ? 'video/mp4' : 'audio/mpeg',
				 'title'                 => '',
				 'caption'               => '',
				 'description'           => '',
				 'image_src'             => sprintf( '%s/wp-includes/images/media/%s.png', get_site_url(), $this->type ),
				 'image_width'           => '48',
				 'image_height'          => '64',
				 'thumb_src'             => sprintf( '%s/wp-includes/images/media/%s.png', get_site_url(), $this->type ),
				 'thumb_width'           => '48',
				 'thumb_height'          => '64',
				 'meta_artist'           => '',
				 'meta_album'            => '',
				 'meta_genre'            => '',
				 'meta_length_formatted' => '',

				 'dimensions_original_width'  => '300',
				 'dimensions_original_height' => '200',
				 'dimensions_resized_width'   => '600',
				 'dimensions_resized_height'  => '400',
			), $atts, 'themify_trac_shortcode' );

		//----------
		// Input
		//----------
		$data['src']                      = esc_url( $atts['src'] );
		$data['title']                    = sanitize_text_field( $atts['title'] );
		$data['type']                     = sanitize_text_field( $atts['type'] );
		$data['caption']                  = sanitize_text_field( $atts['caption'] );
		$data['description']              = sanitize_text_field( $atts['description'] );
		$data['image']['src']             = esc_url( $atts['image_src'] );
		$data['image']['width']           = intval( $atts['image_width'] );
		$data['image']['height']          = intval( $atts['image_height'] );
		$data['thumb']['src']             = esc_url( $atts['thumb_src'] );
		$data['thumb']['width']           = intval( $atts['thumb_width'] );
		$data['thumb']['height']          = intval( $atts['thumb_height'] );
		$data['meta']['length_formatted'] = sanitize_text_field( $atts['meta_length_formatted'] );

		// Video related:
		if( 'video' === $this->type ) {
			$data['dimensions']['original']['width']  = sanitize_text_field( $atts['dimensions_original_width'] );
			$data['dimensions']['original']['height'] = sanitize_text_field( $atts['dimensions_original_height'] );
			$data['dimensions']['resized']['width']   = sanitize_text_field( $atts['dimensions_resized_width'] );
			$data['dimensions']['resized']['height']  = sanitize_text_field( $atts['dimensions_resized_height'] );

		// Audio related:
		} else {
			$data['meta']['artist'] = sanitize_text_field( $atts['meta_artist'] );
			$data['meta']['album']  = sanitize_text_field( $atts['meta_album'] );
			$data['meta']['genre']  = sanitize_text_field( $atts['meta_genre'] );
		}

		//----------
		// Output:
		//----------
		return json_encode( $data ) . ',';
	}

	/**
	 * Check if the site is using an HTTPS scheme and returns the proper url
	 * @param String $url requested url
	 * @return String
	 * @since 1.0.0
	 */
	function https_esc( $url ) {
		if ( is_ssl() ) {
			$url = preg_replace( '/^(http:)/i', 'https:', $url, 1 );
		}
		return $url;
	}

	function hex2rgb( $hex ) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return implode(",", $rgb); // returns the rgb values separated by commas
	}
}
$themify_player = new Themify_Player;