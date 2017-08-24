var app = angular.module("SimKit", []);
app.controller("addNewTeam", function($scope, $http){
	$http({
	  method: 'POST',		  
	  url: simkit.baseUrl+'Teams/getTeamTypes'
	}).then(function successCallback(response) {
		if(response.statusText == "OK")
		{				
			$scope.team_types = response.data;
		}
		else
		{
			alert("Ajax Error: "+response.statusText);
		}			
	}, function errorCallback(response) {			
	    alert("Ajax Error: "+response.statusText);
	});

	$scope.addTeam = function(event){
		var button = $(event.target);
		button.html("Processing...").attr("disabled", "disabled");		
		$http({
		  method: 'POST',
		  data: $scope.data,
		  url: simkit.baseUrl+'Teams/save'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{				
				window.location.href = "teams";
			}
			else
			{
				alert("Ajax Error: "+response.statusText);
			}
			button.html("Save").removeAttr("disabled");			
		}, function errorCallback(response) {
			button.html("Save").removeAttr("disabled");			
		    alert("Ajax Error: "+response.statusText);
		});
	};
});

app.controller("myTeams", function($scope, $http){

	$scope.players = {};	

	$http({
	  method: 'POST',		  
	  url: simkit.baseUrl+'Teams/getTeamList'
	}).then(function successCallback(response) {
		if(response.statusText == "OK")
		{				
			$scope.my_teams = response.data;			
		}
		else
		{
			alert("Ajax Error: "+response.statusText);
		}			
	}, function errorCallback(response) {			
	    alert("Ajax Error: "+response.statusText);
	});

	$scope.addPlayersToTeamModal = function(e, team_id, team_name){
		e.preventDefault();
		$scope.selected_team = team_name;
		$scope.selected_team_id = team_id;

		$http({
		  method: 'POST',
		  data: {tid:team_id},
		  url: simkit.baseUrl+'Teams/getAvailablePlayers'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{
				$scope.my_players = response.data;				
				$('#listPlayers').modal('show');
			}
			else
			{
				alert("Ajax Error: "+response.statusText);
			}
			
		}, function errorCallback(response) {			
		    alert("Ajax Error: "+response.statusText);
		});
	};

	$scope.addToTeam = function(e){
		var button = $(e.target);

		$http({
		  method: 'POST',
		  data: {
		  	players: $scope.players,
		  	tid: $scope.selected_team_id
		  },
		  url: simkit.baseUrl+'Teams/addToTeam'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{
				if(response.data.status == 'OK')
				{
					window.location.href = "teams";
				}
				else
				{
					alert(response.data.msg);
				}				
			}
			else
			{
				alert("Ajax Error: "+response.statusText);
			}
			
		}, function errorCallback(response) {			
		    alert("Ajax Error: "+response.statusText);
		});
	};

	$scope.allCheck = function(e){
		var cb = $(e.target);
		//console.log($scope.selected_players);
	};
});