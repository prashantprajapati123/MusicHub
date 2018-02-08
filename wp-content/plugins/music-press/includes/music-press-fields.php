<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TZ_Music_Press_Fields {

    public function __construct() {

        $this->fields = array();

        // add query vars of our searchform
        add_filter( 'query_vars', array( $this, 'tz_music_press_add_query_vars_filter' ) );
        add_filter( 'pre_get_posts', array( $this, 'tz_music_press_filter_posts' ) );
        add_filter( 'acf/save_post', array( $this, 'tz_music_press_sync_content_field' ) );

        add_action( 'plugins_loaded', array( $this, 'tz_music_press_register_admin_fields' ) );
        add_action( 'init', array( $this, 'tz_music_press_register_fields' ) );

        $this->built_in = array(

            'music_type' => array(
                'label'     => __( 'Music Type', 'music-press' ),
                'name'      => 'music_type',
                'instructions' => '',
                'type'      => 'radio',
                'choices'   => array(
                        'audio' => __( 'Audio', 'music-press' ),
                        'video' => __( 'Video', 'music-press' ),
                ),
                'default_value' => 'audio',
                'class'         => 'music_type',
                'other_choice'  => 0,
                'sort'          => 30
            ),
            'music_license' => array(
                'label'         => __( 'Music Type', 'music-press' ),
                'name'          => 'music_license',
                'instructions'  => '',
                'type'          => 'radio',
                'choices'       => array(
                            'free' => __( 'Free', 'music-press' ),
                            'sale' => __( 'Sale', 'music-press' ),
                ),
                'default_value' => 'free',
                'class'         => 'music_license',
                'other_choice'  => 0,
                'sort'          => 5
            ),
            'song_audio'        => array(
                'label'         => __( 'Audio File <small>(Select mp3 file)</small>', 'music-press' ),
                'name'          => 'song_audio',
                'instructions'  => '',
                'type'          => 'file',
                'sort'          => 30

            ),
            'song_audio_cover'  => array(
                'label'         => __( 'Audio File Cover  <small>(Select mp3 file)</small>', 'music-press' ),
                'name'          => 'song_audio_cover',
                'instructions'  => '',
                'type'          => 'file',
                'sort'          => 30
            ),
            'song_video'  => array(
                'label'         => __( 'Video File  <small>(Select mp4 file)</small>', 'music-press' ),
                'name'          => 'song_video',
                'instructions'  => '',
                'type'          => 'file',
                'sort'          => 35
            ),
            'song_video_cover'  => array(
                'label'         => __( 'Video File Cover  <small>(Select mp4 file)</small>', 'music-press' ),
                'name'          => 'song_video_cover',
                'instructions'  => '',
                'type'          => 'file',
                'sort'          => 35
            ),
            'song_albums'    => array(
                'label'     => __( 'Albums', 'music-press' ),
                'name'      => 'song_albums',
                'type'      => 'taxonomy',
                'taxonomy'  => 'album',
                'sort'      => 5,
                'field_type'=> 'checkbox',
                'allow_null'=> 0
            ),
            'song_artists'    => array(
                'label'     => __( 'Artist', 'music-press' ),
                'name'      => 'song_artists',
                'type'      => 'taxonomy',
                'taxonomy'  => 'artist',
                'sort'      => 5,
                'field_type'=> 'select',
                'allow_null'=> 0
            ),
            'song_for_sale'    => array(
                'label'     => __( 'Sale Url', 'music-press' ),
                'name'      => 'song_for_sale',
                'type'      => 'text',
                'class'     => 'music_sale',
                'sort'      => 10,
                'allow_null'=> 0
            )
        );
    }


    public function tz_music_press_add_query_vars_filter( $vars ) {

        $fields = $this->get_registered_fields();

        if ( $fields ) {
            foreach ( $fields as $field ) {
                if ( 'taxonomy' != $field['type'] ) {
                    $vars[] = $field['name'];
                }
            }
        }
        return $vars;

    }

    public function tz_music_press_filter_posts( $query ) {

        if ( ! is_post_type_archive('music') && ( isset($query->query['post_type']) && 'music' != $query->query['post_type'] )) {
            return;
        }
        if ( ! $query->is_main_query()  ) {
            return;
        }

        $meta_query = array();
        $fields = $this->get_registered_fields();

        if ( $fields ) {
            foreach ( $fields as $field ) {

                $query_var = get_query_var( $field['name'] );

                if ( empty( $query_var ) || ! is_array( $query_var ) ) {
                    continue;
                }
                if ( 'radio' == $field['type'] || 'checkbox' == $field['type'] ) {

                    $meta_query[] = array(
                        'key' => $field['name'],
                        'value' => $query_var,
                        'compare' => 'IN'
                    );
                }
            }
        }

        $query->set('meta_query', $meta_query);

    }

    public function tz_music_press_sync_content_field( $post_id ) {
        // vars
        $fields = false;

        // load from post
        if( isset( $_POST['fields'] ) ) {
            $field_key = get_field( '_content', $post_id );

            if ( $field_key ) {

            }
            else {
                $content = 0;
            }




            // Update post 37
            $updated_post = array(
                'ID'           => $post_id,
                'post_content' => $content
            );

            remove_action( 'acf/save_post', array( $this, 'tz_music_press_sync_content_field' ));

            // Update the post into the database
            wp_update_post( $updated_post );

            add_action( 'acf/save_post', array( $this, 'tz_music_press_sync_content_field' ));
        }
    }

    public function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

        global $wpdb;

        if( empty( $key ) )
            return;

        $r = $wpdb->get_col( $wpdb->prepare( "
	        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	        WHERE pm.meta_key = '%s'
	        AND p.post_status = '%s'
	        AND p.post_type = '%s'
	    ", $key, $status, $type ) );

        return $r;
    }

    public function tz_music_register_field( $args ) {

        // ACF requires a unique key per field so lets generate one
        $key = md5( serialize( $args ));

        if ( empty( $args['type'] )) {
            $args['type'] = 'number';
        }
        $type = $args['type'];

        if ( 'taxonomy' == $type ) {

            $field = wp_parse_args( $args, array(
                'key'           => $key,
                'label'         => '',
                'name'          => '',
                'type'          => 'taxonomy',
                'instructions'  => '',
                'taxonomy'      => '',
                'field_type'    => 'select',
                'allow_null'    => 0,
                'load_save_terms' => 1,
                'add_term'		=> 0,
                'return_format' => 'id',
                'multiple'      => 0,
                'sort'          => 0,
                'group'         => 'information'
            ) );
        } else if ( 'radio' == $type ) {
            $field = wp_parse_args( $args, array (
                'key' => $key,
                'label' => '',
                'name' => '',
                'instructions' => '',
                'choices' => array(),
                'other_choice' => 1,
                'save_other_choice' => 1,
                'default_value' => '',
                'layout' => 'horizontal',
                'sort' => 0,
                'group' => 'music_file'
            ) );
        } else if ( 'checkbox' == $type ) {

            $field = wp_parse_args( $args, array (
                'key' => $key,
                'label' => '',
                'name' => '',
                'instructions' => '',
                'choices' => array(),
                'layout' => 'vertical',
                'sort' => 0,
                'multiple'	=> 1,
                'group' => 'information'
            ) );
        } else {
            $field = wp_parse_args( $args, array (
                'key' => $key,
                'label' => '',
                'name' => '',
                'type' => 'number',
                'instructions' => '',
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => 0,
                'max' => '',
                'step' => '',
                'sort' => 0,
                'group' => 'music_file'
            ) );
        }
        $field = apply_filters( 'pcd/register_field', $field );
        $this->fields[$field['name']] = $field;

        return $field;
    }

    public function tz_register_built_in_fields() {

        $built_in_fields = apply_filters( 'pcd/built_in_fields', $this->built_in);

        if ( ! empty( $built_in_fields )) {
            foreach ( $built_in_fields as $field ) {
                $this->tz_music_register_field( $field );
            }
        }

    }


    public function tz_music_press_register_fields() {

        $this->tz_register_built_in_fields();

        $fields = array();
        $translations = array( // not used anywhere else
            __( 'Description', 'music-press' ),
            __( 'Information', 'music-press' ),
            __( 'Music File', 'music-press' ),
        );

        $layout_groups = array(
            'Description' => array(),
            'Information' => array(),
            'Music File' => array()
        );
        $layout_groups['Description'] = $this->get_registered_fields( 'description' );
        $layout_groups['Description'][] = array (
            'key' => 'field_52910fe7d4efa',
            'label' => __( 'Song Description', 'music-press' ),
            'name' => 'content',
            'type' => 'wysiwyg',
            'default_value' => '',
            'toolbar' => 'basic',
            'media_upload' => 'yes',
            'sort'      => 30,
        );
        $layout_groups['Description'][] = array (
            'key' => 'field_52910fe7d4efd',
            'label' => __( 'Song Lyric', 'music-press' ),
            'name' => 'song_lyric',
            'type' => 'textarea',
            'default_value' => '',
            'sort'      => 10,
        );
        $layout_groups['Information'] = $this->get_registered_fields('information');

        $layout_groups['Music File'] = $this->get_registered_fields('music_file');

        foreach ( $layout_groups as $label => $field_group ) {
            $fields[] = array (
                'key' => 'tab_'. $label,
                'label' => __( $label, 'music-press' ),
                'name' => $label,
                'type' => 'tab',
            );
            foreach ( $field_group as $field ) {
                $fields[] = $field;
            };
        };

        if(function_exists("register_field_group"))
        {
            register_field_group(array (
                'id' => 'acf_song-data',
                'title' => __( 'Song Data', 'music-press' ),
                'fields' => $fields,
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'music',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),
                'options' => array (
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                        'the_content', 'custom_fields'
                    ),
                ),
                'menu_order' => 0,
            ));
        }
    }

    public function get_registered_fields( $group = '' ) {

        $fields = $this->fields;
        $filtered = array();
        $sorted = array();

        foreach ($fields as $key => $field ) {
            $fields[$key]['label'] = __( $field['label'], 'music-press' );
        }

        if ( ! empty( $group ) ) {
            foreach ($fields as $field ) {
                if ( $group == $field['group'] ) {
                    $filtered[] = $field;
                }
            }
        } else {
            $filtered = $fields;
        }

        foreach ( $filtered as $key => $value ) {
            $sorted[$key]  = $value['sort'];
        }

        array_multisort( $sorted, SORT_ASC, SORT_NUMERIC, $filtered );

        return apply_filters( 'pcd/fields', $filtered );
    }

    public function tz_music_press_register_admin_fields(){
        if ( function_exists( 'acf_add_options_sub_page' ) && function_exists( 'register_field_group' )) {

            acf_add_options_sub_page(array(
                'title' => __( 'Settings', 'music-press' ),
                'parent' => 'edit.php?post_type=music',
                'capability' => 'manage_options'
            ));

            $built_in_fields = $this->built_in;


            $choices = array();
            if ( ! empty( $built_in_fields )) {
                foreach ($built_in_fields as $field) {
                    $choices[$field['name']] = $field['label'];
                }
            }

            register_field_group(array (
                'id' => 'music_press_settings_page',
                'title' => __( 'Settings', 'music-press' ),
                'fields' => array (
                    array (
                        'key' => 'field_52910fcad4ef944',
                        'label' => __( 'General', 'music-press' ),
                        'name' => '',
                        'type' => 'tab',
                    ),
                        array (
                            'key' => 'field_5281609138d88',
                            'label' => __( 'AutoPlay', 'music-press' ),
                            'name' => 'music_autoplay',
                            'type' => 'radio',
                            'choices' => array (
                                'yes' => __( 'Yes', 'music-press' ),
                                'no'  => __( 'No', 'music-press' ),
                            ),
                            'other_choice' => 0,
                            'save_other_choice' => 0,
                            'default_value' => 'yes',
                            'layout' => 'horizontal',
                        )
                ),

                'location' => array (
                    array (
                        array (
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'acf-options-settings',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),

                'options' => array (
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                    ),
                ),
                'menu_order' => 0,
            ));

        }
    }
}