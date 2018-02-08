<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TZ_Music_Press_Customs {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'tz_music_register_customs' ) );
		add_action( 'admin_menu', array( $this, 'tz_music_remove_taxonomy_metaboxes' ) );
		add_filter( 'the_content', array( $this, 'tz_music_content' ) );

		add_filter( 'the_music_content', 'wptexturize' );
		add_filter( 'the_music_content', 'convert_smilies' );
		add_filter( 'the_music_content', 'convert_chars' );
		add_filter( 'the_music_content', 'wpautop' );
		add_filter( 'the_music_content', 'shortcode_unautop' );
		add_filter( 'the_music_content', 'prepend_attachment' );

	}
	/**
	 * Registers the necessary custom post types and taxonomies for the plugin
	 */

	public function tz_music_register_customs() {


		$singular  = __( 'Genre', 'music-press' );
		$plural    = __( 'Genres', 'music-press' );

		$args = array(
			'label' 					=> $plural,
			'labels' => array(
				'name' 					=> $singular,
				'singular_name' 		=> $singular,
				'menu_name'				=> $plural,
				'search_items' 			=> sprintf( __( 'Search %s', 'music-press' ), $plural ),
				'all_items' 			=> sprintf( __( 'All %s', 'music-press' ), $plural ),
				'parent_item' 			=> sprintf( __( 'Parent %s', 'music-press' ), $singular ),
				'parent_item_colon'		=> sprintf( __( 'Parent %s:', 'music-press' ), $singular ),
				'edit_item' 			=> sprintf( __( 'Edit %s', 'music-press' ), $singular ),
				'update_item' 			=> sprintf( __( 'Update %s', 'music-press' ), $singular ),
				'add_new_item' 			=> sprintf( __( 'Add New %s', 'music-press' ), $singular ),
				'new_item_name' 		=> sprintf( __( 'New %s Name', 'music-press' ),  $singular )
			),
			'hierarchical'               => true,
			'show_admin_column'          => true,
			'rewrite'           => array( 'slug' => 'genre' )
		);
		register_taxonomy( 'genre', 'music', $args );

		$singular  = __( 'Album', 'music-press' );
		$plural    = __( 'Albums', 'music-press' );


		$args = array(
			'label' 					=> $plural,
            'labels' => array(
                'name' 					=> $singular,
                'singular_name' 		=> $singular,
                'menu_name'				=> $plural,
                'search_items' 			=> sprintf( __( 'Search %s', 'music-press' ), $plural ),
                'all_items' 			=> sprintf( __( 'All %s', 'music-press' ), $plural ),
                'parent_item' 			=> sprintf( __( 'Parent %s', 'music-press' ), $singular ),
                'parent_item_colon'		=> sprintf( __( 'Parent %s:', 'music-press' ), $singular ),
                'edit_item' 			=> sprintf( __( 'Edit %s', 'music-press' ), $singular ),
                'update_item' 			=> sprintf( __( 'Update %s', 'music-press' ), $singular ),
                'add_new_item' 			=> sprintf( __( 'Add New %s', 'music-press' ), $singular ),
                'new_item_name' 		=> sprintf( __( 'New %s Name', 'music-press' ),  $singular )
        	),
			'hierarchical'               => false,
			'show_admin_column'          => true
		);
		register_taxonomy( 'album', 'music', $args );

		$singular  = __( 'Artist', 'music-press' );
		$plural    = __( 'Artists', 'music-press' );

		$args = array(
			'label' 					=> $plural,
            'labels' => array(
                'name' 					=> $singular,
                'singular_name' 		=> $singular,
                'menu_name'				=> $plural,
                'search_items' 			=> sprintf( __( 'Search %s', 'music-press' ), $plural ),
                'all_items' 			=> sprintf( __( 'All %s', 'music-press' ), $plural ),
                'parent_item' 			=> sprintf( __( 'Parent %s', 'music-press' ), $singular ),
                'parent_item_colon'		=> sprintf( __( 'Parent %s:', 'music-press' ), $singular ),
                'edit_item' 			=> sprintf( __( 'Edit %s', 'music-press' ), $singular ),
                'update_item' 			=> sprintf( __( 'Update %s', 'music-press' ), $singular ),
                'add_new_item' 			=> sprintf( __( 'Add New %s', 'music-press' ), $singular ),
                'new_item_name' 		=> sprintf( __( 'New %s Name', 'music-press' ),  $singular )
        	),
			'hierarchical'               => false,
			'show_admin_column'          => true
		);
		register_taxonomy( 'artist', 'music', $args );

		/**
		 * Post types
		 */
		$singular  = __( 'Songs', 'music-press' );
		$plural    = __( 'Music Press', 'music-press' );

		$args = array(
			'description'         => __( 'This is where you can create and manage Songs.', 'music-press' ),
			'labels' => array(
				'name' 					=> $plural,
				'singular_name' 		=> $singular,
				'menu_name'             => $plural,
				'all_items'             => sprintf( __( 'All %s', 'music-press' ), $singular ),
				'add_new' 				=> __( 'Add New Song', 'music-press' ),
				'add_new_item' 			=> sprintf( __( 'Add %s', 'music-press' ), $singular ),
				'edit' 					=> __( 'Edit', 'music-press' ),
				'edit_item' 			=> sprintf( __( 'Edit %s', 'music-press' ), $singular ),
				'new_item' 				=> sprintf( __( 'New %s', 'music-press' ), $singular ),
				'view' 					=> sprintf( __( 'View %s', 'music-press' ), $singular ),
				'view_item' 			=> sprintf( __( 'View %s', 'music-press' ), $singular ),
				'search_items' 			=> sprintf( __( 'Search %s', 'music-press' ), $plural ),
				'not_found' 			=> sprintf( __( 'No %s found', 'music-press' ), $plural ),
				'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'music-press' ), $plural ),
				'parent' 				=> sprintf( __( 'Parent %s', 'music-press' ), $singular )
			),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 20,
			'menu_icon'           => TZ_MUSIC_PRESS_PLUGIN_URL . '/assets/images/music-press.png',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rewrite'			  => array( 'slug' => 'music' )
		);
		register_post_type( 'music', $args );

	}
	/**
	 * Removes the default taxonomy metaboxes from the edit screen.
	 * We use the advanced custom fields instead and sync the data.
	 */
	public function tz_music_remove_taxonomy_metaboxes(){
		add_submenu_page(
			'edit.php?post_type=music',
			'Music Add-ons page', /*page title*/
			'Add-ons', /*menu title*/
			'manage_options', /*roles and capabiliyt needed*/
			'music-add-ons',
			'tz_adds_on_menu' /*replace with your own function*/
		);
		remove_meta_box( 'tagsdiv-album', 'music', 'normal' );
		remove_meta_box( 'tagsdiv-artist', 'music', 'normal' );
		function tz_adds_on_menu(){
			function tz_rmdir_recursive($dir) {
				foreach(scandir($dir) as $file) {
					if ('.' === $file || '..' === $file) continue;
					if (is_dir("$dir/$file")) tz_rmdir_recursive("$dir/$file");
					else unlink("$dir/$file");
				}

				rmdir($dir);
			}

			if($_FILES["zip_file"]["name"]) {
				$filename = $_FILES["zip_file"]["name"];
				$source = $_FILES["zip_file"]["tmp_name"];
				$type = $_FILES["zip_file"]["type"];

				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				foreach($accepted_types as $mime_type) {
					if($mime_type == $type) {
						$okay = true;
						break;
					}
				}

				$continue = strtolower($name[1]) == 'zip' ? true : false;
				if(!$continue) {
					$message = "The file you are trying to upload is not a .zip file. Please try again.";
				}

				/* PHP current path */
				$path = dirname(__FILE__).'/add-ons/';  // absolute path to the directory where zipper.php is in
				$filenoext = basename ($filename, '.zip');  // absolute path to the directory where zipper.php is in (lowercase)
				$filenoext = basename ($filenoext, '.ZIP');  // absolute path to the directory where zipper.php is in (when uppercase)

				$targetdir = $path . $filenoext; // target directory
				$targetzip = $path . $filename; // target zip file

				/* create directory if not exists', otherwise overwrite */
				/* target directory is same as filename without extension */

				if (is_dir($targetdir))  rmdir_recursive ( $targetdir);


				mkdir($targetdir, 0777);

				/* here it is really happening */

				if(move_uploaded_file($source, $targetzip)) {
					WP_Filesystem();
					$unzipfile = unzip_file( $targetzip, $path);
					unlink($targetzip);
					if ( $unzipfile ) {
						echo 'Successfully unzipped the file!';
					} else {
						echo 'There was an error unzipping the file.';
					}


				} else {
					$message = "There was a problem with the upload. Please try again.";
				}
			}
			?>

			<h3 class="music-addons">Music Press Add-ons</h3>
			<div class="addons-header">
				<?php if($message) echo "<p>$message</p>"; ?>
				<h4><?php echo esc_html__('Add new Add-ons','music-press');?></h4>
				<form enctype="multipart/form-data" method="post" action="">
					<label>Choose a zip file to upload: <input type="file" name="zip_file" /></label>
					<input type="submit" class="addon-submit"  name="submit" value="Upload" />
				</form>
			</div>
			<?php
//			$dir    = ''.TZ_MUSIC_PRESS_PLUGIN_DIR.'/includes/add-ons';
//			$folders = scandir($dir);
//			$countdir = count($folders);
//			if($countdir >2){ $i=1;
//				foreach($folders as $folder){
//					if($i>2){
//						$addons_file_name = TZ_MUSIC_PRESS_PLUGIN_DIR.'/includes/add-ons/'.$folder.'/'.$folder.'.txt';
//						if (file_exists($addons_file_name )) {
//							 file_get_contents($addons_file_name);
//							 $info = explode("*", file_get_contents($addons_file_name));

//						}
//					}
//					$i++;
//				}
//			}
			?>
			<ul class="addons-contain">
				<li class="addons-item">
					<a href="javascript: " ><img src="<?php echo esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/includes/add-ons/artist-info/artist-info.jpg');?>"/></a>
					<span><?php echo esc_html__('Artist Info','music-press');?></span>
					<span class="version"><?php echo esc_html__('Version 1.0','music-press');?></span>
					<p><?php echo esc_html__('Add more information for Artist as: Real name, Birthday, website...','music-press')?></p>
					<div class="btn_addons">
						<button class="installed"><?php echo esc_html__('Installed','music-press');?></button>
					</div>
				</li>
				<li class="addons-item">
					<a href="javascript: " ><img src="<?php echo esc_url(TZ_MUSIC_PRESS_PLUGIN_URL.'/includes/add-ons/album-info/album-info.jpg');?>"/></a>
					<span><?php echo esc_html__('Album Info','music-press');?></span>
					<span class="version"><?php echo esc_html__('Version 1.0','music-press');?></span>
					<p><?php echo esc_html__('Add more information for Album','music-press')?></p>
					<div class="btn_addons">
						<button class="installed"><?php echo esc_html__('Installed','music-press');?></button>
					</div>
				</li>
			</ul>

			<?php
		}
	}

	/**
	 * Since our vehicle post type doesn't have an editor field we need to display some of the meta values instead
	 * @param  string $content 	the excisting content
	 * @return string $content 	the updated content
	 */
	function tz_music_content( $content ) {

		global $post;

		if ( $post->post_type == 'music' ) {
			$content = do_shortcode( '[music_press_description]' );
			$content = apply_filters( 'the_music_content', $content );
		}

		return $content;
	}
}
?>