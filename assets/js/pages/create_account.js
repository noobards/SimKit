var app = angular.module("SimKit", []);
app.controller("register", function($scope, $http){
	$scope.data = {};

	$scope.createAccount = function(e){
		var button = $(e.target);

		if($scope.data.pw != $scope.data.cpw)
		{
			alert("Password and Confirm Password no not match. Please check again.");
		}
		else
		{
			button.attr('disabled', 'disabled').html("Processing...");

			$http({
			  method: 'POST',
			  data:$scope.data,
			  url: simkit.baseUrl+'Login/createAccount'
			}).then(function successCallback(response) {
				if(response.statusText == "OK")
				{			
					if(response.data.status == "OK")
					{
						window.location.href = "../../";
					}
					else
					{
						button.removeAttr('disabled').html("Create Account");
						alert("Ajax Error: "+response.data.msg);
					}

				}
				else
				{
					button.removeAttr('disabled').html("Create Account");
					alert("Ajax Error: "+response.statusText);
				}			
			}, function errorCallback(response) {
				button.removeAttr('disabled').html("Create Account");
			    alert("Ajax Error: "+response.statusText);
			});
		}
	};
});