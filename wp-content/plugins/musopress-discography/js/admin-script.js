

jQuery(document).ready(function(){
	jQuery('#muso-bc-color, #muso-bc-link-color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).val('#' + hex);
			jQuery(el).ColorPickerHide();
			jQuery(el).css('border', '2px solid #' + hex);
			
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor(this.value);
		}
		,
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(200);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(200);
			return false;
		}})
		.bind('keyup', function(){
			jQuery(this).ColorPickerSetColor(this.value);
	});
		
});