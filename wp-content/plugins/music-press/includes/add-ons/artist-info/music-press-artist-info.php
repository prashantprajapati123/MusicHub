<?php
/**
 * Name: Artist Info Add-ons
 * Plugin URI: http://www.templaza.com/
 * Description: Add more information for Artist as: Real name, Birthday, website...
 * Author: tuyennv
 * Version: 1.0
 * Author URI: http://www.templaza.com/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!defined('TZ_ARTIST_ADDON_URL'))
	define('TZ_ARTIST_ADDON_URL', untrailingslashit(plugins_url('', __FILE__)));

define('TZ_ARTIST_PLACEHOLDER',  TZ_ARTIST_ADDON_URL."/images/link.png");
add_action('admin_init', 'tz_artist_init');
function tz_artist_init() {
$tz_taxonomy = 'artist';
	add_action($tz_taxonomy.'_add_form_fields', 'tz_add_artist_taxonomy_field');
	add_action($tz_taxonomy.'_edit_form_fields', 'tz_edit_artist_taxonomy_field');

    add_filter($tz_taxonomy.'_edit_form_fields', 'tz_taxonomy_artist_description');
    add_filter($tz_taxonomy.'_add_form_fields', 'tz_taxonomy_artist_add_description');

    remove_filter( 'pre_term_description', 'wp_filter_kses' );

    remove_filter( 'pre_link_description', 'wp_filter_kses' );

    remove_filter( 'pre_link_notes', 'wp_filter_kses' );

    remove_filter( 'term_description', 'wp_kses_data' );

    add_action('admin_head', 'tz_remove_default_artist_description');
}

function tz_taxonomy_artist_add_description(){
    $settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );
    ?>
    <div class="form-field">
        <label for="album_created"><?php echo esc_html__('Description','music-press');?></label>
        <?php wp_editor(html_entity_decode('' , ENT_QUOTES, 'UTF-8'), 'cat_description', $settings); ?>
    </div>
    <?php
}
function tz_taxonomy_artist_description($tag)
{
    ?>
    <table class="form-table">
        <tr class="form-field">
            <th scope="row" valign="top"><label for="description"><?php echo esc_html__('Description', 'music-press'); ?></label></th>
            <td>
                <?php
                $settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );
                wp_editor(html_entity_decode($tag->description , ENT_QUOTES, 'UTF-8'), 'cat_description', $settings);
                ?>
                <br />
                <span class="description"><?php echo  esc_html__('The description is not prominent by default; however, some themes may show it.','music-press'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}
function tz_remove_default_artist_description()
{
    global $current_screen;
    if ( $current_screen->id == 'edit-artist' )
    {
        ?>
        <script type="text/javascript">
            jQuery(function(jQuery) {
                jQuery('textarea#tag-description').parents('.form-field').remove();
                jQuery('textarea#description').parents('.form-field').remove();

                jQuery( '#addtag' ).on( 'mousedown', '#submit', function() {
                    tinyMCE.triggerSave();
                });
            });
        </script>
        <?php
    }
}
// add image field in add form
function tz_add_artist_taxonomy_field() {
	
	echo '
	<div class="form-field">
		<label for="artist_real_name">' . __('Real Name', 'music-press') . '</label>
		<input type="text" name="artist_real_name" id="artist_real_name" value="" />
	</div>
	<div class="form-field">
		<label for="artist_birthday">' . __('Birthday', 'music-press') . '</label>
		<input type="text" name="artist_birthday" id="artist_birthday" value="" />
	</div>
	<div class="form-field">
		<label for="artist_occupation">' . __('Occupation', 'music-press') . '</label>
		<input type="text" name="artist_occupation" id="artist_occupation" value="" />
	</div>
	<div class="form-field">
		<label for="artist_instruments">' . __('Instruments', 'music-press') . '</label>
		<input type="text" name="artist_instruments" id="artist_instruments" value="" />
	</div>
	<div class="form-field">
		<label for="artist_website">' . __('Website', 'music-press') . '</label>
		<input type="text" name="artist_website" id="artist_website" value="" />
	</div>

	';
}

// add image field in edit form
function tz_edit_artist_taxonomy_field($taxonomy) {
	
	if (tz_taxonomy_artist_info( $taxonomy->term_id, NULL, TRUE ) == TZ_ARTIST_PLACEHOLDER)
		$tz_custom_link = "";
	else
		$tz_custom_link = tz_taxonomy_artist_info( $taxonomy->term_id, NULL, TRUE );
	echo '
	<tr class="form-field">
		<th scope="row" valign="top"><label for="artist_real_name">' . __('Real Name', 'music-press') . '</label></th>
		<td><input type="text" name="artist_real_name" id="artist_real_name" value="'.$tz_custom_link['realname'].'" /><br />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="artist_birthday">' . __('Birthday', 'music-press') . '</label></th>
		<td><input type="text" name="artist_birthday" id="artist_birthday" value="'.$tz_custom_link['birthday'].'" /><br />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="artist_occupation">' . __('Occupation', 'music-press') . '</label></th>
		<td><input type="text" name="artist_occupation" id="artist_occupation" value="'.$tz_custom_link['occupation'].'" /><br />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="artist_instruments">' . __('Instruments', 'music-press') . '</label></th>
		<td><input type="text" name="artist_instruments" id="artist_instruments" value="'.$tz_custom_link['instruments'].'" /><br />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="artist_website">' . __('Website', 'music-press') . '</label></th>
		<td><input type="text" name="artist_website" id="artist_website" value="'.$tz_custom_link['website'].'" /><br />
		</td>
	</tr>
	';
}

// save our taxonomy image while edit or save term
add_action('edit_term','tz_artist_save_taxonomy');
add_action('create_term','tz_artist_save_taxonomy');
function tz_artist_save_taxonomy($term_id) {
    if(isset($_POST['artist_real_name']))
        update_option('tz_artist_real_name'.$term_id, $_POST['artist_real_name']);
	if(isset($_POST['artist_birthday']))
        update_option('tz_artist_birthday'.$term_id, $_POST['artist_birthday']);
	if(isset($_POST['artist_occupation']))
        update_option('tz_artist_occupation'.$term_id, $_POST['artist_occupation']);
	if(isset($_POST['artist_instruments']))
        update_option('tz_artist_instruments'.$term_id, $_POST['artist_instruments']);
	if(isset($_POST['artist_website']))
        update_option('tz_artist_website'.$term_id, $_POST['artist_website']);
}
// get taxonomy image url for the given term_id (Place holder image by default)
function tz_taxonomy_artist_info($term_id = NULL, $size = NULL, $return_placeholder = FALSE) {
	if (!$term_id) {
		if (is_category())
			$term_id = get_query_var('cat');
		elseif (is_tax()) {
			$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
			$term_id = $current_term->term_id;
		}
	}
	
    $taxonomy_artist_info['realname']  	= get_option('tz_artist_real_name'.$term_id);
    $taxonomy_artist_info['birthday'] 	= get_option('tz_artist_birthday'.$term_id);
    $taxonomy_artist_info['occupation'] = get_option('tz_artist_occupation'.$term_id);
    $taxonomy_artist_info['instruments']= get_option('tz_artist_instruments'.$term_id);
    $taxonomy_artist_info['website'] 	= get_option('tz_artist_website'.$term_id);

    return $taxonomy_artist_info;
}

