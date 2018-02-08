<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();
?>
<div class="artist-page">
<?php if(tz_music_press_taxonomy_image_url()){ ?>
    <div class="artists-info-image">
        <div class="artist-image">
            <img src="<?php echo tz_music_press_taxonomy_image_url(); ?>" />
        </div>
    </div>
    <?php } ?>
    <div class="artist-info-container">
        <div class="artist-info">
            <h1 class="artist-title"> <?php single_tag_title(); ?> </h1>
            <?php
            $artist_info = tz_taxonomy_artist_info();
            ?>
            <ul>
                <?php if($artist_info['realname']){ ?>
                    <li><?php echo esc_html__('Name: ','music-press');?><span><?php echo esc_attr($artist_info['realname']);?></span></li>
                <?php } ?>
                <?php if($artist_info['birthday']){ ?>
                    <li><?php echo esc_html__('Birthday: ','music-press');?><span><?php echo esc_attr($artist_info['birthday']);?></span></li>
                <?php } ?>
                <?php if($artist_info['occupation']){ ?>
                    <li><?php echo esc_html__('Occupation: ','music-press');?><span><?php echo esc_attr($artist_info['occupation']);?></span></li>
                <?php } ?>
                <?php if($artist_info['instruments']){ ?>
                    <li><?php echo esc_html__('Instruments: ','music-press');?><span><?php echo esc_attr($artist_info['instruments']);?></span></li>
                <?php } ?>
                <?php if($artist_info['website']){ ?>
                    <li><?php echo esc_html__('Website: ','music-press');?><span><?php echo esc_attr($artist_info['website']);?></span></li>
                <?php } ?>
            </ul>
        </div>
        <div class="artist-description">
            <?php echo category_description( ); ?>
        </div>
        <div class="artist-songs">
            <?php if ( have_posts() ) : ?>
                <h2><?php echo esc_attr__('Songs','music-press');?></h2>
                <ul class="all-songs">
                    <?php
                    while ( have_posts() ) : the_post();

                        $file_type = get_field('music_type');
                        if($file_type=='audio'){
                            if(get_field('song_audio')){
                                $file = get_field('song_audio');
                            }
                            if(get_field('song_audio_cover')){
                                $file = get_field('song_audio_cover');
                            }
                            if( $file ) {
                                ?>
                                <li><a href="<?php echo esc_url(get_permalink()) ;?>"><?php the_title();?></a></li>
                                <?php
                            }
                        }
                    endwhile;?>
                </ul>
                <?php
            endif;
            ?>

            <?php if ( have_posts() ) : ?>
                <h2><?php echo esc_attr__('Videos','music-press');?></h2>
                <div class="all-videos">
                    <?php
                    while ( have_posts() ) : the_post();

                        $file_type = get_field('music_type');
                        if($file_type=='video'){
                            if(get_field('song_video')){
                                $file = get_field('song_video');
                            }
                            if(get_field('song_video_cover')){
                                $file = get_field('song_video_cover');
                            }

                            if( $file ) {
                                ?>
                                <div class="video-song">
                                    <a href="<?php echo esc_url(get_permalink()) ;?>">
                                        <?php the_post_thumbnail('medium');?>
                                    </a>
                                    <a href="<?php echo esc_url(get_permalink()) ;?>"><?php the_title();?></a>
                                </div>
                                <?php
                            }
                        }
                    endwhile;?>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>


</div>
<?php

get_footer();
