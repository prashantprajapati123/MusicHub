<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();
wp_enqueue_style('music-press-jplayer-blue');
wp_enqueue_script('music-press-jquery.jplayer');
wp_enqueue_script('music-press-jquery.jplayerlist');
require TZ_MUSIC_PRESS_PLUGIN_DIR . '/includes/duration.php';
$music_term_slug = get_query_var( 'term' );
$music_taxonomyName = get_query_var( 'taxonomy' );
$music_current_term = get_term_by( 'slug', $music_term_slug, $music_taxonomyName );
$music_info = tz_taxonomy_album_info($music_current_term->term_id);
$autoplay = get_field('music_autoplay', 'option');
if($music_info ['type'] == 'audio') {
    ?>
    <div class="music-album">
        <?php if(tz_music_press_taxonomy_image_url()){ ?>
        <div class="album-image">
            <img src="<?php echo tz_music_press_taxonomy_image_url(); ?>"/>
        </div>
        <?php } ?>
        <div class="album-info">
            <h1 class="album-title"> <?php single_tag_title(); ?> </h1>

            <div class="ab-item">
                <label><?php echo esc_html__('Created: ', 'music-press'); ?></label>
                <span><?php echo esc_html($music_info['created']); ?></span>
            </div>
            <div class="ab-item">
                <label><?php echo esc_html__('Tracks: ', 'music-press'); ?></label> <span><?php echo esc_html($music_current_term->count) . ' ' . esc_html__('Songs', 'music-press'); ?></span>
            </div>
            <div class="ab-item">
                <label><?php echo esc_html__('Length: ', 'music-press'); ?></label>

                <span>
                <?php
                $music_total = '';
                if (have_posts()):
                    while (have_posts()):
                        the_post();
                        $music_file_type = get_field('music_type');
                        if ($music_file_type == 'audio') {
                            if (get_field('song_audio')) {
                                $music_file = get_field('song_audio');
                            }
                            if (get_field('song_audio_cover')) {
                                $music_file = get_field('song_audio_cover');
                            }
                            if ($music_file) {
                                $music_url = wp_get_attachment_url($music_file);
                                $music_dataArray = new TZ_Music_Mp3Data("$music_url");
                                $music_song_duration = $music_dataArray->tz_get_mp3_duration();

                                $music_duration = explode(':', $music_song_duration);

                                $music_total += $music_duration[0] * 60;
                                $music_total += $music_duration[1];
                            }
                        }
                    endwhile;
                endif;

                $music_hour = floor($music_total / 3600);
                $music_mins = floor(($music_total - ($music_hour * 3600)) / 60);

                ?>
                <?php
                if (floor($music_total / 3600) >= 1):
                    echo esc_html($music_hour) . ' ' . esc_html__('Hour', 'musika');
                endif;
                echo ' ';
                echo esc_html($music_mins) . ' ' . esc_html__('Minutes', 'musika');
                ?>
            </span>
            </div>
            <div class="al-desc">
                <?php echo category_description(); ?>
            </div>

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
        </div>

        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready(function () {

                new jPlayerPlaylist({
                    jPlayer: "#album_playlist",
                    cssSelectorAncestor: "#album_playlist_container"
                }, [
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
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
                    ?>
                    {
                        title: "<?php echo esc_attr(get_the_title(get_the_ID()));?>",
                        mp3: "<?php echo esc_url($url);?>"
                    },
                    <?php endwhile; endif;?>
                ], {
                <?php if($autoplay=='yes'){
                    ?>playlistOptions: {
                    autoPlay: true
                    },
                <?php }?>
                    swfPath: "<?php echo esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/assets/js');?>",
                    supplied: "mp3",
                    wmode: "window",
                    useStateClassSkin: true,
                    autoBlur: false,
                    smoothPlayBar: true,
                    keyEnabled: true
                });
            });
            //]]>
        </script>
    </div>
    <?php
}
if($music_info ['type'] == 'video') {?>
    <div class="music-album">
        <div class="album-image">
            <img src="<?php echo tz_music_press_taxonomy_image_url(); ?>"/>

            <h1 class="album-title"> <?php single_tag_title(); ?> </h1>
            <div class="ab-item">
                <label><?php echo esc_html__('Created: ', 'music-press'); ?></label>
                <span><?php echo esc_html($music_info['created']); ?></span>
            </div>
            <div class="ab-item">
                <label><?php echo esc_html__('Videos: ', 'music-press'); ?></label> <span><?php echo esc_html($music_current_term->count) . ' ' . esc_html__('Video', 'music-press'); ?></span>
            </div>
            <div class="al-desc">
                <?php echo category_description(); ?>
            </div>

        </div>
        <div class="album-info">
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

        </div>

        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready(function () {

                new jPlayerPlaylist({
                    jPlayer: "#music_album_video",
                    cssSelectorAncestor: "#music_album_video_container"
                }, [
                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
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
                    ?>
                    {
                        title: "<?php echo esc_attr(get_the_title(get_the_ID()));?>",
                        m4v: "<?php echo esc_url($url);?>",
                        poster:"<?php the_post_thumbnail_url( 'full' );?>"
                    },
                    <?php endwhile; endif;?>
                ], {
                    <?php if($autoplay=='yes'){
                        ?>playlistOptions: {
                        autoPlay: true
                    },
                    <?php }?>
                    swfPath: "<?php echo esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/assets/js');?>",
                    supplied: "webmv, ogv, m4v",
                    useStateClassSkin: true,
                    autoBlur: false,
                    smoothPlayBar: true,
                    keyEnabled: true
                });
            });
            //]]>
        </script>
    </div>
<?php
}
get_footer();
