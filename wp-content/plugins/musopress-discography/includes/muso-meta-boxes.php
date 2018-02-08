<?php 
/**
 * Add Meta Box for Discography posts.
 * 
 * @uses add_meta_box()
 * 
 * @since Musopress Discography 0.1
 */
function muso_add_custom_meta_boxes() {
	add_meta_box( 'muso', __( 'Embed-Code for Music Widget', 'musopress' ), 'muso_album_embed' , 'muso-album' );
} 

/**
 * Create Meta Box.
 * 
 * @uses wp_create_nonce()
 * @uses get_post_meta()
 * @uses esc_textarea()
 * 
 * @since Musopress Discography 0.1
 */ 
function muso_album_embed() {
	global $post;
 
		// Noncename needed to verify where the data originated
    echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
 
    // Get the custom data if its already been entered
    $embed_code = get_post_meta( $post->ID, 'embed-code', true );
 
    // Echo out the field
    echo '<textarea name="embed-code" rows="16" cols="60">' . esc_textarea( $embed_code ) . '</textarea>';
 
}

/**
 * Save data from Meta Box.
 * 
 * @uses wp_verify_nonce()
 * @uses plugin_basename()
 * @uses current_user_can()
 * @uses get_post_meta()
 * @uses update_post_meta()
 * @uses add_post_meta()
 * @uses delete_post_meta()
 * 
 * @since Musopress Discography 0.1
 */ 
function muso_save_meta( $post_id, $post ) {
 
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['eventmeta_noncename'] ) || !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
	    return $post->ID;
	}
	 
	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	 
	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
    $events_meta['embed-code'] = $_POST['embed-code'];
    	 
    // Add values of $events_meta as custom fields
    foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
        if ( 'revision' == $post->post_type ) return; // Don't store custom data twice
        	$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if ( get_post_meta( $post->ID, $key, FALSE ) ) { // If the custom field already has a value
            update_post_meta( $post->ID, $key, $value );
        } else { // If the custom field doesn't have a value
            add_post_meta( $post->ID, $key, $value );
        }
        if ( !$value ) delete_post_meta( $post->ID, $key ); // Delete if blank
    }
    
}


?>