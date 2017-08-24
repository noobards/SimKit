var app = angular.module("SimKit", []);
app.controller("addNewPlayer", function($scope, $http){	
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

	// not selected whether file upload or manual entry
	$scope.data.selection_made = false;

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
		table += $scope.getObjectName("Player Type", $scope.data.player_type, $scope.player_types);
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
		table += "</div>";
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
				window.location.href = "players";
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

	$scope.showForm = function(mode){
		if(mode == 'manual' || mode == 'upload')
		{
			if(mode == 'manual')
			{
				$scope.data.manual_entry = true;
				$scope.data.file_upload = false;				
			}
			else if(mode == 'upload')
			{
				$scope.data.manual_entry = false;
				$scope.data.file_upload = true;				
			}
			$scope.data.selection_made = true;
		}
		
	};

	$scope.showSelectionOptions = function(){
		$scope.data.file_upload = false;
		$scope.data.manual_entry = false;
		$scope.data.selection_made = false;
	};
});


app.controller("recentlyAddedPlayers", function($scope, $http){
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