<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('admin_init', 'tz_music_press_init');
define('TZ_MUSIC_PRESS_NO_IMAGE', TZ_MUSIC_PRESS_PLUGIN_URL."/assets/images/no_image.png");
function tz_music_press_init() {
    $music_press_taxonomies = array('album','genre', 'artist');
    if (is_array($music_press_taxonomies)) {
        foreach ($music_press_taxonomies as $music_press_taxonomy) {

            add_action($music_press_taxonomy.'_add_form_fields', 'tz_music_press_texonomy_field');
            add_action($music_press_taxonomy.'_edit_form_fields', 'tz_music_press_edit_texonomy_field');
            add_filter( 'manage_edit-' . $music_press_taxonomy . '_columns', 'tz_music_press_taxonomy_columns' );
            add_filter( 'manage_' . $music_press_taxonomy . '_custom_column', 'tz_music_press_taxonomy_column', 10, 3 );
        }
    }
}

// add image field in add form
function tz_music_press_texonomy_field() {
    if (get_bloginfo('version') >= 3.5)
        wp_enqueue_media();
    else {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
    }

    echo '<div class="form-field">
		<label for="taxonomy_image">' . __('Image', 'categories-images') . '</label>
		<input type="text" name="taxonomy_image" id="taxonomy_image" value="" />
		<br/>
		<button class="music_press_upload_image_button button">' . __('Upload/Add image', 'categories-images') . '</button>
	</div>'.tz_music_press_script();
}

// add image field in edit form
function tz_music_press_edit_texonomy_field($taxonomy) {
    if (get_bloginfo('version') >= 3.5)
        wp_enqueue_media();
    else {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
    }

    if (tz_music_press_taxonomy_image_url( $taxonomy->term_id, NULL, TRUE ) == TZ_MUSIC_PRESS_NO_IMAGE)
        $image_url = "";
    else
        $image_url = tz_music_press_taxonomy_image_url( $taxonomy->term_id, NULL, TRUE );
    echo '<tr class="form-field">
		<th scope="row" valign="top"><label for="taxonomy_image">' . __('Image', 'categories-images') . '</label></th>
		<td><img class="taxonomy-image" src="' . tz_music_press_taxonomy_image_url( $taxonomy->term_id, 'medium', TRUE ) . '"/><br/><input type="text" name="taxonomy_image" id="taxonomy_image" value="'.$image_url.'" /><br />
		<button class="music_press_upload_image_button button">' . __('Upload/Add image', 'categories-images') . '</button>
		<button class="music_press_remove_image_button button">' . __('Remove image', 'categories-images') . '</button>
		</td>
	</tr>'.tz_music_press_script();
}

// upload using wordpress upload
function tz_music_press_script() {
    return '<script type="text/javascript">
	    jQuery(document).ready(function($) {
			var wordpress_ver = "'.get_bloginfo("version").'", upload_button;
			$(".music_press_upload_image_button").click(function(event) {
				upload_button = $(this);
				var frame;
				if (wordpress_ver >= "3.5") {
					event.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media();
					frame.on( "select", function() {
						// Grab the selected attachment.
						var attachment = frame.state().get("selection").first();
						frame.close();
						if (upload_button.parent().prev().children().hasClass("tax_list")) {
							upload_button.parent().prev().children().val(attachment.attributes.url);
							upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
						}
						else
							$("#taxonomy_image").val(attachment.attributes.url);
					});
					frame.open();
				}
				else {
					tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
					return false;
				}
			});

			$(".music_press_remove_image_button").click(function() {
				$(".taxonomy-image").attr("src", "'.TZ_MUSIC_PRESS_NO_IMAGE.'");
				$("#taxonomy_image").val("");
				$(this).parent().siblings(".title").children("img").attr("src","' . TZ_MUSIC_PRESS_NO_IMAGE . '");
				$(".inline-edit-col :input[name=\'taxonomy_image\']").val("");
				return false;
			});

			if (wordpress_ver < "3.5") {
				window.send_to_editor = function(html) {
					imgurl = $("img",html).attr("src");
					if (upload_button.parent().prev().children().hasClass("tax_list")) {
						upload_button.parent().prev().children().val(imgurl);
						upload_button.parent().prev().prev().children().attr("src", imgurl);
					}
					else
						$("#taxonomy_image").val(imgurl);
					tb_remove();
				}
			}

			$(".editinline").click(function() {
			    var tax_id = $(this).parents("tr").attr("id").substr(4);
			    var thumb = $("#tag-"+tax_id+" .thumb img").attr("src");

				if (thumb != "' . TZ_MUSIC_PRESS_NO_IMAGE . '") {
					$(".inline-edit-col :input[name=\'taxonomy_image\']").val(thumb);
				} else {
					$(".inline-edit-col :input[name=\'taxonomy_image\']").val("");
				}

				$(".inline-edit-col .title img").attr("src",thumb);
			});
	    });
	</script>';
}

