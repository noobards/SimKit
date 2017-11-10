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

		$scope.fakeTextArea(str);
	};

	$scope.fakeTextArea = function(str){
		var textArea = document.createElement("textarea");
		//
		// *** This styling is an extra step which is likely not required. ***
		//
		// Why is it here? To ensure:
		// 1. the element is able to have focus and selection.
		// 2. if element was to flash render it has minimal visual impact.
		// 3. less flakyness with selection and copying which **might** occur if
		//    the textarea element is not visible.
		//
		// The likelihood is the element won't even render, not even a flash,
		// so some of these are just precautions. However in IE the element
		// is visible whilst the popup box asking the user for permission for
		// the web page to copy to the clipboard.
		//

		// Place in top-left corner of screen regardless of scroll position.
		textArea.style.position = 'fixed';
		textArea.style.top = 0;
		textArea.style.left = 0;

		// Ensure it has a small width and height. Setting to 1px / 1em
		// doesn't work as this gives a negative w/h on some browsers.
		textArea.style.width = '2em';
		textArea.style.height = '2em';

		// We don't need padding, reducing the size if it does flash render.
		textArea.style.padding = 0;

		// Clean up any borders.
		textArea.style.border = 'none';
		textArea.style.outline = 'none';
		textArea.style.boxShadow = 'none';

		// Avoid flash of white box if rendered for any reason.
		textArea.style.background = 'transparent';


		textArea.value = str;

		document.body.appendChild(textArea);

		textArea.select();

		try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			alert('Copying text command was ' + msg);
		} catch (err) {
			alert('This feature is not supported in your browser. Please copy the text manually');
		}

		document.body.removeChild(textArea);
	};
});