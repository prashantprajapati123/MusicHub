<?php 
/**
 * Create Bandcamp Import page.
 * 
 * @uses add_submenu_page()
 * @uses add_action()
 * @since Musopress Discography 0.1
 */ 
function muso_create_bandcamp_page() {
	$page_hook = add_submenu_page( 'edit.php?post_type=muso-album', __( 'Bandcamp Import', 'musopress' ), __( 'Bandcamp Import', 'musopress' ), 'publish_posts', 'muso-bandcamp', 'build_bandcamp_page' );
	add_action( "admin_print_scripts-$page_hook", 'muso_admin_bandcamp_script' );
}

/**
 * Build initial form.
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
function build_bandcamp_page() {
	 ?>

	<div id="bandcamp-wrap" class="wrap">
		<?php screen_icon( 'upload' ); ?>
		<h2><?php _e( 'Bandcamp Import', 'musopress' ); ?></h2>
		<form action="" method="post">
			<?php wp_nonce_field( 'muso-import_albums' ); ?> 
     		<input type="hidden" name="muso_action" value="import" / > 
			<table class="form-table">
				<tr valign="top">
					<td><label for="artist-url"><?php _e( 'Enter the name of the artist to import.', 'musopress' ); ?></label></td>
					<td><input type="text" size="30" name="artist-name" id="artist-name" /></td>
				</tr>
				<tr valign="top">
					<td><input type="submit" value="<?php _e( 'Submit', 'musopress' ); ?>" class="button-secondary" /></td>
				</tr>
			</table>
		</form>
		<?php if ( muso_check_submit() ) 
			return; ?>
	</div>
<?php 
}

/**
 * Check and sanitize form submissions, then run the appropriate function.
 * 
 * @uses current_user_can()
 * @uses wp_die()
 * @uses check_admin_referer()
 * @uses sanitize_text_field()
 * @uses muso_list_artists()
 * @uses muso_list_albums()
 * @uses muso_import_albums()
 * 
 * @since Musopress Discography 0.1
 */ 
function muso_check_submit() {
	
	if ( !current_user_can( 'publish_posts') )
		wp_die( 'You cannot edit these settings.' );
	
	if ( !isset( $_REQUEST['muso_action'] ) ) return;
	
	if (empty($_POST)) return false;
	
	$action = $_REQUEST['muso_action'];
	
	check_admin_referer( 'muso-' . $action . '_albums' );
	
	if ( isset( $_POST['artist-name'] ) ) {
		$artist_name = sanitize_text_field( $_POST['artist-name'] );
		muso_list_artists( $artist_name );		
	}

	if ( isset( $_POST['artist-list'] ) ) {
		if ( !ctype_digit( $_POST['artist-list'] ) ) {
			_e( 'The artist id that was sent is not valid.', 'musopress' );
		} else {
			$artist_id = $_POST['artist-list'];
			muso_list_albums( $artist_id );
		}
	}

	if ( isset( $_POST['album-list'] ) ) {
		$albums = $_POST['album-list'];
		$discography = unserialize( stripslashes( $_POST['discog_array'] ) );
		foreach ( $discography['discography'] as $album ) {
			$album = array_map( 'sanitize_text_field', $album );
			}
		
		muso_import_albums( $discography, $albums );
	}


}

/**
 * List artist search results.
 * 
 * @uses muso_bandcamp_api_call()
 * @uses is_wp_error()
 * @uses wp_nonce_field()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param string $artist_name
 */ 
