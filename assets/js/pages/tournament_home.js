simkit.app.controller("tournamentHome", function($scope, $http, $window, $timeout){
	$scope.data = {};

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
});