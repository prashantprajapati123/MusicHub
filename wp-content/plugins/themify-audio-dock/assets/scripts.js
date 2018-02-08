jQuery(function($){

	function themifyAudioDockInit() {
		if ( $('.wp-audio-shortcode').length > 0 ) {
			$('.wp-audio-shortcode').on('play playing', function(e){
				var $this = $(this);
				$this.closest('.mejs-inner').find('.mejs-currenttime').addClass('visible-currenttime');
			} );
		}

		var $player = $('#themify-audio-dock');
		if ( $player.length > 0 ) {
			// Toggle player
			$player.on('click', '.button-switch-player', function(e){
				e.preventDefault();
				$(this).closest('#themify-audio-dock').toggleClass('collapsed');
			});

			// Wrap volume/mute/volume slider in div to move them
			$player.find('.mejs-volume-button, .mejs-horizontal-volume-slider').wrapAll('<div class="themify-audio-dock-volume" />');
		}
	}

	$( window ).load( themifyAudioDockInit );
});