function muso_list_artists( $artist_name ) {
	
	$artist_name = urlencode( $artist_name ); 
	
	$artists = muso_bandcamp_api_call( $artist_name, 'search' );	
	
	if ( isset( $artists['error'] ) || empty( $artists ) || is_wp_error( $artists ) ) { //Check for errors.
		echo '<div class="error">' . __( 'Sorry, something seems to have gone wrong. Make sure you typed the name correctly.', 'musopress' ) . '</div>';
		if ( isset( $artists['error'] ) ) echo '<p>Bandcamp says: ' . $artists['error_message'] . '</p>';
		if ( empty( $artists ) ) echo '<p>' . __( 'No data was returned.', 'musopress' ) . '</p>';
		if ( is_wp_error( $artists ) ) echo $artists->get_error_message();
	} else { ?>
		<form action="" method="post">
			<?php wp_nonce_field( 'muso-import_albums' ); ?> 
     		<input type="hidden" name="muso_action" value="import" / >
			<table class="form-table">
				<tr><h4><?php _e( 'Select the artist whose albums you would like to import:', 'musopress' ); ?></h4></tr>
			<?php foreach ( $artists['results'] as $artist ) { ?>
				<tr valign="top">
					<td class="checkbox-column"><input type="radio" name="artist-list" value="<?php echo esc_attr( $artist['band_id'] ); ?>" /></td><td><?php echo esc_html( $artist['name'] ) . ' - ' . esc_html( $artist['url'] ); ?></td>		
				</tr>
			<?php } ?>
				<tr valign="top">
					<td><input type="submit" value="<?php _e( 'Submit', 'musopress' ); ?>" class="button-secondary" /></td>
				</tr>
			</table>
		</form>
	<?php	}
}

/**
 * List albums from the selected artist.
 * 
 * @uses muso_bandcamp_api_call()
 * @uses is_wp_error()
 * @uses wp_nonce_field()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param string $artist_id
 */ 
function muso_list_albums( $artist_id ) {
	
	$discography = muso_bandcamp_api_call( $artist_id, 'discography' );
	
	if ( isset( $discography['error'] ) || empty( $discography ) || is_wp_error( $discography ) ) { //Check for errors.
		echo '<div class="error">' . __( "Sorry, something seems to have gone wrong. Might be the Bandcamp servers aren't responding right now. Try again in a bit.", 'musopress' ) . '</div>';
		if ( isset( $discography['error'] ) ) echo '<p>Bandcamp says: ' . $discography['error_message'] . '</p>';
		if ( empty( $discography ) ) echo '<p>' . __( 'No data was returned.', 'musopress' ) . '</p>';
		if ( is_wp_error( $discography ) ) echo $discography->get_error_message();	
	} else {
		$discog_array = serialize( $discography ); ?>
		<form action="" method="post">
			<?php wp_nonce_field( 'muso-import_albums' ); ?> 
     		<input type="hidden" name="muso_action" value="import" / >
     		<input type="hidden" name="discog_array" value='<?php echo esc_attr( $discog_array ) ?>' / >
			<table class="form-table">
				<tr><h4><?php _e( 'Select the albums you would like to import/update:', 'musopress' ); ?></h4></tr>
				<tr valign="top" id="check-all">
					<td><input type="checkbox" id="select-all" name="select-all" value="select-all" /></td>
					<td>Select All</td>
				</tr>
			<?php foreach ( $discography['discography'] as $album ) { 
				
				if ( isset( $album['album_id'] ) ) { //Check if it's an album or a track.
					$album_id = $album['album_id'];
				} elseif ( isset( $album['track_id'] ) ) {
					$album_id = $album['track_id'];
				} ?>
				
				<tr valign="top">
					<td class="checkbox-column"><input type="checkbox" name="album-list[]" value="<?php echo esc_attr( $album_id ); ?>" /></td><td><?php echo esc_html( $album['title'] ); ?></td>		
				</tr>
			<?php } ?>
				<tr valign="top">
					<td><input type="submit" value="<?php _e( 'Import', 'musopress' ); ?>" class="button-primary" /></td>
				</tr>
			</table>
		</form> <?php
	}

}

