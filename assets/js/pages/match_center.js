simkit.app.controller("listTeams", function($scope, $http, $window){	$scope.team1_text = "Not selected";	$scope.team2_text = "Not selected";	$scope.teams = [];
	$scope.data = {};	$scope.data.selected_teams = [];	$http({	  method: 'GET',	  	  url: simkit.baseUrl+'MatchCenter/getMyTeams'	}).then(function successCallback(response) {		if(response.statusText == "OK")		{						if(response.data.teams.length > 0)			{				while(response.data.teams.length > 0)				{					$scope.teams.push(response.data.teams.splice(0, 4));				}							}					}		else		{			alert("Ajax Error: "+response.statusText);		}				}, function errorCallback(response) {					alert("Ajax Error: "+response.statusText);	});		$scope.selectTeam = function(e){		var target = jQuery(e.target);		if(target.is('span') || target.is('strong'))		{			var box = target.parent();		}		else		{			var box = target;		}				var nop = parseInt(box.attr('data-player-count'), 10);		if(nop >= 15)		{			// count the number of boxes currently selected			var cnt = jQuery('#team_box_cont .team_box.team_selected').length;			var team_name = box.attr('data-team-name');			var team_id = box.attr('data-team-id');			if(cnt < 2)			{				if(box.hasClass('team_selected'))				{									box.removeClass('team_selected');					if($scope.team1_text == team_name)					{						$scope.team1_text = "Not selected";					}					else if($scope.team2_text == team_name)					{						$scope.team2_text = "Not selected";					}
					$scope.removeTeamFromArray(team_id);				}				else				{									box.addClass('team_selected');					if($scope.team1_text == "Not selected")					{						$scope.team1_text = team_name;											}					else if($scope.team2_text == "Not selected")					{						$scope.team2_text = team_name;					}					$scope.data.selected_teams.push(team_id);				}										}			else			{				box.removeClass('team_selected');				if($scope.team1_text == team_name)				{					$scope.team1_text = "Not selected";				}				else if($scope.team2_text == team_name)				{					$scope.team2_text = "Not selected";				}
				$scope.removeTeamFromArray(team_id);			}		}		else		{			alert("Team needs to have at least 15 players.");		}			};
	
	$scope.processForm = function(e){
		var button = jQuery(e.target);
		$scope.buttonLoading(button);
		
		$scope.ajax({data:$scope.data, url:'MatchCenter/saveStepOne', responseHandler:$scope.processFormHandler, target:button, buttonText:'Proceed'});
	};
	
	$scope.processFormHandler = function(response, button){
		if(response.status == 'OK'){
			$window.location.href = "MatchCenter/SquadSelection/"+response.id;
		} else {
			alert(response.msg);
			if(button !== undefined && button !== null)
			{
				button.removeAttr('disabled').html('Proceed');
			}			
		}		
	};
	
	
	$scope.removeTeamFromArray = function (item){
		var index = $scope.data.selected_teams.indexOf(item);
		if (index > -1) {
			$scope.data.selected_teams.splice(index, 1);
		}
	};
});

simkit.app.controller('savedMatches', function($scope, $element) {
	$scope.populateMatchList = function(response){
		$element.removeClass('ajax-loading');
		$scope.matches = response.matches;
	};
	$scope.ajax({type:'GET', url: 'MatchCenter/savedMatchList', responseHandler:$scope.populateMatchList});		
});

