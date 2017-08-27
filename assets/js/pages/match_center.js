simkit.app.controller("listTeams", function($scope, $http, $window){
	$scope.data = {};
					$scope.removeTeamFromArray(team_id);
				$scope.removeTeamFromArray(team_id);
	
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
