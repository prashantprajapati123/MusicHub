jQuery(function($){

	$( '.add-new-track' ).click(function(){
		var $playlist = $( '.themify-playlist' );
		var $new_track = $playlist.find( '.themify-track:last-child' ).clone();
		$new_track.find( 'input' ).each(function(){
			$( this ).attr( 'name', $( this ).attr( 'name').replace( /themify_audio_dock_playlist\[\d*\]/, 'themify_audio_dock_playlist['+ $playlist.children().length +']' ) );
			$( this ).val( '' );
		});
		$new_track.appendTo( $playlist );
	});

	// Uploading files
	var file_frame = '', set_to_post_id = wp.media.model.settings.post.id; // Set this

	jQuery( 'body' ).on( 'click', '.themify-audio-dock-media-browse', function( event ){
		var $el = jQuery(this), $data = $el.data('submit');

		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery(this).data('uploader-title'),
			library: {
				type: $el.data('type')
			},
			button: {
				text: jQuery(this).data( 'uploader-button-text' )
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();

			jQuery( $el ).closest( '.themify-track' ).find( '.song-file-field' ).val( attachment.url );

		});

		// Finally, open the modal
		file_frame.open();
		event.preventDefault();
	});

	$( '.themify-audio-dock-color-picker' ).minicolors({
		opacity: true,
		format: 'rgb'
	});

	$( 'body' ).on( 'click', '.themify-audio-dock-delete-track', function(e){
		e.preventDefault();
		$( this ).closest( '.themify-track' ).fadeOut(function(){
			$( this ).empty();
		});
	} );
});