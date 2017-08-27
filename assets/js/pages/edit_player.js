simkit.app.controller("editPlayer", function($scope, $http){
	$scope.data = {speciality:{}};
	$scope.data.player_id = $.trim($('#pid').val());

	// get dropdown menu values from db
	$http({
	  method: 'GET',
	  url: simkit.baseUrl+'Players/dropdown_values'
	}).then(function successCallback(response) {
		if(response.statusText == "OK")
		{			
			if(response.data.countries)
			{
				if(response.data.countries.length > 0)
				{					
					$scope.countries = response.data.countries;
				}
			}

			if(response.data.player_types)
			{				
				if(response.data.player_types.length > 0)
				{					
					$scope.player_types = response.data.player_types;
				}
			}

			if(response.data.bowler_types)
			{				
				if(response.data.bowler_types.length > 0)
				{					
					$scope.bowler_types = response.data.bowler_types;
				}
			}
		}
		else
		{
			alert("Ajax Error Pull: "+response.statusText);
		}
	}, function errorCallback(response) {
	    alert("Ajax Error: "+response.statusText);
	});


	// get player data
	$http({
	  method: 'POST',
	  data: {
	  	pid: $scope.data.player_id
	  },
	  url: simkit.baseUrl+'Players/GetPlayerData'
	}).then(function successCallback(response) {
		if(response.statusText == "OK")
		{
			if(response.data.length > 0)			
			{
				var obj = response.data[0];
				$scope.data.fn = obj.first_name;
				$scope.data.ln = obj.last_name;
				$scope.data.age = obj.age;
				$scope.data.gender = obj.gender;
				$scope.data.nick = obj.nick_name;
				$scope.data.country = obj.country;				
				$scope.data.player_type = obj.player_type;
				$scope.data.bowler_type = obj.bowler_type;
				$scope.data.bat_hand = obj.batting_hand;
				$scope.data.bowl_hand = obj.bowling_hand;				
				$scope.data.speciality.test = (obj.test == "1" ? true : false);
				$scope.data.speciality.odi = (obj.odi == "1" ? true : false);
				$scope.data.speciality.t20 = (obj.t20 == "1" ? true : false);
				$scope.player_name = obj.first_name+' '+obj.last_name;
			}
			else
			{
				alert("Player data could not be found.");
			}			
		}
		else
		{
			alert("Ajax Error Data: "+response.statusText);
		}
	}, function errorCallback(response) {
	    alert("Ajax Error: "+response.statusText);
	});

	$scope.hasBowlingAbility = function(){
		return ($scope.data.player_type == 2 || $scope.data.player_type == 3 || $scope.data.player_type == 4); 
	};

	$scope.atLeastOneSelected = function (object) {
		if(object)
		{
			return Object.keys(object).some(function (key) {
			    return object[key];
			});
		}
		else
		{
			return false;
		}
	  
	};

	$scope.updatePlayer = function(e){
		var button = $(e.target);
		button.html("Processing...").attr('disabled', 'disabled');

		$http({
		  method: 'POST',
		  data: $scope.data,
		  url: simkit.baseUrl+'Players/UpdatePlayerData'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{
				if(response.data.status == 'OK')
				{
					button.html("Redirecting...");
					window.location.href = "../ListPlayers";
				}
				else
				{
					button.removeAttr('disabled').html('Update');
					alert(response.data.msg);
				}
			}
			else
			{
				button.removeAttr('disabled').html('Update');
				alert("Ajax Error Data: "+response.statusText);
			}
		}, function errorCallback(response) {
			button.removeAttr('disabled').html('Update');
		    alert("Ajax Error: "+response.statusText);
		});

	};
});