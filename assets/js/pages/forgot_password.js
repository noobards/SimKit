var app = angular.module("SimKit", []);
app.controller("forgotPassword", function($scope, $http){
	$scope.data = {};	
	$scope.showErrorAlert = false;
	$scope.showSuccessAlert = false;
	$scope.response = null;


	$scope.forgotPassword = function(e){
		$scope.showErrorAlert = false;
		$scope.showSuccessAlert = false;
		var button = jQuery(e.target);		
		var button_text = button.text();
		button.attr('disabled', 'disabled').html("Processing...");

		$http({
		  method: 'POST',
		  data:$scope.data,
		  url: simkit.baseUrl+'Login/retrievePassword'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{			
				if(response.data.status == "OK")
				{					
					$scope.data.em = null;					
					$scope.showSuccessAlert = true;
				}
				else
				{	
					$scope.showErrorAlert = true;
				}
				$scope.response = response.data.msg;				
			}
			else
			{
				$scope.response = response.statusText;				
				$scope.showErrorAlert = true;
			}
			button.removeAttr('disabled').html(button_text);
		}, function errorCallback(response) {
			button.removeAttr('disabled').html(button_text);
			$scope.response = response.statusText;
			$scope.showErrorAlert = true;
		});
	};

});