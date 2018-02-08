jQuery(document).ready(function() {
	
	jQuery("#select-all").click(function() {
		
		var checked_status = this.checked;
		
		jQuery("input[type=checkbox]").each(function() {
			this.checked = checked_status;
		});
		
	});
   
});