/**
 * Import Albums.
 * 
 * @uses $wpdb
 * @uses muso_bandcamp_api_call()
 * @uses muso_insert_bc_post()
 * @uses muso_create_bc_widget()
 * @uses update_post_meta()
 * @uses muso_get_album_cover()
 * @uses is_wp_error()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param array $discography
 * @param array $albums
 */ 
function muso_import_albums( $discography, $albums ) {
	global $wpdb;

	$error = false;
	$post_count = 0;
	$post_update_count = 0;

	$sql = "SELECT post_id, meta_value
			FROM $wpdb->postmeta
			WHERE meta_key = 'bandcamp-id'"; //Get the existing albums posts to see if any already exist.
			
	$posted_albums = $wpdb->get_results( $sql, ARRAY_A );
	foreach ( $discography['discography'] as $album ) { //Loop through all the albums in the discography.
		
		if ( isset( $album['album_id'] ) ) { //Check if it's a track or an album and make appropriate changes.
			$album_id = $album['album_id'];
			$api_type = 'album';
			$widget_url = 'album=' . $album_id;
		} else {
			$album_id = $album['track_id'];
			$api_type = 'track';
			$widget_url = 'track=' . $album_id;
		}
		
		if ( in_array( $album_id, $albums ) ) { //Check if this album is one of the ones we want to import.
			
			$artist = $album['artist'];
			
			$album_info = muso_bandcamp_api_call( $album_id, $api_type );
			
			if ( isset( $album_info['error_message'] ) || empty( $album_info ) || is_wp_error( $album_info ) ) { //Check for errors.
				$error = true;
				break;
			} else {
					
				//Check if an album with the same id already exists. If so, update it...
				$insert_return = muso_insert_bc_post( $album_info, $posted_albums, $artist, $album_id );
				
				$post_id = $insert_return['post_id'];
				
				if ( 'update' == $insert_return['post_count'] )
					$post_update_count++;
				elseif ( 'insert' == $insert_return['post_count'] )
					$post_count++;
				
				//Check for color options and number of tracks to modify the widget accordingly.
				$widget_code = muso_create_bc_widget( $album_info, $album, $widget_url, $artist );
				
				update_post_meta( $post_id, 'embed-code', $widget_code );
				update_post_meta( $post_id, 'bandcamp-id', $album_id );
					
				if ( isset( $album_info['large_art_url'] ) ) { //if an album cover exists, get it and add it as post thumbnail.
					
					$return = muso_get_album_cover( $album_info['large_art_url'], $album_info['title'], $post_id );
					$error = $return['error'];
					$photo = $return['photo'];
				}		
			}
		}
	}

	if ( true == $error ) { //Check for errors. Output message if succesful.
		echo '<div class="error">' . __( 'Oops, looks like something went wrong while importing the albums. If you have a lot of albums, try importing a few at a time.', 'musopress' ) . '</div>';
		if ( isset( $album_info['error'] ) ) echo '<p>Bandcamp says: ' . $album_info['error_message'] . '</p>';
		if ( empty( $album_info ) ) echo '<p>' . __( 'No data was returned.', 'musopress' ) . '</p>';
		if ( is_wp_error( $album_info ) ) echo $album_info->get_error_message();
		if ( isset( $photo ) && is_wp_error( $photo ) ) echo $photo->get_error_message();
	} else {
		
		if ( 0 == $post_update_count && 0 == $post_count )
			echo '<div class="updated">' . __( 'No albums were imported.', 'musopress' ) . '</div>';
		else {
				
			if ( 1 <= $post_count ) {
				echo '<div class="updated">';
				printf( _n( '%d album was imported succesfully.', '%d albums were imported succesfully.', $post_count, 'musopress' ), $post_count );
				echo '</div>';
			}
				
			if ( 1 <= $post_update_count ) {
				echo '<div class="updated">';
				printf( _n( '%d album was updated succesfully.', '%d albums were updated succesfully.', $post_update_count, 'musopress' ), $post_update_count );
				echo '</div>';
			}				
		}	
	}
}

