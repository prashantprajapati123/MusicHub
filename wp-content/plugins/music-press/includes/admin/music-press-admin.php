<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Music_Press_Admin class.
 */
class Music_Press_Admin {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {

        // Enqueue CSS and JS in admin
        add_action( 'admin_enqueue_scripts', array( $this, 'music_press_admin_scripts' ) );

        // Use a custom walker for the ACF dropdowns
        add_filter( 'acf/fields/taxonomy/wp_list_categories', array( $this, 'acf_wp_list_categories' ), 10, 2 );

        // Adds a Make column to the Model admin columns
        add_filter( 'manage_edit-model_columns', array( $this, 'manage_edit_model_columns' ) );
        add_filter( 'manage_model_custom_column', array( $this, 'manage_model_custom_column' ), 10, 3 );

        // Add helpful action links to the plugin list
        add_filter( 'plugin_action_links_'. TZ_MUSIC_PRESS_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
    }

    /**
     * Enqueue CSS and JS in admin
     *
     * @access public
     * @return void
     */
    public function music_press_admin_scripts() {
		wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_style( 'music_press_admin_css', TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/css/admin.css', false );
        wp_enqueue_script( 'music_press_admin_js', TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ),  true );
    }

    /**
     * Use a custom walker for the ACF dropdowns
     * @param  array $args 		The default dropdown arguments
     * @return array 			Default dropdown args + custom walker
     */
    public function acf_wp_list_categories( $args, $field ) {
        $args['walker'] = new music_press_acf_taxonomy_field_walker( $field );
        return $args;
    }

    /**
     * Adds a Make column to the Model admin columns
     * @param  array $columns 	Existing columns
     * @return array 			Added the Make column
     */
    public function manage_edit_model_columns( $columns ) {
        $columns['make'] = 'Make';
        return $columns;
    }

    /**
     * Displays the actual content of the Make column
     * @param  string $content     	Default content of each cell
     * @param  string $column_name 	Name of the current column
     * @param  int $term_id     	The ID of the current term
     * @return string               Show an admin edit link to the Make
     */
    public function manage_model_custom_column( $content, $column_name, $term_id ){
        $make = get_field( 'make', 'model_'. $term_id );
        if ( $make ) {
            $content .= sprintf( '<a href="%s" title="%s">%s</a>', admin_url( 'edit-tags.php?action=edit&taxonomy=album&tag_ID='. $make->term_id .'&post_type=music' ), __( 'Edit Album', 'music-press' ), $make->name );
        } else {
            $content .= '-';
        }
        return $content;
    }

    /**
     * Add helpful action links to the plugin list
     * @param  array $links Excisting links
     * @return array        Added link to plugin settings
     */
    public function plugin_action_links( $links ) {
        $links[] = '<a href="' . admin_url( 'edit.php?post_type=music&page=acf-options-settings' ) . '">' . __( 'Settings', 'music-press' ) . '</a>';
        return $links;
    }
}

new Music_Press_Admin();


class music_press_acf_taxonomy_field_walker extends acf_taxonomy_field_walker
{
    // start_el
    function start_el( &$output, $term, $depth = 0, $args = array(), $current_object_id = 0)
    {
        // vars

        $selected = in_array( $term->term_id, $this->field['value'] );
        if( $this->field['field_type'] == 'checkbox' )
        {
            $output .= '<li><label class="selectit"><input type="checkbox" name="' . $this->field['name'] . '" value="' . $term->term_id . '" ' . ($selected ? 'checked="checked"' : '') . ' /> ' . $term->name . '</label>';
        }
        elseif( $this->field['field_type'] == 'radio' )
        {
            $output .= '<li><label class="selectit"><input type="radio" name="' . $this->field['name'] . '" value="' . $term->term_id . '" ' . ($selected ? 'checked="checkbox"' : '') . ' /> ' . $term->name . '</label>';
        }
        elseif( $this->field['field_type'] == 'select' )
        {
            if ( 'model' == $term->taxonomy ) {
                $make = get_field( 'make', $term );

                $make_attr = 'data-make="'. $make->name .'"';
            }
            $indent = str_repeat("&mdash;", $depth);
            $output .= '<option ' . $make_attr . ' value="' . $term->term_id . '" ' . ($selected ? 'selected="selected"' : '') . '>' . $indent . ' ' . $term->name . '</option>';
        }

    }
}