simkit.app.controller("listSiteMembers", function($scope, $http){	
	
	$scope.data = {};
	$http({
		  method: 'GET',		  
		  url: simkit.baseUrl+'Admin/getSiteMembers'
	}).then(function successCallback(response) {
		if(response.statusText == "OK")
		{				
			$scope.data.site_members = response.data;
		}
		else
		{
			alert("Ajax Error: "+response.statusText);
		}			
	}, function errorCallback(response) {			
	    alert("Ajax Error: "+response.statusText);
	});
});