simkit.app.controller("matchCenter", function($scope, $http, $window, $timeout){	$scope.data = {};	$scope.data.home = 0;	$scope.data.away = 0;	$scope.data.home_label = "";	$scope.data.away_label = "";	$scope.data.players_selected = false;	$scope.data.home_eleven  = [];	$scope.data.away_eleven = [];		$scope.data.pitch = "Green";	$http({		method: 'get',		url: simkit.baseUrl+'MatchCenter/initializeData'			}).then(function success(resp){		if(resp.statusText == "OK")		{			$scope.data.teams = resp.data.teams;			$scope.data.match_types = resp.data.match_types;			$scope.data.pitch_types = resp.data.pitch_types;						$scope.data.team_count = $scope.data.teams.length;						$scope.data.m_type = $scope.data.match_types[0];			$scope.data.p_type = $scope.data.pitch_types[0];			if($scope.data.teams.length > 0)			{				$scope.data.home = $scope.data.teams[0].team_id;				$scope.data.away = $scope.data.teams[0].team_id;				$scope.data.home_label = $scope.data.teams[0].team_name;				$scope.data.away_label = $scope.data.teams[0].team_name;			}					}		else		{			alert(resp.statusText);		}	}, function error(resp){		alert(resp.statusText);	}).then(function complete(){			});			jQuery('#config_match').on('show.bs.collapse','.collapse', function() {		jQuery('#config_match').find('.collapse.in').collapse('hide');	});		$scope.goNext = function(e, panel_id){		var button = jQuery(e.currentTarget);		var current_panel = button.attr('data-current-panel');		if(current_panel == 'panel_1')		{			// check if home and away teams are same			if($scope.data.home == $scope.data.away)			{								alert("Home and Away teams cannot be same");				return false;			}			$scope.loading("panel_1");			$http({				method: 'post',				data: $scope.data,				url: simkit.baseUrl+'MatchCenter/getCompetingTeamPlayers'			}).then(function success(resp){				if(resp.statusText == "OK")				{					if(resp.data.status == "OK")					{						$scope.data.team_players = resp.data.db;												jQuery('#config_match #panel_2').collapse('show');					}					else					{						alert(resp.data.msg);					}				}				else				{					alert(resp.statusText);				}			}, function error(resp){				alert(resp.statusText);			}).then(function complete(){				$scope.finish("panel_1");			});		}		else if(current_panel == 'panel_2')		{			// check if 11 players have been selected from both teams			var home_cnt = $scope.data.home_eleven.length;			var away_cnt = $scope.data.away_eleven.length;			if(home_cnt != 11 || away_cnt != 11)			{				alert("11 players must be selected from Home and Away team.");				return false;			}			else			{				jQuery('#config_match #panel_3').collapse('show');			}		}		else if(current_panel == 'panel_3')		{			jQuery('#config_match #panel_4').collapse('show');		}		else if(current_panel == 'panel_4')		{			if($scope.data.ground && $scope.data.pitch && $scope.data.overs)			{				jQuery('#config_match #panel_5').collapse('show');			}			else			{				alert("All fields are mandatory");			}		}			};	$scope.goPrev = function(e, panel_id){		jQuery('#config_match #'+panel_id).collapse('show');	};	$scope.playerSelect = function(e){		var cb = jQuery(e.target);				var tr = cb.closest('.tr');		var table  = cb.closest('.table-mockup');		var mode = table.attr('data-mode');				var icon = cb.attr('data-icon');		var type = cb.attr('data-role');		var name = cb.attr('data-name');		var pid = cb.attr('data-pid');		if(cb.is(':checked'))		{			tr.addClass('selected');			if(mode == 'home')			{				$scope.data.home_eleven.push({'name':name, 'type':type, 'pid':pid, 'icon':icon});			}			else if(mode == 'away')			{				$scope.data.away_eleven.push({'name':name, 'type':type, 'pid':pid, 'icon':icon});			}		}		else		{			tr.removeClass('selected');			console.log('remove');			if(mode == 'home')			{				$scope.removeFromEleven($scope.data.home_eleven, pid);			}			else if(mode == 'away')			{				$scope.removeFromEleven($scope.data.away_eleven, pid);			}					}		var cnt = 0;		if(mode == 'home')		{			cnt = $scope.data.home_eleven.length;		}		else if(mode == 'away')		{			cnt = $scope.data.away_eleven.length;		}				table.siblings('.sel-count').find('.selection_number').html(cnt+ ' selected');		if(cnt == 11)		{			table.siblings('.sel-count').find('.selection_number').addClass('green').removeClass('red');			table.find(':checkbox').not(':checked').attr('disabled', 'disabled');		}		else if(cnt == 0)		{			table.siblings('.sel-count').find('.selection_number').removeClass('green').removeClass('red');		}		else		{			table.siblings('.sel-count').find('.selection_number').removeClass('green').addClass('red');			table.find(':checkbox').not(':checked').removeAttr('disabled');		}				if($scope.data.home_eleven.length == 11 && $scope.data.away_eleven.length == 11)		{			$scope.data.players_selected = true;		}		else		{			$scope.data.players_selected = false;		}					};		$scope.removeFromEleven = function(bank, needle){		if(bank.length > 0)		{			for(var i = 0; i < bank.length; i++)			{				var player = bank[i];				if(player.pid == needle)				{										bank.splice(i, 1);				}			}		}	};		$scope.moveUp = function(e, mode){		var margin_top = jQuery('.list_of_teams').height();				var arrow = jQuery(e.currentTarget);				var cont = arrow.siblings('.list_of_teams');		var active_box = cont.find('.team.team_active');		var index = active_box.attr('data-index');				if(index == $scope.data.team_count)		{						return false;		}		else		{			cont.siblings('.arrow_down').removeClass('arrow_disabled');			if(index == ($scope.data.team_count - 1))			{				cont.siblings('.arrow_up').addClass('arrow_disabled');			}			else			{				cont.siblings('.arrow_up').removeClass('arrow_disabled');			}						active_box.css({				marginTop: -(margin_top)+'px'			});			active_box.next().addClass('team_active').css('marginTop', '0px');			active_box.removeClass('team_active');			$scope.setHomeAway(mode, cont);		}	};		$scope.moveDown = function(e, mode){		var margin_top = jQuery('.list_of_teams').height();			var arrow = jQuery(e.currentTarget);		var cont = arrow.siblings('.list_of_teams');		var active_box = cont.find('.team.team_active');		var index = active_box.attr('data-index');		if(index == '1')		{						return false;		}		else		{			cont.siblings('.arrow_up').removeClass('arrow_disabled');			if(index == '2')			{				cont.siblings('.arrow_down').addClass('arrow_disabled');			}			else			{				cont.siblings('.arrow_down').removeClass('arrow_disabled');			}						active_box.css({				marginTop: (margin_top)+'px'			});			active_box.prev().addClass('team_active').css('marginTop', '0px');			active_box.removeClass('team_active');			$scope.setHomeAway(mode, cont);		}	};	$scope.setHomeAway = function(type, box){		if(type == 'home')		{			$scope.data.home = box.find('.team.team_active').attr('data-tid');			$scope.data.home_label = box.find('.team.team_active').attr('data-team');		}		else if(type == 'away')		{			$scope.data.away = box.find('.team.team_active').attr('data-tid');			$scope.data.away_label = box.find('.team.team_active').attr('data-team');		}		else		{			$scope.data.home = 0;			$scope.data.away = 0;			$scope.data.home_label = "";			$scope.data.away_label = "";		}	};		$scope.randomSelection = function(e, mode){		e.preventDefault();		var table = null;		if(mode == 'home')		{			table = jQuery('.table-mockup[data-mode="home"]');			var tr = table.find('.tbody .tr');			tr.removeClass('selected');			angular.forEach(tr.find('.select-player-cb'), function(c, i){				if(jQuery(c).is(':checked'))				{					// required when trigger ng-click programatically					$timeout(function(){						jQuery(c).trigger('click');					}, 0, false);				}			});			if(tr.length >= 11)			{								if(tr.length == 11) // select all				{					// required when trigger ng-click programatically					$timeout(function(){						tr.find('.select-player-cb').trigger('click');					}, 0, false);									}				else				{					var total_available_players = tr.length;					var bank = [];					for(var i = 0; i < total_available_players; i++)					{						bank.push(i+1);					}										var total_needed = 11;					var row_nums = [];					while(total_needed > 0)					{						var index = Math.floor(Math.random() * (bank.length - 1)) + 0;						row_nums.push(bank.splice(index, 1)[0]);						total_needed--;					}															jQuery.each(tr, function(i, row){						for(var j = 0; j < row_nums.length; j++)						{							var item = row_nums[j];							if(item == i)							{								// required when trigger ng-click programatically								$timeout(function(){									jQuery(row).find('.select-player-cb').trigger('click');								}, 0, false);															}						}											});									}			}		}		else if(mode == 'away')		{			table = jQuery('.table-mockup[data-mode="away"]');			var tr = table.find('.tbody .tr');			tr.removeClass('selected');			angular.forEach(tr.find('.select-player-cb'), function(c, i){				if(jQuery(c).is(':checked'))				{					// required when trigger ng-click programatically					$timeout(function(){						jQuery(c).trigger('click');					}, 0, false);				}			});			if(tr.length >= 11)			{								if(tr.length == 11) // select all				{					$timeout(function(){						tr.find('.select-player-cb').trigger('click');					}, 0, false);				}				else				{					var total_available_players = tr.length;					var bank = [];					for(var i = 0; i < total_available_players; i++)					{						bank.push(i+1);					}										var total_needed = 11;					var row_nums = [];					while(total_needed > 0)					{						var index = Math.floor(Math.random() * (bank.length - 1)) + 0;						row_nums.push(bank.splice(index, 1)[0]);						total_needed--;					}															jQuery.each(tr, function(i, row){						for(var j = 0; j < row_nums.length; j++)						{							var item = row_nums[j];							if(item == i)							{								// required when trigger ng-click programatically								$timeout(function(){									jQuery(row).find('.select-player-cb').trigger('click');								}, 0, false);															}						}											});									}			}		}	};		$scope.shiftHomePlayer = function(e, dir, pid, current_index){		var arrow = jQuery(e.currentTarget);		arrow.closest('.tr').removeClass('selected').siblings('.tr').removeClass('selected');		var new_index = null;		current_index = parseInt(current_index, 10);				if(dir == 'up')		{			if(current_index > 0) // is not the first item			{				new_index = current_index - 1;				var moving_player = $scope.data.home_eleven[current_index];				var replacing_player = $scope.data.home_eleven[new_index];				$scope.data.home_eleven[current_index] = replacing_player;				$scope.data.home_eleven[new_index] = moving_player;				arrow.closest('.tr').addClass('selected');			}		}		else if(dir == 'down')		{			if(current_index < 10) // is not the last item			{				new_index = current_index + 1;				var moving_player = $scope.data.home_eleven[current_index];				var replacing_player = $scope.data.home_eleven[new_index];				$scope.data.home_eleven[current_index] = replacing_player;				$scope.data.home_eleven[new_index] = moving_player;				arrow.closest('.tr').addClass('selected');			}					}	};		$scope.shiftAwayPlayer = function(e, dir, pid, current_index){		var arrow = jQuery(e.currentTarget);		arrow.closest('.tr').removeClass('selected').siblings('.tr').removeClass('selected');		var new_index = null;		current_index = parseInt(current_index, 10);				if(dir == 'up')		{			if(current_index > 0) // is not the first item			{				new_index = current_index - 1;				var moving_player = $scope.data.away_eleven[current_index];				var replacing_player = $scope.data.away_eleven[new_index];				$scope.data.away_eleven[current_index] = replacing_player;				$scope.data.away_eleven[new_index] = moving_player;				arrow.closest('.tr').addClass('selected');			}		}		else if(dir == 'down')		{			if(current_index < 10) // is not the last item			{				new_index = current_index + 1;				var moving_player = $scope.data.away_eleven[current_index];				var replacing_player = $scope.data.away_eleven[new_index];				$scope.data.away_eleven[current_index] = replacing_player;				$scope.data.away_eleven[new_index] = moving_player;				arrow.closest('.tr').addClass('selected');			}					}	};		$scope.setMatch = function(e){		var button = jQuery(e.currentTarget);		var buttonText = button.html();		$scope.loading('final_panel');		button.html('Processing...').attr('disabled', 'disabled');				$http({			method: 'post',			url: simkit.baseUrl+"MatchCenter/setMatch",			data:$scope.data		}).then(function success(res){			if(res.statusText == "OK")			{				if(res.data.status == 'OK')				{					button.html('Redirecting...');					$window.location.href = "MatchCenter/PreMatch/"+res.data.match_id;				}				else				{					alert(res.data.msg);				}			}			else			{				alert(res.statusText);			}		}, function error(res){			alert(res.statusText);		}).then(function complete(){			button.removeAttr('disabled').html(buttonText);			$scope.finish('final_panel');		});	};});