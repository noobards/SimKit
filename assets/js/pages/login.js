var app = angular.module('SimKit', []);
app.controller('login', function($scope, $http){
	$scope.data = {};

	$scope.doLogin = function(e){
		var button = jQuery(e.target).find('.btn-login');
		button.attr('disabled', 'disabled').html("Authenticating...");
		return true;
	};
});