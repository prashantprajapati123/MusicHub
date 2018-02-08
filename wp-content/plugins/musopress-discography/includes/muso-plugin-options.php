<?php 

/**
 * Add Options Page.
 * 
 * @uses add_options_page()
 * 
 * @since Musopress Discography 0.1
 */
function muso_create_plugin_options_page() {
	add_options_page( __( 'Musopress Discography Options', 'musopress' ), __( 'Discography', 'musopress' ), 'manage_options', 'musopress-plugin-options', 'muso_build_options_page' );
}

/**
 * Create Options Page.
 * 
 * @uses screen_icon()
 * @uses settings_fields()
 * @uses do_settings_sections()
 * 
 * @since Musopress Discography 0.1
 */
function muso_build_options_page() {
?>
	<div id="theme-options-wrap" class="wrap">
    	<?php screen_icon( 'themes' ); ?>

    	<h2><?php _e( 'Musopress Discography Options', 'musopress' ); ?></h2>
    	<p><?php _e( '', 'musopress' ); ?></p>

    	<form method="post" action="options.php" enctype="multipart/form-data">
			<?php  settings_fields( 'es_md_options_group' ); ?>
	  		<?php do_settings_sections( 'musopress-plugin-options' ); ?>
	      	<p class="submit">
	        	<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes','musopress' ); ?>" />
	      	</p>
    	</form>
  	</div>
<?php
}

/**
 * Add Fields for Options Page.
 * 
 * @uses register_setting()
 * @uses add_settings_section()
 * @uses add_settings_field()
 * 
 * @since Musopress Discography 0.1
 */
function muso_register_and_build_fields() {
	 
	register_setting( 'es_md_options_group', 'es_md_options', 'muso_validate_setting' );
		  
	add_settings_section( 'main_section', __( '', 'musopress' ), 'muso_section_cb', 'musopress-plugin-options' );
			 
	add_settings_field( 'muso_bc_color', __( 'Background Color:', 'musopress' ), 'muso_bc_color_setting', 'musopress-plugin-options', 'main_section' );
	add_settings_field( 'muso_bc_link_color', __( 'Link Color:', 'musopress' ), 'muso_bc_link_color_setting', 'musopress-plugin-options', 'main_section' );

	add_settings_field( 'muso_number_rows', __( 'Number of albums to display per row:', 'musopress' ), 'muso_number_rows_setting', 'musopress-plugin-options', 'main_section' );
			
	add_settings_field( 'muso_enable_artist', __( 'Enable Artist Taxonomy:', 'musopress' ), 'muso_enable_artist_setting', 'musopress-plugin-options', 'main_section' );
	add_settings_field( 'muso_enable_comments', __( 'Enable Comments for Albums:', 'musopress' ), 'muso_enable_comments_setting', 'musopress-plugin-options', 'main_section' );
	add_settings_field( 'muso_disable_css', __( 'Check to disable plugin CSS:', 'musopress' ), 'muso_disable_css_setting', 'musopress-plugin-options', 'main_section' );
		
}

/**
 * Sanitize and validate all input.
 * 
 * @uses add_settings_error()
 * @uses get_option()
 * @uses add_settings_field()
 * 
 * @since Musopress Discography 0.1
 */
function muso_validate_setting($input) {

	$valid = array();
		
	foreach ( $input as $key => $value ) {
		
		if ( 'muso_bc_color' == $key || 'muso_bc_link_color' == $key ) {
				
				if ( '' == $input[$key]  
				|| preg_match( '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/', $value ) ) {
					$valid[$key] = $input[$key];
					
				} else {
					$options = get_option('es_md_options');
					$valid[$key] = $options[$key];
					add_settings_error( $key, 'settings_updated', __( 'Please enter a valid hex value.', 'musopress' ) );
				}
			}
			
		if ( 'muso_enable_artist' == $key || 'muso_enable_comments' == $key || 'muso_disable_css' == $key ) {
				
				if ( 'true' == $input[$key] ) {
					$valid[$key] = $input[$key];
				} else {
					$valid[$key] = '';
				}
				
			}
			
		if ( 'muso_number_rows' == $key ) {
			$input[$key] = intval( $input[$key] );
			if ( 2 < $input[$key] && 7 > $input[$key] ) {
				$valid[$key] = $input[$key];
			} else {
				$valid[$key] = 3;
			}
		}
	}		
	return $valid;
}