/**
 * Runs the API call to bandcamp.
 * 
 * @uses wp_remote_get()
 * @uses wp_remote_retrieve_body()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param string $param
 * @param string $type
 * @return array
 */ 
function muso_bandcamp_api_call( $param, $type ) {
	$api_key = 'samvinnaritutlaaptarlapustr';
	
	if ( $type == 'search' )
		$api_url = 'http://api.bandcamp.com/api/band/3/search?key=' . $api_key . '&name=' . $param;
	elseif ( $type == 'discography' )
		$api_url = 'http://api.bandcamp.com/api/band/3/discography?key=' . $api_key . '&band_id=' . $param;
	elseif ( 'album' == $type )
		$api_url = 'http://api.bandcamp.com/api/album/2/info?key=' . $api_key . '&album_id=' . $param;
	elseif ( 'track' == $type )
		$api_url = 'http://api.bandcamp.com/api/track/1/info?key=' . $api_key . '&track_id=' . $param;
	
	$api_response = wp_remote_get( $api_url, array( 'timeout' => 30 )  );
	$json = wp_remote_retrieve_body( $api_response );
	$json = json_decode( $json, true ); 
	
	return $json;
}

/**
 * Sideloads the album cover and attaches it as a post thumbnail.
 * 
 * @uses wp_remote_get()
 * @uses wp_remote_retrieve_body()
 * @uses is_wp_error()
 * @uses wp_upload_bits()
 * @uses wp_check_filetype()
 * @uses wp_insert_attachment()
 * @uses wp_generate_attachment_metadata()
 * @uses wp_update_attachment_metadata()
 * @uses set_post_thumbnail()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param string $url
 * @param string $title
 * @param int    $post_id
 * @return array
 */ 
function muso_get_album_cover( $url, $title, $post_id ) {
	
	$error = false;
	
	$response = wp_remote_get( $url, array( 'timeout' => 30 ) );
	$photo = wp_remote_retrieve_body( $response );	
	
	if ( is_wp_error( $photo ) ){
		$error = true;
	} else {
	
		$attachment = wp_upload_bits( $title . '.jpg', '', $photo );  //copy image file to uploads folder.

		$filetype = wp_check_filetype( basename( $attachment['file'] ), null );

		$postinfo = array(
				'post_mime_type'	=> $filetype['type'],
				'post_title'		=> $title,
				'post_content'	=> '',
				'post_status'	=> 'inherit',
			);
		$filename = $attachment['file'];
		$attach_id = wp_insert_attachment( $postinfo, $filename, $post_id ); //add the image as an attachment.
		
		if( !function_exists( 'wp_generate_attachment_data' ) )
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id,  $attach_data ); //add metadata and create thumbnails.
		
		set_post_thumbnail( $post_id, $attach_id ); //set the image as the post thumbnail.
		
	}
	
	$return = array( 'error' => $error, 'photo' => $photo );
	return $return;
	
}

/**
 * Insert or Update the Discography post accordingly.
 * 
 * @uses wpautop()
 * @uses muso_array_search()
 * @uses wp_update_post()
 * @uses get_option()
 * @uses wp_set_post_terms()
 * @uses wp_insert_post()
 * @uses delete_post_meta()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param array  $json
 * @param array  $posted_albums
 * @param string $artist
 * @param string $album_id
 * @return string
 */ 
