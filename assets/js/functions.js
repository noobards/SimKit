jQuery(document).ready(function(){
	jQuery('.mobile-nav-trigger').on('click', function(e){
		var trigger = jQuery(e.target);
		var mobileMenu = jQuery('#mobile-nav');
		if(mobileMenu.hasClass('menu-expanded'))
		{			
			mobileMenu.removeClass('menu-expanded').addClass('menu-collapsed');						
		}
		else if(mobileMenu.hasClass('menu-collapsed'))
		{			
			mobileMenu.addClass('menu-expanded').removeClass('menu-collapsed');			
		}
	});
});