jQuery(document).ready(function(){
	// mobile menu icon trigger
	jQuery('.mobile_menu_bars').on('click', function(e){		
		var mobileMenu = jQuery('#mobile_menu');		
		if(mobileMenu.hasClass('is_expanded'))
		{
			mobileMenu.removeAttr('style');
			mobileMenu.removeClass('is_expanded').slideUp(function(){
				jQuery(this).addClass('is_collapsed');
			});
		}
		else if(mobileMenu.hasClass('is_collapsed'))
		{	
			mobileMenu.addClass('is_expanded').removeClass('is_collapsed').hide().slideDown();;			
		}
	});
	
	// build the mobile nav
	var mobileMenu = jQuery('.main_menu > ul').clone();
	mobileMenu.addClass('mobile_menu');
	jQuery('#mobile_menu').html(mobileMenu);
});