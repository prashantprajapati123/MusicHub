<?php
/*
Plugin Name: Musopress Discography
Plugin URI: http://musopress.ernestoschnack.com/discography-plugin/
Description: Creates a Discography Custom Post Type and allows you to import your albums from Bandcamp.
Version: 0.5.1
Author: Ernesto Schnack
Author URI: http://musopress.ernestoschnack.com/
License: GPLv2
*/

/*  Copyright 2011  Ernesto Schnack  (email : eschnack@gmail.com)
        
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
        
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
        
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook( __FILE__ , 'muso_activate' );

function muso_activate() {
	muso_create_custom_posts();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__ , 'muso_deactivate' );

function muso_deactivate() {
	flush_rewrite_rules();
}

add_action( 'after_setup_theme', 'muso_discog_setup' );

/**
 * Setup the default filters and actions.
 * 
 * @uses add_theme_support() To add support for post thumbnails if needed.
 * @uses current_theme_supports() Check if current theme supports post thumbnails.
 * @uses add_image_size() To add image sizes for album covers.
 * @uses locate_template() To check if a Single Album template exists.
 * @uses load_plugin_textdomain() For translation/localization support.
 * @uses get_option() To get plugin options.
 *
 * @since Musopress Discography 0.1
 */
function muso_discog_setup() {
	
	$options = get_option('es_md_options');
	
	//Load textdomain.
	load_plugin_textdomain( 'musopress', false, 'musopress-discography/languages' );
	
	//Create custom Post Type.
	add_action( 'init', 'muso_create_custom_posts' );
	
	//Create Artist Taxonomy if enabled.
	if ( isset( $options['muso_enable_artist'] ) && 'true' == $options['muso_enable_artist'] ) 
		add_action( 'init', 'muso_taxonomies', 0 );
	
	//Add theme support for Post Thumbnails if not supported.
	if ( !current_theme_supports( 'post-thumbnails' ) )
		add_theme_support( 'post-thumbnails', array( 'muso-album' ) );
	
	//Add Image Sizes for album covers.
	add_image_size( 'album-thumbnail', 140, 140, true );
	add_image_size( 'album-cover',     350, 350, true );
	
	//Add Widget and Shortcode.
	add_action( 'widgets_init',  'muso_load_widgets' );
	add_shortcode('discography', 'muso_discography_shortcode');
	
	//Use Page template for Single Albums if single-muso-album.php doesn't exist.
	if ( '' == ( locate_template( 'single-muso-album.php' ) ) )
		add_filter( 'single_template', 'muso_get_album_template' );
	
	//Use empty comments template if comments are disabled for Discography posts.
	if ( !isset( $options['muso_enable_comments'] ) || '' == $options['muso_enable_comments'] ) 
		add_filter( 'comments_template', 'muso_comments_template' );
	
	//Add content to Singel Albums.
	add_filter( 'the_content', 'muso_add_templates' );
	
	//Load css and js files.
	if ( !isset( $options['muso_disable_css'] ) || ( isset( $options['muso_disable_css'] ) && 'true' != $options['muso_disable_css'] ) ) 
		add_action( 'wp_print_styles', 'muso_load_styles' );
	add_action( 'admin_print_styles', 'muso_admin_styles' );
	add_action( 'admin_print_scripts-settings_page_musopress-plugin-options', 'muso_admin_scripts' );
		
	//Load options, Bandcamp page and additional meta-boxes, only in back-end.
	if ( is_admin() ) {
		add_action( 'admin_menu',     'muso_create_bandcamp_page' );
		add_action( 'admin_menu',     'muso_create_plugin_options_page' );
		add_action( 'admin_init',     'muso_register_and_build_fields' );
		add_action( 'add_meta_boxes', 'muso_add_custom_meta_boxes' ); //custom fields for custom post types
		add_action( 'save_post',      'muso_save_meta', 1, 2 ); // save the custom fields
	}

}

