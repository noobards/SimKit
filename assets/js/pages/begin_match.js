simkit.app.controller("beginMatch", function($scope, $http, $element, $timeout, $location, $window){		
	$scope.data = {};
	$scope.showSecondInningsScorecard = false;
	$scope.data.debug = false;
	$scope.data.debug_text = "Show Debug Info";

	$scope.startSecondInnings = function(e){
		var button = jQuery(e.currentTarget);
		button.attr('disabled', 'disabled').html('Changing Innings');
		$timeout(function(){			
			button.html('Simulating...');
			$timeout(function() {
				button.remove();
				$scope.data.showSecondInningsScorecard = true;
				$timeout(function() {
					jQuery('html,body').animate({
			        	scrollTop: jQuery("#secondInnings").offset().top},
			        'medium');
				});				
			}, 1500);			
		}, 1000);
	};

	$scope.resimulate = function(e){
		var button = jQuery(e.currentTarget);
		button.attr('disabled', 'disabled').html('Restarting match...');
		$timeout(function(){			
			button.html('Generating batting and bowling lineups...');
			$timeout(function() {
				button.html("Starting match...");
				$timeout(function() {
					$window.location.href = $location.absUrl();					
				}, 2000);
			}, 2000);			
		}, 1500);
	};

	$scope.debug = function(e)	{
		e.preventDefault();
		var anchor = jQuery(e.currentTarget);
		if($scope.data.debug)
		{
			$scope.data.debug_text = "Show Debug Info";
			$scope.data.debug = false;
		}
		else
		{
			$scope.data.debug_text = "Hide Debug Info";
			$scope.data.debug = true;
		}
	};
});