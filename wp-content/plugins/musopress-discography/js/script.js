jQuery(document).ready(function(){
  jQuery('#header .menu li').hover(function(){
  	jQuery(this).children('.sub-menu, .children').css('display','none').slideDown('fast');
  },function(){
  	jQuery(this).children('.sub-menu, .children').stop(true, true).css('display','none');
  }
  	
 );
 

 
 jQuery('#s').attr({value: 'search', style: 'color:#999;'});
  jQuery('#s').focus(function(){
  	if(jQuery(this).attr("value")=="search") jQuery(this).attr({value: '', style: 'color:#222;'});
  });
  jQuery('#s').blur(function(){
  	if(jQuery(this).attr("value")=="") jQuery(this).attr({value: 'search', style: 'color:#999;'});
  });
 
 
	
});