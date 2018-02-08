<?php

//Musopress Discography Widget

class Muso_Discog_Widget extends WP_Widget {

	function Muso_Discog_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'muso-list-discog', 'description' => __('A widget for the Musopress theme to list Discography posts.', 'musopress') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'muso-discog-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'muso-discog-widget', __('Musopress Discography', 'musopress'), $widget_ops, $control_ops );
	}


	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$num_albums = $instance['num_albums'];
		$artist = $instance['artist'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) 
			echo $before_title . $title . $after_title;
		

		/* Display name from widget settings if one was input. */
		if ( 'All' == $artist ) {
	        $args = array(
	        'post_type' => 'muso-album',
	        'posts_per_page' => $num_albums
	        );
		} else {
			$args = array(
	        'post_type' => 'muso-album',
	        'posts_per_page' => $num_albums,
	        'artist' => $artist
	        );
			
		}
	        $album_query = new WP_Query( $args );
	        ?>
	        <dl>
	        <!--Start the Loop-->
	        <?php 
	        if ( $album_query->have_posts() ) : while ( $album_query->have_posts() ) : $album_query->the_post(); ?>
			<dt><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'album-thumbnail', array( 'class' => 'img-frame' ) ); ?></a></dt>
            <dd><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></dd>
	        <?php
	        endwhile;
	        endif; 
	        ?>
	        <!--End the Loop-->
	        
	        <?php wp_reset_query(); 
		
		echo '</dl>';
		/* After widget (defined by themes). */
		echo $after_widget;
	}


	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['num_albums'] = strip_tags( $new_instance['num_albums'] );
		$instance['artist'] = $new_instance['artist'];


		return $instance;
	}


	function form( $instance ) { //Output Widget Fields

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Albums', 'musopress'), 'num_albums' => __('5', 'musopress'), 'artist' => 'All' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>


		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'musopress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>


		<p>
			<label for="<?php echo $this->get_field_id( 'num_albums' ); ?>"><?php _e('Number of albums to show:', 'musopress'); ?></label>
			<input id="<?php echo $this->get_field_id( 'num_albums' ); ?>" name="<?php echo $this->get_field_name( 'num_albums' ); ?>" value="<?php echo $instance['num_albums']; ?>" style="width:100%;" />
		</p>


		<p>
			<label for="<?php echo $this->get_field_id( 'artist' ); ?>"><?php _e('Artist:', 'musopress'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'artist' ); ?>" name="<?php echo $this->get_field_name( 'artist' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'All' == $instance['artist'] ) echo 'selected="selected"'; ?>>All</option>
				<?php 
				$options = get_option('es_md_options');
				if ( isset( $options['muso_enable_artist'] ) && 'true' == $options['muso_enable_artist'] ) {
					$terms = get_terms("artist"); //gets list of artists
					$count = count($terms); 
					if($count > 0){ 
						foreach ($terms as $term) { ?>
							<option <?php if ( $term->name == $instance['artist'] ) echo 'selected="selected"'; ?>><?php echo $term->name; ?></option> 
		     			<?php } 
					} 
				} ?>	
			</select>
		</p>
<?php			
	}
}
?>