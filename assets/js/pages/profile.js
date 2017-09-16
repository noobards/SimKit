simkit.app.controller("profile", function($scope, $http, $window){	
	$scope.data = {};		
	$scope.data.tz = "Asia/Kolkata";
	
	$scope.loading("save_preferences_form");
	
	$http({
		url: simkit.baseUrl+'Account/getProfileData',
		method: 'get'		
	}).then(function success(response){
		if(response.statusText == "OK")
		{
			if(response.data.status == 'OK')
			{
				var data = response.data.user;
				$scope.data.un = data.username;
				$scope.data.em = data.email;
				$scope.data.fn = data.first_name;
				$scope.data.ln = data.last_name;
				$scope.data.dob = data.dob;
				$scope.data.gender = data.gender;
				$scope.data.tz = data.timezone;
			}
			else
			{
				alert(response.data.msg);
			}
			$scope.finish("save_preferences_form");
		}
		else
		{
			alert(response.statusText);
		}
	}, function error(response){
		alert(response.statusText);
	});
	
	$scope.savePreferences = function(e){
		var button = jQuery(e.target);
		var button_text = button.text();
		button.html('Processing...').attr('disabled', 'disabled');
		
		
		$http({
		url: simkit.baseUrl+'Account/saveProfileData',
		method: 'post',
		data: $scope.data
		}).then(function success(response){
			if(response.statusText == "OK")
			{
				if(response.data.status == 'OK')
				{
					button.html('Redirecting...');
					$window.location.href = "Profile";
				}
				else
				{
					button.html(button_text).removeAttr('disabled');
					alert(response.data.msg);
				}				
			}
			else
			{
				button.html(button_text).removeAttr('disabled');
				alert(response.statusText);
			}
		}, function error(response){
			button.html(button_text).removeAttr('disabled');
			alert(response.statusText);
		});
	};
});