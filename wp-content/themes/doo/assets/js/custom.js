(function($){
	jQuery(document).ready(function($) {	
		jQuery('#back_top').click(function(){
			jQuery('html, body').animate({scrollTop:0}, 'normal');return false;
		});	
		jQuery(window).scroll(function(){
			if(jQuery(window).scrollTop() !== 0){jQuery('#back_top').css('display','block');}else{jQuery('#back_top').css('display','none');}
		});
		if(jQuery(window).scrollTop() !== 0){jQuery('#back_top').css('display','block');}else{jQuery('#back_top').css('display','none');}
		
		jQuery("#main").fitVids();
	});
})(jQuery);