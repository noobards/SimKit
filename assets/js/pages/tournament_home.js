simkit.app.controller("tournamentHome", function($scope, $http, $window, $timeout){
	$scope.data = {};
	$scope.data.t_type = "-1";

	$scope.goToStep2 = function(e){
		var button = jQuery(e.currentTarget);
		var original_text = button.text();
		button.attr('disabled', 'disabled').html('Processing...');

		$http({
			url: simkit.baseUrl+'MatchCenter/setTournamentName',
			method: 'post',
			data:$scope.data
		}).then(function success(response){
			if(response.statusText == "OK")
			{
				if(response.data.status == 'OK')
				{
					//$scope.buttonNormal(button, original_text);
					//alert('done');
					button.text("Redirecting...");
					$window.location.href = simkit.baseUrl+'MatchCenter/SelectTeams/tourID/'+response.data.id;
				}
				else
				{
					$scope.buttonNormal(button, original_text);
					alert(response.data.msg);
				}				
			}
			else
			{
				$scope.buttonNormal(button, original_text);
				alert(response.statusText);
			}
		}, function error(response){
			$scope.buttonNormal(button, original_text);
			alert(response.statusText);
		});
	};

	$scope.calculatePPAndDeath = function(){
		var overs = parseInt($scope.data.t_noo, 10);
		if(isNaN(overs))
		{
			$scope.data.t_pp = null;
			$scope.data.t_do = null;			
		}
		else
		{
			if(overs > 50)			
			{
				$scope.data.t_pp = null;
				$scope.data.t_noo = null;
				$scope.data.t_do = null;
			}
			else
			{
				if(overs < 25)
				{
					$scope.data.t_pp = Math.floor((30*overs)/100);					
					$scope.data.t_do = Math.floor((75*overs)/100);	
				}
				else
				{
					$scope.data.t_pp = Math.floor((20*overs)/100);
					$scope.data.t_do = Math.floor((80*overs)/100);
				}
				
			}
		}
	};

	$scope.setNoO = function(){
		if($scope.data.t_type == 'ODI')
		{
			$scope.data.t_noo = 50;
		}
		else if($scope.data.t_type == 'T20')
		{
			$scope.data.t_noo = 20;
		}
		else if($scope.data.t_type == 'CUSTOM')
		{
			$scope.data.t_noo = 40;
		}
		else
		{
			$scope.data.t_noo = null;
		}

		$scope.calculatePPAndDeath();
	};
});