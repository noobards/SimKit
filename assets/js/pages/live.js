simkit.app.controller("liveMatch", function($scope, $http, $element, $timeout, $interval, $location, $window){		
	$scope.data = {};
	$scope.edit = {};
	$scope.edit.home = {}, $scope.edit.away = {};
	$scope.save = {};
	$scope.showSecondInningsScorecard = false;
	$scope.data.debug = false;
	$scope.data.debug_text = "Show Debug Info";
	$scope.ajax = {};
	
	$scope.live = {};
	$scope.live.first_innings = {};
	$scope.live.first_innings.total = 0;
	$scope.live.first_innings.wickets = 0;
	$scope.live.first_innings.overs = "0.0";


	var path = window.location.pathname;
	path = path.split('/');
	$scope.ajax.mid = parseInt(path.pop());
	
	if($scope.ajax.mid > 0)
	{		
		$scope.loading();

		$http({
			method:'post',
			url:simkit.baseUrl+'MatchCenter/liveMatch',
			data:$scope.ajax
		}).then(function success(response){
			if(response.statusText == 'OK')
			{
				if(response.data.status == 'OK')
				{
					console.log(response.data);
					$scope.live.first_batting_label = response.data.first_batting_label;
					$scope.live.first_bowling_label = response.data.first_bowling_label;

					$scope.live.first_batting_order = response.data.first_batsmen;
					$scope.live.first_bowling_order = response.data.first_bowlers;

					var index = 0;					
					var number_of_deliveries = response.data.live.first.length;

					var interval = $interval(function() {
						if(index < number_of_deliveries)
						{
							var obj = response.data.live.first[index];
							var tr = jQuery('.first-bat > tr[data-id="'+obj.batsman_id+'"]');
							if(! $scope.live[obj.batsman_id])
							{
								$scope.live[obj.batsman_id] = {};
								$scope.live[obj.batsman_id].sixes = 0;
								$scope.live[obj.batsman_id].fours = 0;
								$scope.live[obj.batsman_id].balls_faced = 0;
								$scope.live[obj.batsman_id].runs = 0;								
							}

							
							var to_add_balls = 0;
							var total_add_runs = 0;
							var batsman_add_runs = 0;
							var wickets_to_add = 0;
							if(obj.type_of_ball == 'NOBALL' || obj.type_of_ball == 'WIDE')
							{								
								total_add_runs = 1;
							}
							else
							{
								to_add_balls = 1;

								if(obj.outcome == 'W')
								{
									$scope.live[obj.batsman_id].status = obj.status;
									wickets_to_add = 1;
								}
								else if(obj.outcome == '4')
								{
									$scope.live[obj.batsman_id].fours += 1;
									total_add_runs = 4;
									batsman_add_runs = 4;
								}
								else if(obj.outcome == '6')
								{
									$scope.live[obj.batsman_id].sixes += 1;
									total_add_runs = 6;
									batsman_add_runs = 6;
								}
								else
								{
									total_add_runs = parseInt(obj.runs);
									batsman_add_runs = parseInt(obj.runs);
								}

							}
														
							$scope.live[obj.batsman_id].runs += batsman_add_runs;
							$scope.live[obj.batsman_id].balls_faced += to_add_balls;
							
							$scope.live.first_innings.total += total_add_runs;
							$scope.live.first_innings.wickets += wickets_to_add;
								
							

							

							jQuery('.1st').append(obj.commentary);
							jQuery('.1st')[0].scrollTop = jQuery('.1st')[0].scrollHeight;


							// boundaries
							$scope.live[obj.batsman_id].boundaries = $scope.live[obj.batsman_id].fours+"/"+$scope.live[obj.batsman_id].sixes;

							// calculating SR
							$scope.live[obj.batsman_id].sr = ( ($scope.live[obj.batsman_id].runs*100)/$scope.live[obj.batsman_id].balls_faced ).toFixed(2);

							
						}
						else
						{	
							$interval.cancel(interval);
						}
						index++;
					}, 500);
					
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
			$scope.finish();
		});		
	}
	else
	{
		alert("Match ID not found");
	}
	

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
		if(window.confirm("Are you sure you want to resimulate the match?"))
		{
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
		}		
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

	$scope.batting = function(selector){
		var f_bat = jQuery(selector);
		var cell_width = 30;
		var str = "";
		str += new Array((cell_width*4)+10).join("-");
		str += "\r\n";
		str += new Array(6*7).join(" ");
		str += "BATTING SCORECARD";
		str += new Array(6*7).join(" ");
		str += "\r\n";
		str += new Array((cell_width*4)+10).join("-");
		str += "\r\n";

		str += "PLAYER NAME"+new Array(cell_width - 10).join(" ") + "STATUS"+new Array(cell_width - 5).join(" ")+ "RUNS (BALLS)"+new Array(cell_width - 11).join(" ")+ "4/6"+new Array(cell_width - 2).join(" ")+ "SR";
		str += "\r\n";
		str += new Array((cell_width*4)+10).join("-");
		str += "\r\n";

		for(var i = 1; i <= f_bat.length; i++)
		{
			if(i == f_bat.length)
			{
				str += new Array((cell_width*4)+10).join("-");
				str += "\r\n";
			}
			var tr = jQuery(f_bat[(i-1)]);
			
			var td = tr.find('.td');
			for(var j = 0; j < td.length; j++)
			{
				var col = jQuery(td[j]);
				var txt = jQuery.trim(col.find('.for-comm').text());	
				var num_of_spaces = 0;
				if(txt && txt.length > 0)										
				{
					num_of_spaces = cell_width - txt.length;
				}
				else
				{
					num_of_spaces = cell_width;
				}

				if(i == f_bat.length) // right align the total score text
				{
					str += new Array((cell_width*4 - txt.length) + 10).join(" ");
					str += txt;
				}
				else
				{
					str += txt;
					for(k = 1; k <= num_of_spaces; k++)
					{
						str += " ";
					}
				}				
				
			}
			
			str += "\r\n";
		}

		str += "\r\n";
		return str;
	};

	$scope.bowling = function(selector){
		var f_bat = jQuery(selector);
		var cell_width = 25;
		var str = "";
		str += new Array((cell_width*5)+10).join("-");
		str += "\r\n";
		str += new Array(6*7).join(" ");
		str += "BOWLING SCORECARD";
		str += new Array(6*7).join(" ");
		str += "\r\n";
		str += new Array((cell_width*5)+10).join("-");
		str += "\r\n";

		str += "PLAYER NAME"+new Array(cell_width - 10).join(" ") + "OVERS"+new Array(cell_width - 4).join(" ")+ "MAIDENS"+new Array(cell_width - 6).join(" ")+ "RUNS"+new Array(cell_width - 3).join(" ")+ "WICKETS"+new Array(cell_width - 6).join(" ")+ "ECONOMY";
		str += "\r\n";
		str += new Array((cell_width*5)+10).join("-");
		str += "\r\n";

		for(var i = 1; i <= f_bat.length; i++)
		{			
			var tr = jQuery(f_bat[(i-1)]);
			
			var td = tr.find('.td');
			for(var j = 0; j < td.length; j++)
			{
				var col = jQuery(td[j]);
				var txt = jQuery.trim(col.find('.for-comm').text());	
				var num_of_spaces = 0;
				if(txt && txt.length > 0)										
				{
					num_of_spaces = cell_width - txt.length;
				}
				else
				{
					num_of_spaces = cell_width;
				}
				
				str += txt;
				for(k = 1; k <= num_of_spaces; k++)
				{
					str += " ";
				}
			}
			
			str += "\r\n";
		}

		str += "\r\n\r\n\r\n\r\n";
		return str;
	};

	$scope.copyScorecard = function(e){
		var button = jQuery(e.currentTarget);
		var str = $scope.batting('.first-bat > .tr');
		str += $scope.bowling('.first-bowl > .tr');
		str += $scope.batting('.second-bat > .tr');
		str += $scope.bowling('.second-bowl > .tr');
		$scope.clipBoardCopy(str);
	};

	$scope.copyCommentary = function(e, div){
		var button = jQuery(e.currentTarget);
		var comm_div = jQuery('.'+div+' > div');

		var str = "";

		for(var i = 0; i < comm_div.length; i++)
		{
			var line_div = jQuery(comm_div[i]);
			if(line_div.hasClass('comm_line'))
			{
				str += line_div.find('.comm_over > span.over_number').text();
				str += "\t";
				str += line_div.find('.comm_desc').text();
				str += "\r\n";
			}
			else if(line_div.hasClass('comm_end_of_over'))
			{
				str += "\r\n"+line_div.text();
				str += "\r\n";
			}
			else if(line_div.hasClass('comm_over_stat'))
			{
				str += line_div.find('.over_stat1').text();
				str += "\r\n";
				str += line_div.find('.over_stat2').text();
				str += "\r\n";
				str += "\r\n";
			}
		}

		$scope.clipBoardCopy(str);
	};

	$scope.clipBoardCopy = function(str){
		
		$scope.clipboard = str;
		jQuery('#copyCommentary').modal('show');
	};
	
	$('#copyCommentary').on('shown.bs.modal', function () {
	  //document.getElementById('clipboard').select();
	});
});