// save our taxonomy image while edit or save term
add_action('edit_term','tz_music_press_save_taxonomy_image');
add_action('create_term','tz_music_press_save_taxonomy_image');
function tz_music_press_save_taxonomy_image($term_id) {
    if(isset($_POST['taxonomy_image']))
        update_option('music_press_taxonomy_image'.$term_id, $_POST['taxonomy_image'], NULL);
}

// get attachment ID by image url
function tz_music_press_get_attachment_id_by_url($image_src) {
    global $wpdb;
    $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid = %s", $image_src);
    $id = $wpdb->get_var($query);
    return (!empty($id)) ? $id : NULL;
}

// get taxonomy image url for the given term_id (Place holder image by default)
function tz_music_press_taxonomy_image_url($term_id = NULL, $size = 'full', $return_placeholder = FALSE) {
    if (!$term_id) {
        if (is_category())
            $term_id = get_query_var('cat');
        elseif (is_tax()) {
            $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $term_id = $current_term->term_id;
        }
    }

    $taxonomy_image_url = get_option('music_press_taxonomy_image'.$term_id);
    if(!empty($taxonomy_image_url)) {
        $attachment_id = tz_music_press_get_attachment_id_by_url($taxonomy_image_url);
        if(!empty($attachment_id)) {
            $taxonomy_image_url = wp_get_attachment_image_src($attachment_id, $size);
            $taxonomy_image_url = $taxonomy_image_url[0];
        }
    }

    if ($return_placeholder)
        return ($taxonomy_image_url != '') ? $taxonomy_image_url : TZ_MUSIC_PRESS_NO_IMAGE;
    else
        return $taxonomy_image_url;
}

function tz_music_press_quick_edit_custom_box($column_name, $screen, $name) {
    if ($column_name == 'thumb')
        echo '<fieldset>
		<div class="thumb inline-edit-col">
			<label>
				<span class="title"><img src="" alt="Thumbnail"/></span>
				<span class="input-text-wrap"><input type="text" name="taxonomy_image" value="" class="tax_list" /></span>
				<span class="input-text-wrap">
					<button class="music_press_upload_image_button button">' . __('Upload/Add image', 'categories-images') . '</button>
					<button class="music_press_remove_image_button button">' . __('Remove image', 'categories-images') . '</button>
				</span>
			</label>
		</div>
	</fieldset>';
}

/**
 * Thumbnail column added to category admin.
 *
 * @access public
 * @param mixed $columns
 * @return void
 */
function tz_music_press_taxonomy_columns( $columns ) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['thumb'] = __('Image', 'categories-images');

    unset( $columns['cb'] );

    return array_merge( $new_columns, $columns );
}

/**
 * Thumbnail column value added to category admin.
 *
 * @access public
 * @param mixed $columns
 * @param mixed $column
 * @param mixed $id
 * @return void
 */
function tz_music_press_taxonomy_column( $columns, $column, $id ) {
    if ( $column == 'thumb' )
        $columns = '<span><img src="' . tz_music_press_taxonomy_image_url($id, 'thumbnail', TRUE) . '" alt="' . __('Thumbnail', 'categories-images') . '" class="wp-post-image" /></span>';

    return $columns;
}

// Change 'insert into post' to 'use this image'
function tz_music_press_change_insert_button_text($safe_text, $text) {
    return str_replace("Insert into Post", "Use this image", $text);
}

// Style the image in category list
if ( strpos( $_SERVER['SCRIPT_NAME'], 'edit-tags.php' ) > 0 ) {
    add_action('quick_edit_custom_box', 'tz_music_press_quick_edit_custom_box', 10, 3);
    add_filter("attribute_escape", "tz_music_press_change_insert_button_text", 10, 2);
}

// display taxonomy image for the given term_id
function music_press_taxonomy_image($term_id = NULL, $size = 'full', $attr = NULL, $echo = TRUE) {
    if (!$term_id) {
        if (is_category())
            $term_id = get_query_var('cat');
        elseif (is_tax()) {
            $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $term_id = $current_term->term_id;
        }
    }

    $taxonomy_image_url = get_option('music_press_taxonomy_image'.$term_id);
    if(!empty($taxonomy_image_url)) {
        $attachment_id = tz_music_press_get_attachment_id_by_url($taxonomy_image_url);
        if(!empty($attachment_id))
            $taxonomy_image = wp_get_attachment_image($attachment_id, $size, FALSE, $attr);
        else {
            $image_attr = '';
            if(is_array($attr)) {
                if(!empty($attr['class']))
                    $image_attr .= ' class="'.$attr['class'].'" ';
                if(!empty($attr['alt']))
                    $image_attr .= ' alt="'.$attr['alt'].'" ';
                if(!empty($attr['width']))
                    $image_attr .= ' width="'.$attr['width'].'" ';
                if(!empty($attr['height']))
                    $image_attr .= ' height="'.$attr['height'].'" ';
                if(!empty($attr['title']))
                    $image_attr .= ' title="'.$attr['title'].'" ';
            }
            $taxonomy_image = '<img src="'.$taxonomy_image_url.'" '.$image_attr.'/>';
        }
    }

    if ($echo)
        echo $taxonomy_image;
    else
        return $taxonomy_image;
}