simkit.app.controller("beginMatch", function($scope, $http, $element, $timeout, $location, $window){		
	$scope.data = {};
	$scope.showSecondInningsScorecard = false;

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
});