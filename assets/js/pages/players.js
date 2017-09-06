simkit.app.controller("addNewPlayer", function($scope, $http){	

	/*$scope.data = {

		"fn": "John",

		"ln": "Smith",

		"nick": "NL",

		"age": 28,

		"gender": "Male",

		"country": "3",

		"player_type":"2",

		"bowler_type": "3",

		"bat_hand": "Right",

		"bowl_hand": "Left",

		"speciality":{

			"test":true,

			"t20": true

		}

	};*/

	$scope.data = {};
	$scope.data.max_rp = 60;
	$scope.data.initial_rp = 40;
	$scope.data.batting_rp = angular.copy($scope.data.initial_rp);
	$scope.data.bowling_rp = angular.copy($scope.data.initial_rp);
	$scope.data.fielding_rp = angular.copy($scope.data.initial_rp);
	$scope.data.available_rp = $scope.data.max_rp*2 - ($scope.data.batting_rp + $scope.data.bowling_rp + $scope.data.fielding_rp);
	


	// get dropdown menu values from db

	$http({

	  method: 'GET',

	  url: simkit.baseUrl+'Players/dropdown_values',

	  dataType:'json'

	}).then(function successCallback(response) {

		if(response.statusText == "OK")

		{			

			if(response.data.countries)

			{

				if(response.data.countries.length > 0)

				{					

					$scope.countries = response.data.countries;					

				}

				else

				{

					alert('no countries');

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

			alert("Ajax Error: "+response.statusText);

		}

	}, function errorCallback(response) {

	    alert("Ajax Error: "+response.statusText);

	});





	$scope.showPlayerModal = function(){		

		var modal = $('#playerDataModal');

		var table = "<div class='row'><div class='col-md-6'>";

		table += "<table class='table'>";

		table += "<thead>";

		table += "<tr class='alert-warning'><th colspan='2'>Demographics</th>";

		table += "</thead>";

		table += "<tbody>";

		table += $scope.createRow("First Name", $scope.data.fn);

		table += $scope.createRow("Last Name", $scope.data.ln);

		table += $scope.createRow("Nickname", $scope.data.nick);

		table += $scope.createRow("Age", $scope.data.age);

		table += $scope.createRow("Gender", $scope.data.gender);

		table += $scope.getObjectName("Country", $scope.data.country, $scope.countries);

		table += "</tbody>";

		table += "</table>";

		table += "</div><div class='col-md-6'>";

		table += "<table class='table'>";

		table += "<thead>";

		table += "<tr class='alert-warning'><th colspan='2'>Attributes</th>";

		table += "</thead>";

		table += "<tbody>";

		table += $scope.getObjectName("Player Role", $scope.data.player_type, $scope.player_types);

		table += $scope.createRow("Batting Hand", $scope.data.bat_hand);

		if($scope.hasBowlingAbility())

		{

			table += $scope.createRow("Bowling Hand", $scope.data.bowl_hand);

			table += $scope.getObjectName("Bowler Type", $scope.data.bowler_type, $scope.bowler_types);

		}

		

		if($scope.data.speciality)

		{

			var formats = [];

			if($scope.data.speciality.test)

			{

				formats.push("Test");

			}

			if($scope.data.speciality.odi)

			{

				formats.push("ODI");

			}

			if($scope.data.speciality.t20)

			{

				formats.push("T20");

			}

			table += $scope.createRow("Speciality", formats.join(", "));

		}

		table += "</tbody>";

		table += "</table>";

		table += "</div></div>";

		table += "<div class='row'><div class='col-md-12'>";
		table += "<table class='table'>";
		table += "<thead>";
		table += "<tr class='alert-warning'><th colspan='2'>Rating Points</th>";
		table += "</thead>";
		table += "<tbody>";
		
		table += "<tr>";
		table += "<td style='width:30%;'><strong>Batting</strong></td>";
		table += "<td style='width:70%;'><div class='outer_bar'><div style='width:"+$scope.data.batting_rp+"%' class='inner_bar'></div><span class='bar_value'>"+$scope.data.batting_rp+"</span></div></td>";
		table += "</tr>";
		table += "<tr>";
		table += "<td><strong>Bowling</strong></td>";
		table += "<td><div class='outer_bar'><div style='width:"+$scope.data.bowling_rp+"%' class='inner_bar'></div><span class='bar_value'>"+$scope.data.bowling_rp+"</span></div></td>";
		table += "</tr>";
		table += "<tr>";
		table += "<td><strong>Fielding</strong></td>";
		table += "<td><div class='outer_bar'><div style='width:"+$scope.data.fielding_rp+"%' class='inner_bar'></div><span class='bar_value'>"+$scope.data.fielding_rp+"</span></div></td>";
		table += "</tr>";
		
		
		table += "</tbody>";
		table += "</table>";
		table += '</div></div>';

		modal.find('.modal-body').html(table);

		modal.modal('show');

		

	};



	$scope.hasBowlingAbility = function(){

		return ($scope.data.player_type == 2 || $scope.data.player_type == 3 || $scope.data.player_type == 4); 

	}



	$scope.createRow = function(label, value){

		var table = "";

		if(value)

		{

			table += "<tr>";

			table += "<td><strong>"+label+"</strong></td>";

			table += "<td>"+value+"</td>";

			table += "</tr>";

		}		

		return table;		

	};



	$scope.getObjectName = function(label, value, data){		

		var table = "";

		for(var i=0; i < data.length; i++)

		{

			var item = data[i];			

			if(item.id == value)

			{

				table += "<tr>";

				table += "<td><strong>"+label+"</strong></td>";

				table += "<td>"+item.name+"</td>";

				table += "</tr>";

				return table;

			}

		}

		return table;

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



	$scope.addNewPlayer = function(e){

		var button = $(e.target);

		button.html("Processing...").attr("disabled", "disabled");

		button.siblings().attr("disabled", "disabled");

		$http({

		  method: 'POST',

		  data: $scope.data,

		  url: simkit.baseUrl+'Players/save'

		}).then(function successCallback(response) {

			if(response.statusText == "OK")

			{				

				window.location.href = "Players";

			}

			else

			{

				alert("Ajax Error: "+response.statusText);

			}

			button.html("Save").removeAttr("disabled");

			button.siblings().removeAttr("disabled");

		}, function errorCallback(response) {

			button.html("Save").removeAttr("disabled");

			button.siblings().removeAttr("disabled");

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
		$scope.data.available_rp = $scope.data.max_rp*2 - total;
	};

	$scope.checkRPValidity = function(e){
		var val = jQuery(e.target).val();
		if(val > $scope.data.max_rp || (parseInt($scope.data.batting_rp, 10) + parseInt($scope.data.bowling_rp, 10) + parseInt($scope.data.fielding_rp, 10) > $scope.data.max_rp*2) )
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





simkit.app.controller("recentlyAddedPlayers", function($scope, $http){

	$http({

		  method: 'POST',		  

		  url: simkit.baseUrl+'Players/getPlayerList'

		}).then(function successCallback(response) {

			if(response.statusText == "OK")

			{				

				$scope.players = response.data;

			}

			else

			{

				alert("Ajax Error: "+response.statusText);

			}			

		}, function errorCallback(response) {			

		    alert("Ajax Error: "+response.statusText);

		});

});