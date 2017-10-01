simkit.app.controller("simulateMatch", function($scope, $http, $window, $timeout, $element){		$scope.coin_tossed = false;			$scope.data = {};		$scope.data.decision_made = false;	$scope.data.home = $element.attr('data-home');	$scope.data.away = $element.attr('data-away');	$scope.data.home_label = $element.attr('data-home_label');	$scope.data.away_label = $element.attr('data-away_label');	$scope.data.match_id = $element.attr('data-match');		$scope.flipCoin = function(e){		var button = jQuery(e.currentTarget);		button.hide();		$scope.coin_tossed = true;		$scope.data.decision_made = false;		$timeout(function(){			$http({				method: 'post',				url: simkit.baseUrl+'MatchCenter/coinToss',				data:$scope.data			}).then(function success(res){				if(res.statusText == 'OK')				{					if(res.data.status == 'OK')					{						$scope.data.toss_win = res.data.toss;						$scope.data.toss_decision = res.data.decided_to;						$scope.data.toss_win_id = res.data.toss_win_id;												$scope.data.home_bowlers = res.data.home_bowlers;						$scope.data.away_bowlers = res.data.away_bowlers;						$scope.coin_tossed = false;						$scope.data.decision_made = true;					}					else					{						alert(res.data.msg);						$scope.coin_tossed = false;						button.show();					}				}				else				{					alert(res.statusText);					$scope.coin_tossed = false;					button.show();				}			}, function error(res){				alert(res.statusText);				$scope.coin_tossed = false;				button.show();			}).then(function complete(){							});					}, 750);	};		$scope.saveBowlingOrder = function(e){		var button = jQuery(e.currentTarget);		var order_set_num = 0;				var temp = [];		var conflict = [];		angular.forEach($scope.data.home_order, function(o, pid){						if(jQuery.inArray(o, temp) > -1)			{								conflict.push(pid);			}			else			{				temp.push(o);				order_set_num++;			}					});				var temp = [];				angular.forEach($scope.data.away_order, function(o, pid){			if(jQuery.inArray(o, temp) > -1)			{								conflict.push(pid);			}			else			{				temp.push(o);				order_set_num++;			}					});				if(conflict.length > 0)		{			var names = [];			angular.forEach(conflict, function(pid){				var select = jQuery('select[data-pid="'+pid+'"]');				var name = select.attr('data-pname');				names.push(name);			});						alert("The following players have a conflict in their bowling order. Please change them:\n"+names.join("\n"));		}		else		{			if(order_set_num != ($scope.data.home_bowlers.length + $scope.data.away_bowlers.length))			{				alert("The bowling order of all players must be set.\nCurrently set: "+order_set_num+"\nRemaning: "+(($scope.data.home_bowlers.length + $scope.data.away_bowlers.length) - order_set_num));							}			else			{				console.log($scope.data);			}		}	}});