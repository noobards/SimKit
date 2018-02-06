simkit.app.controller("matchCenter", function($scope, $http, $window, $timeout){
	$scope.data = {};
	$scope.data.home = 0;
	$scope.data.away = 0;
	$scope.data.home_label = "";
	$scope.data.away_label = "";
	$scope.data.players_selected = false;
	$scope.data.home_eleven  = [];
	$scope.data.away_eleven = [];	
	$scope.data.pitch = "Green";

	var cont_home = document.getElementById('sortHomeBattingOrder');
	var cont_away = document.getElementById('sortAwayBattingOrder');
	var sortable_home = Sortable.create(cont_home,
		{
			handle: ".handle",
			animation: 100,
			scroll:true,
			onEnd: function(evt){				
				var tr = jQuery('#sortHomeBattingOrder > .tr');				
				var temp = [];
				for(var i = 0; i < tr.length; i++)
				{
					var item = jQuery(tr[i]);
					var player_name = jQuery.trim(item.find('> .td').text());
					for(var j = 0; j < $scope.data.home_eleven.length; j++)
					{
						var item = $scope.data.home_eleven[j];
						if(jQuery.trim(item.name) == player_name)
						{
							temp.push(item);
							break;
						}
					}
				}				
				$scope.data.home_eleven = temp;
			}
		}
	);
	var sortable_away = Sortable.create(cont_away,
		{
			handle: ".handle",
			animation: 100,
			scroll:true,
			onEnd: function(evt){				
				var tr = jQuery('#sortAwayBattingOrder > .tr');
				var temp = [];
				for(var i = 0; i < tr.length; i++)
				{
					var item = jQuery(tr[i]);
					var player_name = jQuery.trim(item.find('> .td').text());
					for(var j = 0; j < $scope.data.away_eleven.length; j++)
					{
						var item = $scope.data.away_eleven[j];
						if(jQuery.trim(item.name) == player_name)
						{
							temp.push(item);
							break;
						}
					}
				}				
				$scope.data.away_eleven = temp;
			}
		}
	);
	
	$http({
		method: 'get',
		url: simkit.baseUrl+'MatchCenter/initializeData'		
	}).then(function success(resp){
		if(resp.statusText == "OK")
		{
			$scope.data.teams = resp.data.teams;
			$scope.data.match_types = resp.data.match_types;
			$scope.data.commentators = [{'Id':'1', 'label':'System (Default)'}];
			$scope.data.pitch_types = resp.data.pitch_types;
			
			$scope.data.team_count = $scope.data.teams.length;			
			$scope.data.m_type = $scope.data.match_types[0];
			$scope.data.p_type = $scope.data.pitch_types[0];
			$scope.data.comm = $scope.data.commentators[0];

			if($scope.data.teams.length > 0)
			{
				$scope.data.home = $scope.data.teams[0].team_id;
				$scope.data.away = $scope.data.teams[0].team_id;

				$scope.data.home_label = $scope.data.teams[0].team_name;
				$scope.data.away_label = $scope.data.teams[0].team_name;
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
	
	$scope.deleteMatch = function(e){
		var icon = jQuery(e.currentTarget);
		var mid = icon.attr('data-mid');
		if(mid)
		{
			$scope.ajax = {};
			$scope.ajax.mid = mid;
			if(window.confirm('Are you sure you want to delete this match?'))
			{
				$http({
					method:'post',
					url:simkit.baseUrl+'MatchCenter/deleteMatch',
					data:$scope.ajax
				}).then(function success(response){
					if(response.statusText == 'OK')
					{
						if(response.data.status == 'OK')
						{
							icon.closest('.tr').fadeOut(800, function(){
								jQuery(this).remove();
							});
						}
						else
						{
							alert(response.data.msg);
						}
					}
					else
					{
						alert(response.statusText);
					}
				}, function error(response){
					alert(response.statusText);
				}).then(function complete(){

				});
			}
		}
	};
		
	jQuery('#config_match').on('show.bs.collapse','.collapse', function() {
		jQuery('#config_match').find('.collapse.in').collapse('hide');
	});
	
	$scope.goNext = function(e, panel_id){
		var button = jQuery(e.currentTarget);
		var current_panel = button.attr('data-current-panel');
		if(current_panel == 'panel_1')
		{
			// check if home and away teams are same
			if($scope.data.home == $scope.data.away)
			{				
				alert("Home and Away teams cannot be same");
				return false;
			}
			$scope.data.home_eleven.length = 0;
			$scope.data.away_eleven.length = 0;
			$scope.loading("panel_1");
			$http({
				method: 'post',
				data: $scope.data,
				url: simkit.baseUrl+'MatchCenter/getCompetingTeamPlayers'
			}).then(function success(resp){
				if(resp.statusText == "OK")
				{
					if(resp.data.status == "OK")
					{
						if(resp.data.db.length == 2)
						{
							for(var i = 0; i < resp.data.db.length; i++)
							{
								var t = resp.data.db[i];								
								
								if(t.players)
								{
									if(t.players.length < 11)
									{
										alert("Selected teams need to have 11 players (at the very least) in the rooster.");
										return false;
									}
								}
								else
								{
									alert("Selected teams need to have 11 players (at the very least) in the rooster.");
									return false;
								}
								
								
							}							
							$scope.data.team_players = resp.data.db;						
							jQuery('#config_match #panel_2').collapse('show');
						}
						else
						{
							alert("Need to select 2 teams to proceed futher");
						}
						
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
				$scope.finish("panel_1");
			});
		}
		else if(current_panel == 'panel_2')
		{
			// check if 11 players have been selected from both teams
			var home_cnt = $scope.data.home_eleven.length;
			var away_cnt = $scope.data.away_eleven.length;
			if(home_cnt != 11 || away_cnt != 11)
			{
				alert("11 players must be selected from Home and Away team.");
				return false;
			}
			else
			{
				// check both teams have at least 5 bowlers
				var cnt = 0, can_proceed = true, err = "";
				angular.forEach($scope.data.home_eleven, function(obj){
					if(jQuery.inArray(obj.role_id, ['2', '3', '4']) !== -1)
					{
						cnt++;
					}

					if(cnt >= 5)
					{
						return;
					}
				});
				if(cnt < 5)
				{
					can_proceed = false;
					err += "Home team needs to have at least 5 players who can bowl. Currently, only "+cnt+" players can bowl.\n\n";
				}
				var cnt = 0;
				angular.forEach($scope.data.away_eleven, function(obj){
					if(jQuery.inArray(obj.role_id, ['2', '3', '4']) !== -1)
					{
						cnt++;
					}

					if(cnt >= 5)
					{
						return;
					}
				});
				if(cnt < 5)
				{
					can_proceed = false;
					err += "Away team needs to have at least 5 players who can bowl. Currently, only "+cnt+" players can bowl.\n\n";
				}
				if(can_proceed)
				{
					jQuery('#config_match #panel_3').collapse('show');
				}
				else
				{
					alert(err);
				}
			}
		}
		else if(current_panel == 'panel_3')
		{
			jQuery('#config_match #panel_4').collapse('show');
		}
		else if(current_panel == 'panel_4')
		{
			if($scope.data.ground && $scope.data.pitch)
			{
				if($scope.data.m_type.Id == 1)
				{
					$scope.data.overs = 50;
				}
				else
				{
					$scope.data.overs = 20;
				}
				jQuery('#config_match #panel_5').collapse('show');
			}
			else
			{
				alert("Ground name needs to be filled in.");
				jQuery('#ground').focus();
			}
		}
		
	};

	$scope.goPrev = function(e, panel_id){
		jQuery('#config_match #'+panel_id).collapse('show');
	};

	$scope.playerSelect = function(e){
		var cb = jQuery(e.target);		
		var tr = cb.closest('.tr');
		var table  = cb.closest('.table-mockup');
		var mode = table.attr('data-mode');
		
		var icon = cb.attr('data-icon');
		var type = cb.attr('data-role');
		var name = cb.attr('data-name');
		var pid = cb.attr('data-pid');
		var role_id = cb.attr('data-role_id');
		var ment_label = cb.attr('data-ment_label');
		var ment_icon = cb.attr('data-ment_icon');
		if(cb.is(':checked'))
		{
			tr.addClass('selected');
			if(mode == 'home')
			{
				$scope.data.home_eleven.push({'name':name, 'type':type, 'pid':pid, 'icon':icon, 'role_id':role_id, 'ment_label':ment_label, 'ment_icon':ment_icon});
			}
			else if(mode == 'away')
			{
				$scope.data.away_eleven.push({'name':name, 'type':type, 'pid':pid, 'icon':icon, 'role_id':role_id, 'ment_label':ment_label, 'ment_icon':ment_icon});
			}
		}
		else
		{
			tr.removeClass('selected');			
			if(mode == 'home')
			{
				$scope.removeFromEleven($scope.data.home_eleven, pid);
			}
			else if(mode == 'away')
			{
				$scope.removeFromEleven($scope.data.away_eleven, pid);
			}
			
		}
		var cnt = 0;
		if(mode == 'home')
		{
			cnt = $scope.data.home_eleven.length;
		}
		else if(mode == 'away')
		{
			cnt = $scope.data.away_eleven.length;
		}
		
		table.siblings('.sel-count').find('.selection_number').html(cnt+ ' selected');
		if(cnt == 11)
		{
			table.siblings('.sel-count').find('.selection_number').addClass('green').removeClass('red');
			table.find(':checkbox').not(':checked').attr('disabled', 'disabled');
		}
		else if(cnt == 0)
		{
			table.siblings('.sel-count').find('.selection_number').removeClass('green').removeClass('red');
		}
		else
		{
			table.siblings('.sel-count').find('.selection_number').removeClass('green').addClass('red');
			table.find(':checkbox').not(':checked').removeAttr('disabled');
		}
		
		if($scope.data.home_eleven.length == 11 && $scope.data.away_eleven.length == 11)
		{
			$scope.data.players_selected = true;
		}
		else
		{
			$scope.data.players_selected = false;
		}		
		
	};
	
	$scope.removeFromEleven = function(bank, needle){
		if(bank.length > 0)
		{
			for(var i = 0; i < bank.length; i++)
			{
				var player = bank[i];
				if(player.pid == needle)
				{					
					bank.splice(i, 1);
				}
			}
		}
	};
	
	$scope.moveUp = function(e, mode){
		var margin_top = jQuery('.list_of_teams').height();		
		var arrow = jQuery(e.currentTarget);		
		var cont = arrow.siblings('.list_of_teams');
		var active_box = cont.find('.team.team_active');
		var index = active_box.attr('data-index');
		
		if(index == $scope.data.team_count)
		{			
			return false;
		}
		else
		{
			cont.siblings('.arrow_down').removeClass('arrow_disabled');
			if(index == ($scope.data.team_count - 1))
			{
				cont.siblings('.arrow_up').addClass('arrow_disabled');
			}
			else
			{
				cont.siblings('.arrow_up').removeClass('arrow_disabled');
			}
			
			active_box.css({
				marginTop: -(margin_top)+'px'
			});
			active_box.next().addClass('team_active').css('marginTop', '0px');
			active_box.removeClass('team_active');

			$scope.setHomeAway(mode, cont);
		}
	};
	
	$scope.moveDown = function(e, mode){
		var margin_top = jQuery('.list_of_teams').height();	
		var arrow = jQuery(e.currentTarget);
		var cont = arrow.siblings('.list_of_teams');
		var active_box = cont.find('.team.team_active');
		var index = active_box.attr('data-index');
		if(index == '1')
		{			
			return false;
		}
		else
		{
			cont.siblings('.arrow_up').removeClass('arrow_disabled');
			if(index == '2')
			{
				cont.siblings('.arrow_down').addClass('arrow_disabled');
			}
			else
			{
				cont.siblings('.arrow_down').removeClass('arrow_disabled');
			}
			
			active_box.css({
				marginTop: (margin_top)+'px'
			});
			active_box.prev().addClass('team_active').css('marginTop', '0px');
			active_box.removeClass('team_active');

			$scope.setHomeAway(mode, cont);
		}
	};

	$scope.setHomeAway = function(type, box){
		if(type == 'home')
		{
			$scope.data.home = box.find('.team.team_active').attr('data-tid');
			$scope.data.home_label = box.find('.team.team_active').attr('data-team');
		}
		else if(type == 'away')
		{
			$scope.data.away = box.find('.team.team_active').attr('data-tid');
			$scope.data.away_label = box.find('.team.team_active').attr('data-team');
		}
		else
		{
			$scope.data.home = 0;
			$scope.data.away = 0;
			$scope.data.home_label = "";
			$scope.data.away_label = "";
		}
	};
	
	$scope.randomSelection = function(e, mode){
		e.preventDefault();
		var table = null;
		if(mode == 'home')
		{
			table = jQuery('.table-mockup[data-mode="home"]');
			var tr = table.find('.tbody .tr');
			tr.removeClass('selected');
			angular.forEach(tr.find('.select-player-cb'), function(c, i){
				if(jQuery(c).is(':checked'))
				{
					// required when trigger ng-click programatically
					$timeout(function(){
						jQuery(c).trigger('click');
					}, 0, false);
				}
			});
			if(tr.length >= 11)
			{				
				if(tr.length == 11) // select all
				{
					// required when trigger ng-click programatically
					$timeout(function(){
						tr.find('.select-player-cb').trigger('click');
					}, 0, false);					
				}
				else
				{
					var total_available_players = tr.length;
					var bank = [];
					for(var i = 0; i < total_available_players; i++)
					{
						bank.push(i + 1);
					}
					
					var total_needed = 11;
					var row_nums = [];
					while(total_needed > 0)
					{
						var index = Math.floor(Math.random() * (bank.length)) + 0;
						row_nums.push(bank.splice(index, 1)[0]);
						total_needed--;
					}					
					
					jQuery.each(tr, function(i, row){
						for(var j = 0; j < row_nums.length; j++)
						{
							var item = row_nums[j];
							if(item == (i + 1))
							{
								// required when trigger ng-click programatically
								$timeout(function(){
									jQuery(row).find('.select-player-cb').trigger('click');
								}, 0, false);
								
							}
						}
						
					});
					
				}
			}
		}
		else if(mode == 'away')
		{
			table = jQuery('.table-mockup[data-mode="away"]');
			var tr = table.find('.tbody .tr');
			tr.removeClass('selected');
			angular.forEach(tr.find('.select-player-cb'), function(c, i){
				if(jQuery(c).is(':checked'))
				{
					// required when trigger ng-click programatically
					$timeout(function(){
						jQuery(c).trigger('click');
					}, 0, false);
				}
			});
			if(tr.length >= 11)
			{				
				if(tr.length == 11) // select all
				{
					$timeout(function(){
						tr.find('.select-player-cb').trigger('click');
					}, 0, false);
				}
				else
				{
					var total_available_players = tr.length;
					var bank = [];
					for(var i = 0; i < total_available_players; i++)
					{
						bank.push(i+1);
					}
					
					var total_needed = 11;
					var row_nums = [];
					while(total_needed > 0)
					{
						var index = Math.floor(Math.random() * (bank.length)) + 0;
						row_nums.push(bank.splice(index, 1)[0]);
						total_needed--;
					}					
					
					jQuery.each(tr, function(i, row){
						for(var j = 0; j < row_nums.length; j++)
						{
							var item = row_nums[j];
							if(item == (i+1))
							{
								// required when trigger ng-click programatically
								$timeout(function(){
									jQuery(row).find('.select-player-cb').trigger('click');
								}, 0, false);
								
							}
						}
						
					});
					
				}
			}
		}
	};
			
	$scope.setMatch = function(e){
		var button = jQuery(e.currentTarget);
		var buttonText = button.html();
		$scope.loading('final_panel');
		button.html('Processing...').attr('disabled', 'disabled');
		
		$http({
			method: 'post',
			url: simkit.baseUrl+"MatchCenter/setMatch",
			data:$scope.data
		}).then(function success(res){
			if(res.statusText == "OK")
			{
				if(res.data.status == 'OK')
				{
					button.html('Redirecting...');
					$window.location.href = "MatchCenter/PreMatch/"+res.data.match_id;
				}
				else
				{
					alert(res.data.msg);
				}
			}
			else
			{
				alert(res.statusText);
			}
		}, function error(res){
			alert(res.statusText);
		}).then(function complete(){
			button.removeAttr('disabled').html(buttonText);
			$scope.finish('final_panel');
		});
	};
});