/**
 * Register the Discography Custom Post Type.
 * 
 * @uses register_post_type() To register the post type.
 * @uses get_option() To get plugin options.
 *
 * @since Musopress Discography 0.1
 */
function muso_create_custom_posts() {
	$options = get_option('es_md_options');
	
	if ( isset( $options['muso_enable_comments'] ) && 'true' == $options['muso_enable_comments'] )
		$supports = array( 'title', 'editor', 'thumbnail', 'comments' );
	else
		$supports = array( 'title', 'editor', 'thumbnail' );
	
	$labels = array(
		'singular_name' => __( 'Album', 'musopress' ),
		'add_new_item'  => __( 'Add New Album', 'musopress' ),
		'edit_item'     => __( 'Edit Album', 'musopress' ),
		'new_item'      => __( 'New Album', 'musopress' ),
		'view_item'     => __( 'View Album', 'musopress' ),
		'search_items'  => __( 'Search Albums', 'musopress' )
	);
		
	$album_args = array(  
	   'label'           => __( 'Discography', 'musopress' ),  
	   'labels'          => $labels,  
	   'public'          => true,
	   'show_ui'         => true,  
	   'capability_type' => 'post',  
	   'hierarchical'    => false,  
	   'rewrite'         => array('slug' => 'discography', 'with_front' => false ),
	   'has_archive'     => false,     
	   'supports'        => $supports 
	);  
		   	   		   
	register_post_type( 'muso-album' , $album_args );

}

/**
 * Register the Artist Taxonomy.
 * 
 * @uses register_taxonomy() To register the term.
 *
 * @since Musopress Discography 0.1
 */
function muso_taxonomies() {  //Artist Taxonomy for Discography
	register_taxonomy( 'artist', 'muso-album', array( 'label' => 'Artist' ) );    
}

/**
 * Loads the Discography Widget.
 * 
 * @uses register_widget() To register the widget class.
 *
 * @since Musopress Discography 0.1
 */
function muso_load_widgets() {
	register_widget( 'Muso_Discog_Widget' );
}

/**
 * Creates a Discography index page using the [discography] shortcode.
 * 
 * @uses $post to get post ID.
 * @uses $wp_query
 * @uses get_option()
 * @uses get_query_var()
 * @uses have_posts()
 * @uses the_post()
 * @uses get_the_term_list()
 * @uses get_permalink()
 * @uses the_title_attribute()
 * @uses get_the_post_thumbnail()
 * @uses get_the_title()
 * @uses get_next_posts_link()
 * @uses get_previous_posts_link()
 * @uses wp_reset_query()
 * 
 * @since Musopress Discography 0.1
 * @return string
 */
function muso_discography_shortcode() {
	
	global $post;
	global $wp_query;
	
	$options = get_option('es_md_options');
	
	if ( isset( $options['muso_number_rows'] ) ) {
		$num_rows = $options['muso_number_rows'];
		$albums_per_page = $num_rows * 4;
	} else {
		$num_rows = 3;
		$albums_per_page = 12;
	}
	
	/* Query albums from the database. */
    $loop = new WP_Query(
        array(
            'post_type' => 'muso-album',
            'posts_per_page' => $albums_per_page,
	        'paged' => get_query_var('paged') ? get_query_var('paged') : 1
        )
    );
             
    /* Check if any albums were returned. */
    if ( $loop->have_posts() ) {
             
        /* Open an unordered list. */
        $output = '<table id="muso-discog-grid">';
		$i = 0; 
		$j = 1;
			     
        /* Loop through the albums (The Loop). */
        while ( $loop->have_posts() ) {
             
            $loop->the_post();
			
            if ( isset( $options['muso_enable_artist'] ) && 'true' == $options['muso_enable_artist'] )
            	$artist = '<br/>' . get_the_term_list( $post->ID, 'artist', '', ', ', ' ' );
            else
            	$artist = '';
            
			if ( 1 == $j )
				$output .= '<tr>';
			elseif ( 0 == ( $i % $num_rows ) )
				$output .= '</tr><tr>';
			
			$output .= '<td class="album-grid">';
			$output .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_post_thumbnail( $post->ID, 'album-thumbnail', array( 'class' => 'img-frame' ) ) . '</a>';
			$output .= '<p><a href="' . get_permalink() . '" title="' . get_the_title() . '" class="album-title">' . get_the_title() . '</a>' . $artist . '</p></td>';
          
			$i++;
			$j++;
             
        } //end while have posts...
             
        /* Close the table. */
        $output .= '</tr></table>';
        
		$backup_page_total = $wp_query->max_num_pages;
		$wp_query->max_num_pages = $loop->max_num_pages;
		$output .= '<div><span class="alignright muso-nav-links">' . get_next_posts_link( __( 'Older Releases &raquo;', 'musopress' ) ) . '</span><span class="alignleft muso-nav-links">' . get_previous_posts_link( __('&laquo; Newer Releases', 'musopress' ) ) . '</span></div>';
		wp_reset_query(); 
		$wp_query->max_num_pages = $backup_page_total;
    } else {  /* If no albums were found. */
        $output = '<p>No albums have been published.</p>';
    } //end if have posts...else...
             
    /* Return the music albums list. */
    return $output;	
}