/**
 * Empty callback function.
 * 
 * @since Musopress Discography 0.1
 */
function muso_section_cb() {}


/** Settings Fields *************************************************************/

/**
 * Create Bandcamp Color field.
 * 
 * @uses get_option()
 * 
 * @since Musopress Discography 0.1
 */
function muso_bc_color_setting() {
	$options = get_option('es_md_options');
	if ( isset ( $options['muso_bc_color'] ) && '' != $options['muso_bc_color'] ) {
		$border_style = "style='border:2px solid {$options['muso_bc_color']};'";
		$color = $options['muso_bc_color'];
	} else {
		$border_style = "style='border-width:2px;'";
		$color = '';
	}
	
	echo "<input name='es_md_options[muso_bc_color]' type='text' id='muso-bc-color' $border_style value='" . esc_attr( $color ) . "' />"; 
}

/**
 * Create Bandcamp Link Color field.
 * 
 * @uses get_option()
 * 
 * @since Musopress Discography 0.1
 */
function muso_bc_link_color_setting() {
	$options = get_option('es_md_options');
	if ( isset ( $options['muso_bc_link_color'] ) && '' != $options['muso_bc_link_color'] ) {
		$border_style = "style='border:2px solid {$options['muso_bc_link_color']};'";
		$color = $options['muso_bc_link_color'];
	} else {
		$border_style = "style='border-width:2px;'";
		$color = '';
	}
	
	echo "<input name='es_md_options[muso_bc_link_color]' type='text' id='muso-bc-link-color' $border_style value='" . esc_attr( $color ) . "' />"; 
}

/**
 * Create Number of rows field.
 * 
 * @uses get_option()
 * @uses selected()
 * 
 * @since Musopress Discography 0.1
 */
function muso_number_rows_setting() {
	$options = get_option('es_md_options'); ?>
	
	<select name="es_md_options[muso_number_rows]">
		<option value="3" <?php selected( isset( $options['muso_number_rows'] ) ? $options['muso_number_rows'] : 0, 3 ); ?>><?php echo esc_html( '3' ); ?></option>
		<option value="4" <?php selected( isset( $options['muso_number_rows'] ) ? $options['muso_number_rows'] : 0, 4 ); ?>><?php echo esc_html( '4' ); ?></option>
		<option value="5" <?php selected( isset( $options['muso_number_rows'] ) ? $options['muso_number_rows'] : 0, 5 ); ?>><?php echo esc_html( '5' ); ?></option>
		<option value="6" <?php selected( isset( $options['muso_number_rows'] ) ? $options['muso_number_rows'] : 0, 6 ); ?>><?php echo esc_html( '6' ); ?></option>
	</select>
<?php }

/**
 * Create enable Artist Taxonomy field.
 * 
 * @uses get_option()
 * 
 * @since Musopress Discography 0.1
 */
function muso_enable_artist_setting() {
	$options = get_option('es_md_options'); 
	echo "<input name='es_md_options[muso_enable_artist]' type='checkbox' value='true' ";
	checked ( isset( $options[ 'muso_enable_artist' ] ) ? $options[ 'muso_enable_artist' ] : 0, 'true' );
	echo "/>";
}

/**
 * Create enable comments field.
 * 
 * @uses get_option()
 * 
 * @since Musopress Discography 0.1
 */
function muso_enable_comments_setting() {
	$options = get_option('es_md_options'); 
	echo "<input name='es_md_options[muso_enable_comments]' type='checkbox' value='true' ";
	checked ( isset( $options[ 'muso_enable_comments' ] ) ? $options[ 'muso_enable_comments' ] : 0, 'true' );
	echo "/>";
}

/**
 * Create disable CSS field.
 * 
 * @uses get_option()
 * 
 * @since Musopress Discography 0.1
 */
function muso_disable_css_setting() {
	$options = get_option('es_md_options'); 
	echo "<input name='es_md_options[muso_disable_css]' type='checkbox' value='true' ";
	checked ( isset( $options[ 'muso_disable_css' ] ) ? $options[ 'muso_disable_css' ] : 0, 'true' );
	echo "/>";
}

?>