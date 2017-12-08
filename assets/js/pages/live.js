simkit.app.controller("liveMatch", function($scope, $http, $element, $timeout, $interval, $location, $window){		
	$scope.data = {};
	$scope.edit = {};
	$scope.edit.home = {}, $scope.edit.away = {};
	$scope.save = {};
	$scope.showSecondInningsScorecard = false;
	$scope.data.debug = false;
	$scope.data.debug_text = "Show Debug Info";
	$scope.ajax = {};
	$scope.onload = {};
	
	$scope.live = {};
	$scope.live.first_innings = {};
	$scope.live.first_innings.total = 0;
	$scope.live.first_innings.wickets = 0;
	$scope.live.first_innings.overs = "0.0";
	$scope.live.first_innings.run_rate = "0.0 rpo";
	$scope.live.first_innings.fow = [];
	$scope.live.second_innings = {};
	$scope.live.second_innings.total = 0;
	$scope.live.second_innings.wickets = 0;
	$scope.live.second_innings.overs = "0.0";
	$scope.live.second_innings.run_rate = "0.0 rpo";
	$scope.live.second_innings.fow = [];

	var url = window.location.href;
	var idx = url.indexOf("mid");
	$scope.ajax.mid = parseInt(url.substring(idx).split("/")[1], 10);
	idx = url.indexOf("delay");
	$scope.ajax.delay = parseInt(url.substring(idx).split("/")[1], 10);
	idx = url.indexOf("stream");
	$scope.ajax.stream = url.substring(idx).split("/")[1];

	
	//$scope.ajax.mid = parseInt(document.getElementById('onload_mid').value, 10);
	//$scope.ajax.delay = parseInt(document.getElementById('onload_delay').value, 10);
	if($scope.ajax.delay == 0)
	{
		$scope.ajax.delay = 3;
	}

	
	if($scope.ajax.mid > 0)
	{
		if(jQuery('.not-ok').length == 0)
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
						$scope.live.response = response.data;
						$scope.startInOverlay();					
						$scope.startInnings('first');
						
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
	}
	else
	{
		alert("Match ID not found");
	}

	$scope.startInOverlay = function(){
		var div = jQuery('<div id="feedback"><span></span></div>');
		div.css({
			position:'fixed',
			width:'100%',
			height:'100%',
			backgroundColor:'rgba(0,0,0,0.7)',
			color:'#fff',
			fontSize:'120px',
			textAlign:'center',			
			top:0,
			left:0,
			display:'table'
		});

		var countdown = $scope.ajax.delay;
		div.find('span').css({display:'table-cell', verticalAlign:'middle'}).html(countdown);
		jQuery('body').append(div);

		var cd = setInterval(function(){
			countdown--;

			if(countdown > 0)
			{
				jQuery('#feedback').find('span').html(countdown);	
			}
			else
			{
				clearInterval(cd);
			}
		}, 1000);
	};

	$scope.startSecondInnings = function(e){
		var button = jQuery(e.currentTarget);
		button.attr('disabled', 'disabled').html('Changing Innings');
		$timeout(function(){						
			$timeout(function() {
				button.remove();
				$scope.data.showSecondInningsScorecard = true;
				$timeout(function() {
					jQuery('html,body').animate({
			        	scrollTop: jQuery("#secondInnings").offset().top},
			        'medium');

					$scope.startInOverlay();
			        $scope.startInnings('second');
				});				
			}, 1000);			
		}, 1000);
	};

	$scope.startInnings = function(innings_string){

		if(innings_string == 'first')
		{
			$scope.live.first_batting_label = $scope.live.response.first_batting_label;
			$scope.live.first_bowling_label = $scope.live.response.first_bowling_label;

			$scope.live.first_batting_order = $scope.live.response.first_batsmen;
			$scope.live.first_bowling_order = $scope.live.response.first_bowlers;
		}
		else if(innings_string == 'second')
		{
			$scope.live.second_batting_label = $scope.live.response.second_batting_label;
			$scope.live.second_bowling_label = $scope.live.response.second_bowling_label;

			$scope.live.second_batting_order = $scope.live.response.second_batsmen;
			$scope.live.second_bowling_order = $scope.live.response.second_bowlers;
		}

		

		var index = 0;					
		var legal_balls = 0;
		var number_of_deliveries = 0;
		var last_bowler_id = 0;
		var runs_in_over = 0;
		var obj = null;
		var obj2 = null;
		var bowl_tr, bat_tr = null;
		var countdown_span = "";		
		var countdown_seconds = 0;

		if(innings_string == 'first')
		{
			number_of_deliveries = $scope.live.response.live.first.length;
			countdown_span = jQuery('#first_countdown');
		}
		else if(innings_string == 'second')
		{
			number_of_deliveries = $scope.live.response.live.second.length;
			countdown_span = jQuery('#second_countdown');
		}
		countdown_span.html($scope.ajax.delay);
		

		var cd = $interval(function(){
			var cd_num = parseInt(countdown_span.text(), 10);
			if(cd_num > 1)
			{
				cd_num--;
				countdown_span.html(cd_num);
			}
			else
			{
				cd_num = $scope.ajax.delay;
				countdown_span.html(cd_num);
				if(index < number_of_deliveries)
				{
					if(innings_string == 'first')
					{
						obj = $scope.live.response.live.first[index];
						obj2 = $scope.live.response.live.first[(index+1)];

						bowl_tr = jQuery('.first-bowl > .tr[data-id="'+obj.bowler_id+'"]');
						bat_tr = jQuery('.first-bat > .tr[data-id="'+obj.batsman_id+'"]');
						jQuery('.first-bowl > .tr, .first-bat > .tr').removeClass('highlight');
					}
					else if(innings_string == 'second')
					{
						obj = $scope.live.response.live.second[index];
						obj2 = $scope.live.response.live.second[(index+1)];
						bowl_tr = jQuery('.second-bowl > .tr[data-id="'+obj.bowler_id+'"]');
						bat_tr = jQuery('.second-bat > .tr[data-id="'+obj.batsman_id+'"]');
						jQuery('.second-bowl > .tr, .second-bat > .tr').removeClass('highlight');
					}

					if(index == 0)
					{
						jQuery('#feedback').remove();
					}
					
					bat_tr.addClass('highlight');
					bowl_tr.addClass('highlight');
					
					if(! $scope.live[obj.batsman_id])
					{
						$scope.live[obj.batsman_id] = {};
						$scope.live[obj.batsman_id].sixes = 0;
						$scope.live[obj.batsman_id].fours = 0;
						$scope.live[obj.batsman_id].balls_faced = 0;
						$scope.live[obj.batsman_id].runs = 0;
						$scope.live[obj.batsman_id].legal_balls_bowled = 0;
						$scope.live[obj.batsman_id].overs_bowled = "0.0";
						$scope.live[obj.batsman_id].maidens = 0;
						$scope.live[obj.batsman_id].runs_conceded = 0;
						$scope.live[obj.batsman_id].wickets_taken = 0;
						$scope.live[obj.batsman_id].econ = "0.0";							
					}

					if(! $scope.live[obj.bowler_id])
					{
						$scope.live[obj.bowler_id] = {};
						$scope.live[obj.bowler_id].sixes = 0;
						$scope.live[obj.bowler_id].fours = 0;
						$scope.live[obj.bowler_id].balls_faced = 0;
						$scope.live[obj.bowler_id].runs = 0;								
						$scope.live[obj.bowler_id].legal_balls_bowled = 0;
						$scope.live[obj.bowler_id].overs_bowled = "0.0";
						$scope.live[obj.bowler_id].maidens = 0;
						$scope.live[obj.bowler_id].runs_conceded = 0;
						$scope.live[obj.bowler_id].wickets_taken = 0;
						$scope.live[obj.bowler_id].econ = "0.0";
					}

					// check if maiden
					if(index == 0) // first ball of the innings
					{
						last_bowler_id = obj.bowler_id;
					}
					else
					{
						if(innings_string == 'first')
						{
							last_bowler_id = $scope.live.response.live.first[(index - 1)]
						}
						else if(innings_string == 'second')
						{
							last_bowler_id = $scope.live.response.live.second[(index - 1)]
						}					
					}

					if(last_bowler_id == obj.bowler_id)
					{
						runs_in_over += obj.runs;
					}
					else
					{
						// over complete
						if(runs_in_over == 0)
						{
							if(innings_string == 'first')
							{
								$scope.live.response.live.first[(index - 1)].bowler_id.maidens += 1;
							}
							else if(innings_string == 'second')
							{
								$scope.live.response.live.second[(index - 1)].bowler_id.maidens += 1;
							}						
						}
						runs_in_over = obj.runs;
					}
					
					
					var to_add_balls = 0;
					var total_add_runs = 0;
					var batsman_add_runs = 0;
					var wickets_to_add = 0;
					if(obj.type_of_ball == 'NOBALL' || obj.type_of_ball == 'WIDE')
					{								
						total_add_runs = 1;
						$scope.live[obj.bowler_id].runs_conceded += 1;
					}
					else
					{
						legal_balls++;
						to_add_balls = 1;

						$scope.live[obj.bowler_id].legal_balls_bowled++;
						$scope.live[obj.bowler_id].overs_bowled = $scope.bowlerOvers($scope.live[obj.bowler_id].legal_balls_bowled);

						if(obj.outcome == 'W')
						{
							$scope.live[obj.batsman_id].status = obj.status;
							wickets_to_add = 1;

							if(obj.out_how !== 'Run Out')
							{
								$scope.live[obj.bowler_id].wickets_taken += 1;
							}

							if(innings_string == 'first')
							{
								$scope.live.first_innings.fow.push(($scope.live.first_innings.wickets + 1)+"-"+$scope.live.first_innings.total+"  "+obj.batsman_name+" ("+$scope.bowlerOvers(legal_balls)+")");
							}
							else if(innings_string == 'second')
							{
								$scope.live.second_innings.fow.push(($scope.live.second_innings.wickets + 1)+"-"+$scope.live.second_innings.total+"  "+obj.batsman_name+" ("+$scope.bowlerOvers(legal_balls)+")");
							}
							total_add_runs = 0;
							batsman_add_runs = 0;
						}
						else if(obj.outcome == '4')
						{
							$scope.live[obj.batsman_id].fours += 1;
							total_add_runs = 4;
							batsman_add_runs = 4;
							$scope.live[obj.bowler_id].runs_conceded += 4;
						}
						else if(obj.outcome == '6')
						{
							$scope.live[obj.batsman_id].sixes += 1;
							total_add_runs = 6;
							batsman_add_runs = 6;
							$scope.live[obj.bowler_id].runs_conceded += 6;
						}
						else
						{
							total_add_runs = parseInt(obj.runs, 10);
							batsman_add_runs = parseInt(obj.runs, 10);
							$scope.live[obj.bowler_id].runs_conceded += parseInt(obj.runs);
						}

					}
												
					$scope.live[obj.batsman_id].runs += batsman_add_runs;
					$scope.live[obj.batsman_id].balls_faced += to_add_balls;
					$scope.live[obj.batsman_id].runsballs = $scope.live[obj.batsman_id].runs+" ("+$scope.live[obj.batsman_id].balls_faced+")";

					$scope.live[obj.bowler_id].econ = ($scope.live[obj.bowler_id].runs_conceded*6/$scope.live[obj.bowler_id].legal_balls_bowled).toFixed(2);
					
					if(innings_string == 'first')
					{
						$scope.live.first_innings.total += total_add_runs;
						$scope.live.first_innings.wickets += wickets_to_add;
						$scope.live.first_innings.overs = $scope.ballsToOver(legal_balls, innings_string, (index + 1) == number_of_deliveries);
						
						jQuery('.1st').append(obj.commentary);
						jQuery('.1st')[0].scrollTop = jQuery('.1st')[0].scrollHeight;
					}
					else if(innings_string == 'second')
					{
						$scope.live.second_innings.total += total_add_runs;
						$scope.live.second_innings.wickets += wickets_to_add;
						$scope.live.second_innings.overs = $scope.ballsToOver(legal_balls, innings_string, (index + 1) == number_of_deliveries);

						jQuery('.2nd').append(obj.commentary);
						jQuery('.2nd')[0].scrollTop = jQuery('.1st')[0].scrollHeight;
					}
					


					// boundaries
					$scope.live[obj.batsman_id].boundaries = $scope.live[obj.batsman_id].fours+"/"+$scope.live[obj.batsman_id].sixes;

					// calculating SR
					$scope.live[obj.batsman_id].sr = ( ($scope.live[obj.batsman_id].runs*100)/$scope.live[obj.batsman_id].balls_faced ).toFixed(2);
					
				}
				else
				{
					if(innings_string == 'first')
					{
						$scope.live.first_innings_completed = true;
						$scope.live.to_win = $scope.live.response.to_win;
						$scope.live.rrr = $scope.live.response.rrr;
						$scope.live.in_overs = $scope.live.response.in_overs;
						jQuery('#first_countdown').parent().remove();
					}
					else if(innings_string == 'second')
					{
						$scope.live.second_innings_completed = true;
						$scope.live.winning_team_label = $scope.live.response.result.team_label;
						$scope.live.winning_margin = $scope.live.response.result.margin;
						jQuery('#second_countdown').parent().remove();
					}

					$interval.cancel(cd);
				}
				index++;
			}
		}, 1000);


		
	};
	
	$scope.ballsToOver = function(balls, innings_number, innings_over){
		if(innings_number == 'first')
		{
			$scope.live.first_innings.run_rate = (($scope.live.first_innings.total*6)/balls).toFixed(2)+" rpo";
		}
		else if(innings_number == 'second')
		{
			$scope.live.second_innings.run_rate = (($scope.live.second_innings.total*6)/balls).toFixed(2)+" rpo";
		}
		

		if(balls % 6 == 0)
		{
			return (Math.floor(balls/6))+'.0';				
		}
		else
		{
			return Math.floor(balls/6)+'.'+Math.floor(balls % 6);
		}

		
	};

	$scope.bowlerOvers = function(balls){		
		if(balls % 6 == 0)
		{			
			return (Math.floor(balls/6))+'.0';			
		}
		else
		{
			return Math.floor(balls/6)+'.'+Math.floor(balls % 6);
		}
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
		var tbody_bowl = jQuery(selector);
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

		for(var i = 1; i <= tbody_bowl.length; i++)
		{			
			var tr = jQuery(tbody_bowl[(i-1)]);
			
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

		str += new Array((cell_width*5)+10).join("-")+"\r\n";
		str += "FOW: ";
		var fow = tbody_bowl.closest('.table-mockup').siblings('.fow').find('span');		
		jQuery.each(fow, function(i, span){
			str += jQuery(span).text();
			str += new Array(8).join(" ");
		});

		str += "\r\n\r\n\r\n\r\n";
		return str;
	};

	$scope.copyScorecard = function(e){
		var button = jQuery(e.currentTarget);
		var str = $scope.batting('.first-bat > .tr');
		str += $scope.bowling('.first-bowl > .tr');
		str += $scope.batting('.second-bat > .tr');
		str += $scope.bowling('.second-bowl > .tr');

		str += jQuery('.comm_result').text();

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
}).filter('unsafe', function($sce) { return $sce.trustAsHtml; });;