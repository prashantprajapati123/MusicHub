<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
get_header();
wp_enqueue_style('music-press-jplayer-blue');
wp_enqueue_script('music-press-jquery.jplayer');
// Start the loop.
$autoplay = get_field('music_autoplay', 'option');

?>

<?php
while ( have_posts() ) : the_post();

	$file_type = get_field('music_type');
	$songID = get_the_ID();
	$term_albums = wp_get_post_terms( $songID, 'album', $args );
	$term_artist = wp_get_post_terms( $songID, 'artist', $args );
	if($file_type=='audio'){
?>

<div class="music-song">
	<div class="song-image tz-50">
		<div class="song-artist">
			<?php
			foreach($term_artist as $artist){?>
				<?php if(tz_music_press_taxonomy_image_url($artist->term_id)){ ?>
				<img class="artist-image" src="<?php echo tz_music_press_taxonomy_image_url($artist->term_id); ?>" />

				<?php } ?>

				<a href="<?php echo get_term_link($artist->term_id);?>" class="artist-name">
					<?php echo esc_attr($artist->name);?>
				</a>
				<?php
			}
			?>
			<div class="music-description">
				<?php the_content();?>
			</div>
		</div>

	</div>

	<div class="tz-50 music_player">
		<?php
		the_post_thumbnail();
		?>
		<div id="music_press_audio_player" class="jp-jplayer"></div>
		<div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<div class="jp-controls">
						<button class="jp-play" role="button" tabindex="0">play</button>
						<button class="jp-stop" role="button" tabindex="0">stop</button>
					</div>
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-volume-controls">
						<button class="jp-mute" role="button" tabindex="0">mute</button>
						<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
					</div>
					<div class="jp-time-holder">
						<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
						<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
						<div class="jp-toggles">
							<button class="jp-repeat" role="button" tabindex="0">repeat</button>
						</div>
					</div>
				</div>
				<div class="jp-details">
					<div class="jp-title" aria-label="title">&nbsp;</div>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>

		<h1 class="song-name">
			<?php the_title();?>
			<span>
		<?php
		echo esc_html__('-','music-press');
		foreach($term_artist as $artist){?>
			<a href="<?php echo get_term_link($artist->term_id);?>">
				<?php echo esc_attr($artist->name);?>
			</a>
			<?php
		}
		?>
		</span>
		</h1>
		<div class="song_album">
		<span class="song_album_label">
			<?php echo esc_html__('Albums','music-press');?>
		</span>
			<?php
			foreach($term_albums as $album){?>
				<a href="<?php echo get_term_link($album->term_id);?>">
					<?php echo esc_attr($album->name);?>
				</a>
				<?php
			}
			?>
		</div>

		<?php
		if(get_field('song_for_sale')){?>
			<a class="sale_song" href="<?php echo esc_url(get_field('song_for_sale'));?>" target="_blank">
				<?php echo esc_html__('Buy Now','music-press'); ?>
			</a>
			<?php
		}
		?>
		<?php if(get_field('song_lyric')){ ?>
			<div class="music-lyric">
				<h3><?php echo esc_html__('Lyric','music-press');?></h3>
				<?php echo balanceTags(get_field('song_lyric'));?>
			</div>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php
			if(get_field('song_audio')){
				$file = get_field('song_audio');
			}
			if(get_field('song_audio_cover')){
				$file = get_field('song_audio_cover');
			}
			if( $file ) {
				$url = wp_get_attachment_url( $file );
			}
		?>
		jQuery("#music_press_audio_player").jPlayer({
			ready: function (event) {
				jQuery(this).jPlayer("setMedia", {
					title: "<?php echo esc_attr(get_the_title(get_the_ID()));?>",
					m4a: "<?php echo esc_url($url);?>"
				})<?php if($autoplay=='yes'){ ?>.jPlayer("play") <?php }?>;
			},
			swfPath: "<?php echo esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'assets/js');?>",
			supplied: "m4a, oga",
			wmode: "window",
			useStateClassSkin: true,
			autoBlur: false,
			smoothPlayBar: true,
			keyEnabled: true,
			remainingDuration: true,
			toggleDuration: true
		});
	});
</script>
<?php
	}
	if($file_type=='video'){
		?>
		<div class="music_video">
			<div id="jp_container_1" class="jp-video jp-video-360p" role="application" aria-label="media player">
				<div class="jp-type-single">
					<div id="music_press_video" class="jp-jplayer"></div>
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
									<button class="jp-play" role="button" tabindex="0">play</button>
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
									<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
								</div>
							</div>
							<div class="jp-details">
								<div class="jp-title" aria-label="title">&nbsp;</div>
							</div>
						</div>
					</div>
					<div class="jp-no-solution">
						<span>Update Required</span>
						To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
					</div>
				</div>
			</div>
		</div>

		<div class="song-video-artist">
			<h1 class="song-name">
				<?php the_title();?>
				<span>
					<?php
					echo esc_html__('-','music-press');
					foreach($term_artist as $artist){?>
						<a href="<?php echo get_term_link($artist->term_id);?>">
							<?php echo esc_attr($artist->name);?>
						</a>
						<?php
					}
					?>
				</span>
			</h1>
			<div class="song_album">
				<span class="song_album_label">
					<?php echo esc_html__('Albums','music-press');?>
				</span>
				<?php
				foreach($term_albums as $album){?>
					<a href="<?php echo get_term_link($album->term_id);?>">
						<?php echo esc_attr($album->name);?>
					</a>
					<?php
				}
				?>
			</div>
			<div class="music-description">
				<?php the_content();?>
			</div>
			<?php if(get_field('song_lyric')){ ?>
				<div class="music-lyric">
					<h3><?php echo esc_html__('Lyric','music-press');?></h3>
					<?php echo balanceTags(get_field('song_lyric'));?>
				</div>
			<?php } ?>
		</div>


		<script type="text/javascript">
			<?php
				if(get_field('song_video')){
					$file = get_field('song_video');
				}
				if(get_field('song_video_cover')){
					$file = get_field('song_video_cover');
				}
				if( $file ) {
					$url = wp_get_attachment_url( $file );
					?>
			//<![CDATA[
			jQuery(document).ready(function(){

				jQuery("#music_press_video").jPlayer({
					ready: function () {
						jQuery(this).jPlayer("setMedia", {
							title: "<?php echo esc_attr(get_the_title(get_the_ID()));?>",
							m4v: "<?php echo esc_url($url);?>",
							poster: "<?php the_post_thumbnail_url( 'full' );?>"
						})<?php if($autoplay=='yes'){ ?>.jPlayer("play") <?php }?>;
					},
					swfPath: "<?php echo esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/assets/js');?>",
					supplied: "webmv, ogv, m4v",
					size: {
						width: "640px",
						height: "360px",
						cssClass: "jp-video-360p"
					},
					useStateClassSkin: true,
					autoBlur: false,
					smoothPlayBar: true,
					keyEnabled: true,
					remainingDuration: true,
					toggleDuration: true
				});

			});
			<?php } ?>
			//]]>
		</script>

		<?php
	 }
endwhile;
get_footer();