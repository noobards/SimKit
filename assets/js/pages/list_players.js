simkit.app.controller("listPlayers", function($scope, $http, $window){
	$scope.my_players = [];
	$scope.showDeleteButton = false;

	$http({
	  method: 'GET',
	  url: simkit.baseUrl+'Players/getMyPlayers'
	}).then(function successCallback(response) {
		if(response.statusText == "OK")
		{			
			if(response.data.length > 0)
			{
				$scope.my_players = response.data;
			}
		}
		else
		{
			alert("Ajax Error: "+response.statusText);
		}
	}, function errorCallback(response) {
	    alert("Ajax Error: "+response.statusText);
	});

	$scope.selectAll = function(){
		var total_selected = 0;
		if($scope.check_all){
			angular.forEach($scope.filtered, function (model) {
				model.selected = true;
				total_selected++;
			});
		} else {
			angular.forEach($scope.filtered, function (model) {
				model.selected = false;
			});
		}
		if(total_selected > 0)		
		{
			$scope.showDeleteButton = true;
		}
		else
		{
			$scope.showDeleteButton = false;
		}
	};

	$scope.selectSingle = function(e){
		var cb = jQuery(e.target);
		if(cb.is(':checked'))
		{
			$scope.showDeleteButton = true;
		}
		else
		{
			if(jQuery('.nl-checkbox:checked').length == 0)
			{
				$scope.showDeleteButton = false;
			}
		}
	};

	$scope.removeSelected = function(e){
		var button = jQuery(e.target);
		$scope.to_delete = [];
		angular.forEach($scope.filtered, function (model) {
			if(model.selected == true)
			{
				$scope.to_delete.push({'id': model.id, 'name': model.name});
			}
		});
		if($scope.to_delete.length > 0)
		{
			if(window.confirm("Are you sure you want to remove the selected players?"))
			{
				$http({
				  method: 'POST',
				  url: simkit.baseUrl+'Players/removeSeletedPlayers',
				  data:$scope.to_delete
				}).then(function successCallback(response) {
					if(response.statusText == "OK")
					{			
						if(response.data.status == 'OK')
						{
							$window.location.href = response.data.redirect;
						}
						else
						{
							alert(response.data.msg);
							$window.location.href = response.data.redirect;
						}
					}
					else
					{
						alert("Ajax Error: "+response.statusText);
					}
				}, function errorCallback(response) {
				    alert("Ajax Error: "+response.statusText);
				});
			}
		}
	};
});