function muso_insert_bc_post( $json, $posted_albums, $artist, $album_id ) {
		
	$release_date = date('Y-m-d H:i:s', $json['release_date']);
				
	if ( isset( $json['about'] ) ) //Get the about and credits texts and combine them, if they exist.
		$about = wpautop( $json['about'] );
	else
		$about = '';
		
	if ( isset( $json['credits'] ) )
		$about .= '<h3>Credits:</h3>' . wpautop( $json['credits'] );

	if ( isset( $posted_albums ) && muso_array_search ( $album_id, $posted_albums ) ) {
		foreach ( $posted_albums as $posted_album ) {
			if ( $album_id == $posted_album[ 'meta_value' ] ) {
				$post_id = $posted_album[ 'post_id' ];
			} 	
		}

		
		$my_post = array();
		$my_post['ID'] = $post_id;
		$my_post['post_title'] = $json['title'];
		$my_post['post_content'] = $about;
		
		wp_update_post( $my_post );
		$post_count = 'update';
		
		$options = get_option('es_md_options');
		if ( isset( $options['muso_enable_artist'] ) && '' != $options['muso_enable_artist'] )
			wp_set_post_terms( $post_id, $artist, 'artist', false);
		
	} else { // ...otherwise, make a new post.

		$my_post = array( 
				'post_title' => $json['title'],
				'post_content' => $about,
				'post_date' => $release_date,
				'post_status' => 'publish',
				'post_type' => 'muso-album' );
				
		$post_id = wp_insert_post( $my_post );
		$post_count = 'insert';
		
		$options = get_option('es_md_options');
		if ( isset( $options['muso_enable_artist'] ) && '' != $options['muso_enable_artist'] )
			wp_set_post_terms( $post_id, $artist, 'artist', false);
		
	}
	
	$return = array( 'post_id' => $post_id, 'post_count' => $post_count );
	return $return;
}

/**
 * Builds the Bandcamp widget code.
 * 
 * @uses get_option()
 * 
 * @since Musopress Discography 0.1
 * 
 * @param array  $json
 * @param array  $album
 * @param string $widget_url
 * @param string $artist
 * @return string
 */ 
function muso_create_bc_widget( $json, $album, $widget_url, $artist  ) {
	$options = get_option('es_md_options');
				
	if ( isset( $options['muso_bc_color'] ) && '' != $options['muso_bc_color'] ) {
		$bg_color = str_replace( '#', '', $options['muso_bc_color'] );
	} else {
		$bg_color = 'ffffff';
	}
	
	
	if ( isset( $options['muso_bc_link_color'] ) && '' != $options['muso_bc_link_color'] ) {
		$link_color = str_replace( '#', '', $options['muso_bc_link_color'] );
	} else {
		$link_color = '566C9C';
	}
	
	if ( isset( $album['album_id'] ) ) {
		$track_count = count( $json['tracks'] );		
	} elseif ( isset( $album['track_id'] ) ) {
		$track_count = 1;
	}
	
	if ( 2 >= $track_count ) { //Changes the widget height depending on how many tracks the album has.
		$widget_height = '200px';
	} else if ( 2 < $track_count && 4 >= $track_count ) {
		$widget_height = '240px';
	} else if ( 4 < $track_count && 6 >= $track_count ) {
		$widget_height = '280px';
	} else {
		$widget_height = '355px';
	}
	
	$widget_code = '<iframe style="position: relative; display: block; width: 300px; height:' . $widget_height . '" ';
	$widget_code .= 'src="http://bandcamp.com/EmbeddedPlayer/v=2/' . $widget_url;
	$widget_code .= '/size=grande2/bgcol=' . $bg_color . '/linkcol=' . $link_color . '/" allowtransparency="true" frameborder="0"><a href="';
	$widget_code .= $json['url'] . '">' . $json['title'] . ' by ' . $artist . '</a></iframe>';
	
	return $widget_code;
}

/**
 * Recursive array search.
 * 
 * @since Musopress Discography 0.1
 * 
 * @param string  $needle
 * @param array   $haystack
 * @param boolean $strict
 * @param array   $path
 */ 
function muso_array_search( $needle, $haystack, $strict=false, $path=array() ) {
    if( !is_array($haystack) ) {
        return false;
    }
 
    foreach( $haystack as $key => $val ) {
        if( is_array($val) && $subPath = muso_array_search($needle, $val, $strict, $path) ) {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
            $path[] = $key;
            return $path;
        }
    }
    return false;
}



?>