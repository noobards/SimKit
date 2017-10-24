simkit.app.controller("editPlayer", function($scope, $http){

	$scope.data = {speciality:{}};

	$scope.data.player_id = $.trim($('#pid').val());

	$scope.data.max_rp = 100;
	$scope.data.initial_rp = 60;
	$scope.data.available_rp = 0;


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

				$scope.data.mentality = obj.mentality;

				$scope.data.bowler_type = obj.bowler_type;

				$scope.data.bat_hand = obj.batting_hand;

				$scope.data.bowl_hand = obj.bowling_hand;				

				$scope.data.speciality.test = (obj.test == "1" ? true : false);

				$scope.data.speciality.odi = (obj.odi == "1" ? true : false);

				$scope.data.speciality.t20 = (obj.t20 == "1" ? true : false);

				$scope.data.is_private = (obj.is_private == "1" ? true : false);

				$scope.data.batting_rp = obj.batting_rp;

				$scope.data.bowling_rp = obj.bowling_rp;

				$scope.data.fielding_rp = obj.fielding_rp;

				$scope.player_name = obj.first_name+' '+obj.last_name;

				$scope.data.available_rp = $scope.data.max_rp*3 - (parseInt($scope.data.batting_rp) + parseInt($scope.data.bowling_rp) + parseInt($scope.data.fielding_rp));

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

	$scope.plusRP = function(e){
		var obj = jQuery(e.target);
		var objtype = obj.get(0).tagName.toLowerCase();
        var span = null;
		if(objtype == 'i')
		{
			span = obj.parent();
		}
		else if(objtype == 'span' )
		{
			span = obj;
		}
		var current = null;
		var type = span.attr('data-type');
		if(type == 'batting')
		{
			current = parseInt($scope.data.batting_rp, 10);
			if(current < $scope.data.max_rp)
			{
				if($scope.data.available_rp > 0)
				{
					$scope.data.batting_rp = parseInt($scope.data.batting_rp, 10) + 1;
					$scope.data.available_rp = $scope.data.available_rp - 1;
				}
				else
				{
					alert("Rating points are not available. Decrease the rating point of bowling or fielding.");
				}				
			}
			else
			{
				alert("Batting rating point cannot be greater than "+$scope.data.max_rp);
			}
		}
		else if(type == 'bowling')
		{
			current = parseInt($scope.data.bowling_rp, 10);
			if(current < $scope.data.max_rp)
			{
				if($scope.data.available_rp > 0)
				{
					$scope.data.bowling_rp = parseInt($scope.data.bowling_rp, 10) + 1;
					$scope.data.available_rp -= 1;
				}
				else
				{
					alert("Rating points are not available. Decrease the rating point of batting or fielding.");
				}				
			}
			else
			{
				alert("Bowling rating point cannot be greater than "+$scope.data.max_rp);
			}
		}
		else if(type == 'fielding')
		{
			current = parseInt($scope.data.fielding_rp, 10);
			if(current < $scope.data.max_rp)
			{
				if($scope.data.available_rp > 0)
				{
					$scope.data.fielding_rp = parseInt($scope.data.fielding_rp, 10) + 1;
					$scope.data.available_rp -= 1;
				}
				else
				{
					alert("Rating points are not available. Decrease the rating point of batting or bowling.");
				}				
			}
			else
			{
				alert("Fielding rating point cannot be greater than "+$scope.data.max_rp);
			}
		}	
	};

	$scope.minusRP = function(e){
		var obj = jQuery(e.target);
		var objtype = obj.get(0).tagName.toLowerCase();
        var span = null;
		if(objtype == 'i')
		{
			span = obj.parent();
		}
		else if(objtype == 'span' )
		{
			span = obj;
		}
		var current = null;
		var type = span.attr('data-type');
		if(type == 'batting')
		{
			current = parseInt($scope.data.batting_rp, 10);
			if(current > 0)
			{
				$scope.data.batting_rp -= 1;
				$scope.data.available_rp = parseInt($scope.data.available_rp, 10) + 1;
			}
		}
		else if(type == 'bowling')
		{
			current = parseInt($scope.data.bowling_rp, 10);
			if(current > 0)
			{
				$scope.data.bowling_rp -= 1;
				$scope.data.available_rp = parseInt($scope.data.available_rp, 10) + 1;
			}
		}
		else if(type == 'fielding')
		{
			current = parseInt($scope.data.fielding_rp, 10);
			if(current > 0)
			{
				$scope.data.fielding_rp -= 1;
				$scope.data.available_rp = parseInt($scope.data.available_rp, 10) + 1;
			}
		}	
	};

	$scope.recalculateRP = function()	{		
		var total = parseInt($scope.data.batting_rp, 10) + parseInt($scope.data.bowling_rp, 10) + parseInt($scope.data.fielding_rp, 10);
		$scope.data.available_rp = $scope.data.max_rp*3 - total;
	};

	$scope.checkRPValidity = function(e){
		var val = jQuery(e.target).val();
		if(val > $scope.data.max_rp || (parseInt($scope.data.batting_rp, 10) + parseInt($scope.data.bowling_rp, 10) + parseInt($scope.data.fielding_rp, 10) > $scope.data.max_rp*3) )
		{
			var type = jQuery(e.target).attr('data-type');
			if(type == 'batting')
			{
				$scope.data.batting_rp = angular.copy($scope.data.initial_rp);
			}
			else if(type == 'bowling')
			{
				$scope.data.bowling_rp = angular.copy($scope.data.initial_rp);
			}
			else if(type == 'fielding')
			{
				$scope.data.fielding_rp = angular.copy($scope.data.initial_rp);
			}
			$scope.recalculateRP();
		}
	};

});
simkit.app.filter("roundFigure", function(){
	return function (input)
    {
    	if(! input){
    		return;
    	}
        return Math.ceil(input*10);
    }
});