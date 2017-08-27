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

simkit.app = angular.module("SimKit", []);
simkit.app.controller("template", function($scope, $http){
	$scope.buttonLoading = function(button){
		button.attr('disabled', 'disabled').html('Processing...');
	};
	
	$scope.buttonNormal = function(button, text){
		button.removeAttr('disabled');
		if(text === undefined)
		{
			button.html("Submit");
		}
		else
		{
			button.html(text);
		}
	};
	
	$scope.ajax = function(obj){
		$http({
		  method: obj.type ? obj.type : 'POST',		  
		  url: simkit.baseUrl+obj.url,
		  data: obj.data ? obj.data : null,
		  dataType: 'json'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{				
				obj.responseHandler(response.data, (obj.target ? obj.target : null));
			}
			else
			{
				if(obj.target){
					obj.target.removeAttr('disabled').html(obj.buttonText ? obj.buttonText : 'Submit');
				}
				alert("Base Ajax Error: "+response.statusText);
			}
		}, function errorCallback(response) {			
			if(obj.target){
				obj.target.removeAttr('disabled').html(obj.buttonText ? obj.buttonText : 'Submit');
			}
			alert("Base Ajax Error: "+response.statusText);
		});
	}
});

simkit.app.filter('readableDateTime', function($filter){
  return function (val){
    return $filter('date')(new Date(val), 'MM/dd/yyyy H:m:s');
  }
});