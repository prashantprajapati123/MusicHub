<?php
/**
 * Plugin Name: Categories Date Created
 * Plugin URI: http://www.templaza.com/
 * Description: Categories Date Created Plugin allow you to add an link to category or any custom term.
 * Author: tuyennv
 * Version: 1.0
 * Author URI: http://www.templaza.com/
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
if (!defined('TZ_ADDONS_URL'))
	define('TZ_ADDONS_URL', untrailingslashit(plugins_url('', __FILE__)));

define('TZ_ALBUM_PLACEHOLDER',  TZ_ADDONS_URL."/images/link.png");
add_action('admin_init', 'tz_album_init');

function tz_album_init() {
$tz_taxonomy = 'album';	
	wp_enqueue_style('jquery-style', ''.TZ_ADDONS_URL.'/jquery-ui.css');

	add_action($tz_taxonomy.'_add_form_fields', 'tz_add_taxonomy_field');
	add_action($tz_taxonomy.'_edit_form_fields', 'tz_edit_taxonomy_field');

	add_filter($tz_taxonomy.'_edit_form_fields', 'tz_taxonomy_description');
	add_filter($tz_taxonomy.'_add_form_fields', 'tz_taxonomy_add_description');

	remove_filter( 'pre_term_description', 'wp_filter_kses' );

	remove_filter( 'pre_link_description', 'wp_filter_kses' );

	remove_filter( 'pre_link_notes', 'wp_filter_kses' );

	remove_filter( 'term_description', 'wp_kses_data' );

	add_action('admin_head', 'tz_remove_default_category_description');

}
function tz_taxonomy_add_description(){
	$settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );
	?>
	<div class="form-field">
		<label for="album_created"><?php echo esc_html__('Description','music-press');?></label>
		<?php wp_editor(html_entity_decode('' , ENT_QUOTES, 'UTF-8'), 'cat_description', $settings); ?>
	</div>
	<?php
}
function tz_taxonomy_description($tag)
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
				<span class="description"><?php echo esc_html__('The description is not prominent by default; however, some themes may show it.','music-press'); ?></span>
			</td>
		</tr>
	</table>
	<?php
}
function tz_remove_default_category_description()
{
	global $current_screen;
	if ( $current_screen->id == 'edit-album' )
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
function tz_add_taxonomy_field($tag) {
	$date_add = date('d-m-y');
	echo '
	<div class="form-field">
		<label for="album_type">' . __('Album Type', 'music-press') . '</label>
		<input type="radio" name="album_type" id="album_type" value="audio" checked/>' . __('Audio', 'music-press') . '
		<input type="radio" name="album_type" id="album_type" value="video"/>' . __('Video', 'music-press') . '
	</div>
	<div class="form-field">
		<label for="album_created">' . __('Date Created', 'music-press') . '</label>
		<input type="text" name="album_created" id="album_created" value="'.$date_add.'" />
	</div>
	<div class="form-field">
		<label for="album_created">' . __('Short Description', 'music-press') . '</label>
		<textarea name="album_short_description" id="album_short_description" rows="5" cols="40"></textarea>
	</div>
	';
}

// add image field in edit form
function tz_edit_taxonomy_field($taxonomy) {
	if (tz_taxonomy_album_info( $taxonomy->term_id, NULL, TRUE ) == TZ_ALBUM_PLACEHOLDER)
		$tz_album_info = "";
	else
		$tz_album_info = tz_taxonomy_album_info( $taxonomy->term_id, NULL, TRUE );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="album_type"><?php  echo esc_html__('Album Type', 'music-press') ?></label></th>
		<td>
		<input type="radio" name="album_type" id="album_type" value="audio" <?php if($tz_album_info['type']=='audio'){ echo "checked";} ?> /><?php  echo esc_html__('Audio', 'music-press')?>
		<input type="radio" name="album_type" id="album_type" value="video" <?php if($tz_album_info['type']=='video'){ echo "checked";} ?>/><?php  echo esc_html__('Video', 'music-press')?>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="album_created"><?php  echo esc_html__('Date Created', 'music-press') ?></label></th>
		<td><input type="text" name="album_created" id="album_created" value="<?php  echo esc_attr($tz_album_info['created_default']);?>" /><br />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="album_created"><?php  echo esc_html__('Short Description', 'music-press') ?></label></th>
		<td>
			<textarea name="album_short_description" id="album_short_description" rows="5" cols="40"><?php  echo esc_attr($tz_album_info['short_description']);?></textarea>
		</td>
	</tr>
	<?php
}
// save our taxonomy image while edit or save term
add_action('edit_term','tz_album_save_taxonomy');
add_action('create_term','tz_album_save_taxonomy');
function tz_album_save_taxonomy($term_id) {
    if(isset($_POST['album_created'])) {
		update_option('tz_album_created' . $term_id, $_POST['album_created']);
	}
    if(isset($_POST['album_type'])) {
		update_option('tz_album_type' . $term_id, $_POST['album_type']);
	}
    if(isset($_POST['album_short_description'])) {
		update_option('short_description' . $term_id, $_POST['album_short_description']);
	}
}
// get taxonomy image url for the given term_id (Place holder image by default)
function tz_taxonomy_album_info($term_id = NULL, $size = NULL, $return_placeholder = FALSE) {
	if (!$term_id) {
		if (is_category())
			$term_id = get_query_var('cat');
		elseif (is_tax()) {
			$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
			$term_id = $current_term->term_id;
		}
	}
	$date_format =get_option('date_format');
    $taxonomy_album_info['created_default']  	= get_option('tz_album_created'.$term_id);
    $taxonomy_album_info['created']  	= date($date_format,strtotime(get_option('tz_album_created'.$term_id)));
	$taxonomy_album_info['type'] 	= get_option('tz_album_type'.$term_id);
	$taxonomy_album_info['short_description'] 	= get_option('short_description'.$term_id);
    return $taxonomy_album_info;
}