simkit.app.controller("beginMatch", function($scope, $http, $element, $timeout, $location, $window){		
	$scope.data = {};
	$scope.edit = {};
	$scope.edit.home = {}, $scope.edit.away = {};
	$scope.save = {};
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

	$scope.editRatings = function(e){
		$scope.data.showPlayers = false;
		e.preventDefault();
		var anchor = jQuery(e.currentTarget);
		$scope.data.mid = parseInt(anchor.attr('data-mid'), 10);		
		if($scope.data.mid > 0)
		{
			$http({
				method:'post',
				url:simkit.baseUrl+'MatchCenter/getPlayerRatingsForEditPurpose',
				data:$scope.data
			}).then(function success(resp){
				if(resp.statusText == 'OK')
				{
					if(resp.data.status == 'OK')
					{
						$scope.edit.home.name = resp.data.players.home.label;
						$scope.edit.home.players = resp.data.players.home.players;
						angular.forEach($scope.edit.home.players, function(obj, key){
							$scope.save[obj.id] = {};
							$scope.save[obj.id]['bat'] = obj.bat;
							$scope.save[obj.id]['bowl'] = obj.ball;
							$scope.save[obj.id]['ment'] = obj.ment;
							$scope.save[obj.id]['name'] = obj.name;
						});

						$scope.edit.away.name = resp.data.players.away.label;
						$scope.edit.away.players = resp.data.players.away.players;
						angular.forEach($scope.edit.away.players, function(obj, key){
							$scope.save[obj.id] = {};
							$scope.save[obj.id]['bat'] = obj.bat;
							$scope.save[obj.id]['bowl'] = obj.ball;
							$scope.save[obj.id]['ment'] = obj.ment;
							$scope.save[obj.id]['name'] = obj.name;
						});

						$scope.data.showPlayers = true;
					}					
					else
					{
						alert(resp.data.msg);
					}
				}
				else
				{
					alert(resp.statusText);
				}
			}, function error(resp){
				alert(resp.statusText);
			}).then(function complete(){

			});
		}
	};

	$scope.updateRatings = function(e){
		var button = jQuery(e.currentTarget);
		var button_text = button.text();
		button.siblings('button').addClass('hide');
		button.html('Processing...').attr('disabled', 'disabled');
		$http({
			method:'post',
			url:simkit.baseUrl+'MatchCenter/updatePlayerRatings',
			data:$scope.save
		}).then(function success(response){
			if(response.statusText == 'OK')
			{
				if(response.data.status == 'OK')
				{
					button.html('Resimulating...');
					$timeout(function(){			
						button.html('Generating batting and bowling lineups...');
						$timeout(function() {
							button.html("Starting match...");
							$timeout(function() {
								$window.location.href = $location.absUrl();					
							}, 2000);
						}, 2000);			
					}, 1500);
				}
				else if(response.data.status == 'ERROR')
				{
					button.html(button_text).removeAttr('disabled');
					button.siblings('button').removeClass('hide');
					var error = response.data.error;
					var str = "The following players could not be updated because: \r\n\r\n";
					for(var i = 0; i < error.length; i++)
					{
						var p = error[i];
						str += p.name + ' - ' + p.reason+"\r\n";
					}
					alert(str);
				}
				else
				{
					button.html(button_text).removeAttr('disabled');
					button.siblings('button').removeClass('hide');
					alert(response.data.msg);
				}
			}
			else
			{
				button.html(button_text).removeAttr('disabled');
				button.siblings('button').removeClass('hide');
				alert(response.statusText);
			}
		}, function error(response){
			button.html(button_text).removeAttr('disabled');
			button.siblings('button').removeClass('hide');
			alert(response.statusText);
		}).then(function complete(){

		});
	};
});