/**
 * Uses the page.php template for single albums.
 * 
 * @uses locate_template() To get the template's location.
 *
 * @since Musopress Discography 0.1
 * @return string
 */
function muso_get_album_template( $single_template ) {
	
	global $post;
	
	if ( 'muso-album' == $post->post_type ) {
		$locate = locate_template( 'page.php' );
		if ( !empty( $locate ) )
			$single_template = $locate;
	}
		
	return $single_template;
	
}

/**
 * Uses an empty comments template if comments are disabled.
 * 
 * This prevents unwanted 'Comments are closed' messages.
 * 
 * @uses locate_template() To get the template's location.
 *
 * @since Musopress Discography 0.1
 * @return string
 */
function muso_comments_template( $comments_template ) {
	
	global $post;
	
	if ( 'muso-album' == $post->post_type ) {
		$comments_template = dirname( __FILE__ ) . '/templates/muso-comments.php';
	}
	
	return $comments_template;
}

/**
 * Adds the the album template to the single post content.
 * 
 * @uses $post to get post ID.
 * @uses is_singular()
 * @uses get_the_post_thumbnail()
 * @uses get_post_meta()
 * @uses get_the_content
 * 
 * @since Musopress Discography 0.1
 * @return string
 */
function muso_add_templates( $content ) {
	
	global $post;
	
	if ( is_singular( 'muso-album' ) ) {
		$content = '<div id="album-cover-container">';
		$content .= get_the_post_thumbnail( $post->ID, 'album-cover', array('class'=>'album-cover' ) );
		$content .=	'</div>';
		if ( get_post_meta( $post->ID, 'embed-code', true ) ) { 
			$content .= '<div class="music-player">';
			$content .=	get_post_meta( $post->ID, 'embed-code', true );
			$content .=	'</div>';
		}			
		$content .=	'<div id="album-description">';
		$content .= get_the_content();
		$content .= '</div>';
	}
	
	return $content;
}

/**
 * Template tag for the Artist Taxonomy Page.
 * 
 * Takes place of the Loop in the archive template.
 * 
 * @uses $post to get post ID.
 * @uses get_option()
 * @uses have_posts()
 * @uses the_post()
 * @uses get_the_term_list()
 * @uses the_permalink()
 * @uses the_title_attribute()
 * @uses the_post_thumbnail()
 * @uses the_title()
 * @uses next_posts_link()
 * @uses previous_posts_link()
 * 
 * @since Musopress Discography 0.1
 */
