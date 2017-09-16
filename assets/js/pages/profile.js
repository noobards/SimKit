simkit.app.controller("profile", function($scope, $http){	
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
});