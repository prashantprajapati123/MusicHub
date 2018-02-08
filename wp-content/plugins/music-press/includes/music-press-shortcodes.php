<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Music_Press_Shortcodes class.
 */
class TZ_Music_Press_Shortcodes {
	public function __construct() {

		add_shortcode( 'music_press_description', array( $this, 'tz_get_music_description' ) );
		add_shortcode( 'music_press_album', array( $this, 'tz_get_music_album' ) );
        if ( is_admin() ) {
            add_action( 'init', array( $this, 'music_press_setup_tinymce_plugin' ) );
        }

	}

    public function music_press_setup_tinymce_plugin() {

        // Check if the logged in WordPress User can edit Posts or Pages
        // If not, don't register our TinyMCE plugin
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }

        // Check if the logged in WordPress User has the Visual Editor enabled
        // If not, don't register our TinyMCE plugin
        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }

        // Setup some filters
        add_filter( 'mce_external_plugins', array( $this, 'music_press_add_tinymce_plugin' ) );
        add_filter( 'mce_buttons', array( $this, 'music_press_add_tinymce_toolbar_button' ) );

    }
    public function music_press_add_tinymce_plugin( $plugin_array ) {
        $terms = get_terms( array(
            'taxonomy' => 'album',
            'hide_empty' => false
        ) );
        ?>
        <div class="album_select" style="display:none;">
            <div class="album-item">
            <label><?php echo esc_html__('Select Album','music-press'); ?></label>
            <select class="albums">
                <?php
                foreach($terms as $term){
                    ?>
                    <option value="<?php echo $term->term_id;?>"><?php echo $term->name;?> </option>
                    <?php
                }
                ?>
            </select>
            </div>
            <div class="album-item">
            <label><?php echo esc_html__('AutoPlay','music-press'); ?></label>
            <select class="album_autoplay">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
            </div>
        </div>
        <?php
        $plugin_array['music_press_shortcode_btn'] = TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/js/shortcode/tinymce-custom-class.js';
        return $plugin_array;

    }
    public function music_press_add_tinymce_toolbar_button( $buttons ) {

        array_push( $buttons, 'music_press_shortcode_btn' );
        return $buttons;

    }

	public function tz_get_music_description( $post_id = null ) {
		return get_field ( 'content', $post_id );
	}

	public function tz_get_music_album( $shortcode_atts ) {
		wp_enqueue_script( 'music-press-album-playlist');
		wp_enqueue_style('music-press-jplayer-blue');
		wp_enqueue_script('music-press-jquery.jplayer');
		wp_enqueue_script('music-press-jquery.jplayerlist');

		$defaults = array(
			'album_id' 	=> '',
			'autoplay' 	=> 1
		);
		extract( shortcode_atts( apply_filters( 'music_press_album_shortcode', $defaults ), $shortcode_atts ) );

		$album_info = tz_taxonomy_album_info($album_id);
		$album_type = $album_info ['type'];
        if($autoplay==1){
            $autoplaycode = 'playlistOptions: {
            autoPlay: true
            },';
        } else{
            $autoplaycode='';
        }

		$args = array(
			'post_type'         =>  'music',
			'tax_query'         =>  array(
				array(
					'taxonomy'  =>  'album',
					'filed'     =>  'id',
					'terms'      =>  $album_id,
				)
			)
		);

		if($album_type=='audio'){
			$output ='
				<div id="album_playlist" class="jp-jplayer"></div>
            <div id="album_playlist_container" class="jp-audio" role="application" aria-label="media player">
                <div class="jp-type-playlist">
                    <div class="jp-title"></div>
                    <div class="jp-gui jp-interface">
                        <div class="jp-controls">
                            <button class="jp-stop" role="button" tabindex="0"><i class="fa fa-stop"></i></button>
                            <button class="jp-previous" role="button" tabindex="0"><i class="fa fa-step-backward"></i>
                            </button>
                            <button class="jp-play" role="button" tabindex="0"></button>
                            <button class="jp-next" role="button" tabindex="0"><i class="fa fa-step-forward"></i>
                            </button>
                        </div>
                        <div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>
                        <div class="jp-time-holder">
                            <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
                            <span> / </span>

                            <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                        </div>
                        <div class="jp-volume-controls">
                            <button class="jp-mute" role="button" tabindex="0"></button>
                            <div class="jp-volume-bar">
                                <div class="jp-volume-bar-value"></div>
                            </div>
                        </div>
                        <div class="clr"></div>
                    </div>
                    <div class="jp-playlist">
                        <ul>
                            <li>&nbsp;</li>
                        </ul>
                    </div>
                    <div class="jp-no-solution">
                        <span>Update Required</span>
                        To play the media you will need to either update your browser to a recent version or update your
                        <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                    </div>
                </div>
            </div>
			';
            $output .='
		<script type="text/javascript">
            jQuery(document).ready(function () {

                new jPlayerPlaylist({
                    jPlayer: "#album_playlist",
                    cssSelectorAncestor: "#album_playlist_container"
                }, [';

            $fp_query = new WP_Query( $args );
            if ( $fp_query -> have_posts()):
                while($fp_query -> have_posts()): $fp_query -> the_post();
                    $file_type = get_field('music_type');
                    if($file_type=='audio'){
                        if(get_field('song_audio')){
                            $file = get_field('song_audio');
                        }
                        if(get_field('song_audio_cover')){
                            $file = get_field('song_audio_cover');
                        }
                    }
                    $url = wp_get_attachment_url( $file );
                    $output .='{
						title: "'. esc_attr(get_the_title(get_the_ID())).'",
                        mp3: "'. esc_url($url).'"
					},';
                endwhile;
            endif;
            wp_reset_postdata();


            $output .='
                ], {
                    '.$autoplaycode.'
                    swfPath: "'. esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/assets/js').'",
                    supplied: "mp3",
                    wmode: "window",
                    useStateClassSkin: true,
                    autoBlur: false,
                    smoothPlayBar: true,
                    keyEnabled: true
                });
            });
        </script>
		';
		}

		if($album_type=='video'){
			$output ='
				<div id="music_album_video_container" class="jp-video jp-video-270p" role="application" aria-label="media player">
                <div class="jp-type-playlist">
                    <div id="music_album_video" class="jp-jplayer"></div>
                    <div class="jp-gui">
                        <div class="jp-video-play">
                            <button class="jp-video-play-icon" role="button" tabindex="0">play</button>
                        </div>
                        <div class="jp-interface">
                            <div class="jp-progress">
                                <div class="jp-seek-bar">
                                    <div class="jp-play-bar"></div>
                                </div>
                            </div>
                            <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
                            <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                            <div class="jp-controls-holder">
                                <div class="jp-controls">
                                    <button class="jp-previous" role="button" tabindex="0">previous</button>
                                    <button class="jp-play" role="button" tabindex="0">play</button>
                                    <button class="jp-next" role="button" tabindex="0">next</button>
                                    <button class="jp-stop" role="button" tabindex="0">stop</button>
                                </div>
                                <div class="jp-volume-controls">
                                    <button class="jp-mute" role="button" tabindex="0">mute</button>
                                    <button class="jp-volume-max" role="button" tabindex="0">max volume</button>
                                    <div class="jp-volume-bar">
                                        <div class="jp-volume-bar-value"></div>
                                    </div>
                                </div>
                                <div class="jp-toggles">
                                    <button class="jp-repeat" role="button" tabindex="0">repeat</button>
                                    <button class="jp-shuffle" role="button" tabindex="0">shuffle</button>
                                    <button class="jp-full-screen" role="button" tabindex="0">full screen</button>
                                </div>
                            </div>
                            <div class="jp-details">
                                <div class="jp-title" aria-label="title">&nbsp;</div>
                            </div>
                        </div>
                    </div>
                    <div class="jp-playlist">
                        <ul>
                            <!-- The method Playlist.displayPlaylist() uses this unordered list -->
                            <li>&nbsp;</li>
                        </ul>
                    </div>
                    <div class="jp-no-solution">
                        <span>Update Required</span>
                        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                    </div>
                </div>
            </div>
			';
            $output .='
		<script type="text/javascript">
            jQuery(document).ready(function () {

                new jPlayerPlaylist({
                    jPlayer: "#music_album_video",
                    cssSelectorAncestor: "#music_album_video_container"
                }, [';

            $fp_query = new WP_Query( $args );
            if ( $fp_query -> have_posts()):
                while($fp_query -> have_posts()): $fp_query -> the_post();
                    $file_type = get_field('music_type');
                    if($file_type=='video'){
                        if(get_field('song_video')){
                            $file = get_field('song_video');
                        }
                        if(get_field('song_video_cover')){
                            $file = get_field('song_video_cover');
                        }
                    }
                    $url = wp_get_attachment_url( $file );
                    $output .='{
						title: "'. esc_attr(get_the_title(get_the_ID())).'",
                        m4v: "'. esc_url($url).'",
                        poster:"'.esc_url(get_the_post_thumbnail_url( 'full' )).'"
					},';
                endwhile;
            endif;
            wp_reset_postdata();


            $output .='
                ], {
                    '.$autoplaycode.'
                    swfPath: "'. esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/assets/js').'",
                    supplied: "webmv, ogv, m4v",
                    useStateClassSkin: true,
                    autoBlur: false,
                    smoothPlayBar: true,
                    keyEnabled: true
                });
            });
        </script>
		';
		}

		return $output;
	}


}

new TZ_Music_Press_Shortcodes();