function muso_display_artist() { 
	
	global $post; ?>
	
	<table id="muso-discog-grid">
        <?php 
        $i = 0; 
        $j = 1;
        ?>	
        <!--Start the Loop-->
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        	
			$options = get_option('es_md_options');
            if ( isset( $options['muso_enable_artist'] ) && 'true' == $options['muso_enable_artist'] )
            	$artist = get_the_term_list( $post->ID, 'artist', '', ', ', ' ' );
            else
            	$artist = '';
            
			if ( isset( $options['muso_number_rows'] ) )
				$num_rows = $options['muso_number_rows'];
			else
				$num_rows = 3;
			
        	if ( 1 == $j ) { ?>
	        	<tr>
	        <?php } elseif ( 0 == ($i % $num_rows) ) { ?>
	        	</tr>
	        	<tr>
	        <?php } ?>		
			
				<td class="album-grid">
	            	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'album-thumbnail', array( 'class' => 'img-frame' ) ); ?></a>
            		<p>
            			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="album-title"><?php the_title(); ?></a><br/>
            			<?php echo $artist; ?>
            		</p>
	       		</td>
		
		 	<?php
		       $i++;
			   $j++;
			   
        endwhile;
        endif; 
        ?>
        <!--End the Loop-->
			</tr>
		</table>
		<div><span class="alignright muso-nav-links"><?php next_posts_link( __( 'Older Releases &raquo;', 'musopress' ) ); ?></span><span class="alignleft muso-nav-links"><?php previous_posts_link( __('&laquo; Newer Releases', 'musopress' ) ); ?></span></div>
<?php }

/**
 * Template tag for the Artist Taxonomy Title.
 * 
 * Takes place of the Title in the archive template.
 * 
 * @uses get_term_by()
 * @uses get_query_var()
 * 
 * @since Musopress Discography 0.1
 */
function muso_artist_title() {
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	echo __( 'Discography', 'musopress' ) . ' &raquo; ' . $term->name;
}

/**
 * Loads plugin styles for the front-end.
 * 
 * @uses wp_enqueue_style()
 * @uses plugins_url()
 * 
 * @since Musopress Discography 0.1
 */
function muso_load_styles() {
	wp_enqueue_style( 'muso-css-styles', plugins_url( 'css/muso-style.css', __FILE__ ) );
}

/**
 * Loads plugin styles for the back-end.
 * 
 * @uses wp_enqueue_style()
 * @uses plugins_url()
 * 
 * @since Musopress Discography 0.1
 */
function muso_admin_styles() {
	wp_enqueue_style( 'muso-colorpicker-styles', plugins_url( 'css/colorpicker.css', __FILE__ ) );
	wp_enqueue_style( 'muso-admin-styles', plugins_url( 'css/admin-style.css', __FILE__ ) );
}

/**
 * Loads plugin scripts for the back-end.
 * 
 * @uses wp_enqueue_script()
 * @uses wp_register_script()
 * @uses plugins_url()
 * 
 * @since Musopress Discography 0.1
 */
function muso_admin_scripts() {
	wp_enqueue_script( 'muso-colorpicker', plugins_url( 'js/colorpicker.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'muso-admin-script', plugins_url( 'js/admin-script.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'muso-admin-script' );
}

/**
 * Loads script for the Bandcamp Import page.
 * 
 * @uses wp_enqueue_script()
 * @uses wp_register_script()
 * @uses plugins_url()
 * 
 * @since Musopress Discography 0.1
 */
function muso_admin_bandcamp_script() {
	wp_register_script( 'muso-bandcamp-script', plugins_url( 'js/bandcamp-script.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'muso-bandcamp-script' );
}

if( is_admin() ) {
	require_once(plugin_dir_path( __FILE__ ) . '/includes/muso-bandcamp-import.php');
	require_once(plugin_dir_path( __FILE__ ) . '/includes/muso-meta-boxes.php');
	require_once(plugin_dir_path( __FILE__ ) . '/includes/muso-plugin-options.php');
}

require_once(plugin_dir_path( __FILE__ ) . '/includes/muso-discography-widget.php');

?>
