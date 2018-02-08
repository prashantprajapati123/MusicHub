<?php
/*
Plugin Name: Music-Press
Plugin URI: http://wordpress.templaza.net/plugins/music-press/album/sing-me-to-sleep/
Description: Music Press Plugin help you create and manager your music store. Genre manager, Artist manager, Albums manager. You can create playlist audio and playlist video.
Version: 1.0.2
Author: tuyennv, templaza
Author URI: http://templaza.com/
License: GPLv2 or later
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TZ_Music_Press{
    public function __construct() {
        define( 'TZ_MUSIC_PRESS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'TZ_MUSIC_PRESS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
        define( 'TZ_MUSIC_PRESS_PLUGIN_BASENAME', plugin_basename(__FILE__) );

        if ( ! class_exists( 'Acf' ) && ! defined ( 'ACF_LITE' ) ) {
            define( 'ACF_LITE' , true );

            // Include Advanced Custom Fields
            include( 'includes/library/acf/acf.php' );
        }
        if ( ! class_exists( 'acf_options_page_plugin' ) ) {
            include( 'includes/library/acf-options-page/acf-options-page.php' );
        }
        include( 'includes/music-taxonomy-image.php' );
        include( 'includes/music-press-customs.php' );
        include( 'includes/music-press-fields.php' );
        include( 'includes/music-press-template-loader.php' );
        include( 'includes/music-press-shortcodes.php' );
        if ( is_admin() ) {
            include( 'includes/admin/music-press-admin.php' );
        }
        // Check add-ons install

        $dir    = ''.TZ_MUSIC_PRESS_PLUGIN_DIR.'/includes/add-ons';
        $folders = scandir($dir);
        $countdir = count($folders);
        if($countdir >2){ $i=1;
            foreach($folders as $folder){
                if($i>2){
                    $addons_file = TZ_MUSIC_PRESS_PLUGIN_DIR.'/includes/add-ons/'.$folder.'/music-press-'.$folder.'.php';
                    if (file_exists($addons_file )) {
                        include( 'includes/add-ons/'.$folder.'/music-press-'.$folder.'.php');
                    }
                }
                $i++;
            }
        }

        $this->customs	    = new TZ_Music_Press_Customs();
        $this->fields 	    = new TZ_Music_Press_Fields();
        $this->templates 	= new TZ_Music_Press_Loader();

        // Activation - works with symlinks

        add_action( 'admin_init', array( $this, 'music_press_updater' ) );
        add_action( 'plugins_loaded', array( $this, 'music_press_load_plugin_textdomain' ) );
        add_action( 'switch_theme', 'flush_rewrite_rules', 15 );
        add_action( 'wp_enqueue_scripts', array( $this, 'tz_music_frontend_scripts' ) );
        add_action( 'switch_theme', array( $this->customs, 'tz_music_register_customs' ), 10 );

    }

    public function music_press_load_plugin_textdomain() {
        load_plugin_textdomain( 'music-press', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
    public function music_press_updater() {
            include_once( 'includes/music-press-install.php' );
    }
    public function tz_music_frontend_scripts() {
        wp_enqueue_style( 'music-press-style', TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/css/music_style.css' );

        wp_register_script('music-press-jquery.jplayer', TZ_MUSIC_PRESS_PLUGIN_URL .'/assets/js/jquery.jplayer.js', array(), false, true);
        wp_register_script('music-press-jquery.jplayerlist', TZ_MUSIC_PRESS_PLUGIN_URL .'/assets/js/jplayer.playlist.js', array(), false, true);

        wp_register_style( 'music-press-jplayer-blue', TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/css/playlist/blue.monday/css/jplayer.blue.monday.min.css', false );
        wp_register_style( 'music-press-jplayer-playlist', TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/css/playlist/playlist.css', false );
    }

    public function tz_music_template_path() {
        return apply_filters( 'music_press_template_path', 'music-press/' );
    }

}

$GLOBALS['music_press'] = new TZ_Music_Press();