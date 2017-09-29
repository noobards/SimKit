simkit.app.controller('communityPlayers', function($scope, $http, $window){
	$scope.loading("list");
	$scope.page_load_message = "Click on a name on the left panel to see details of the player.";
	$scope.no_player_data = true;
	$scope.data = {};
	$scope.data.players = [];
	$scope.data.cache = {};
	$scope.data.cart = [];
	$scope.usernames = {};
	$http({
		method:'get',
		url:simkit.baseUrl+'Community/fetchPlayers'
	}).then(function success(response){
		if(response.statusText == 'OK')
		{			
			$scope.data.players = response.data.players;
		}
		else
		{
			alert(response.statusText);
		}		
	}, function error(response){
		alert(response.statusText);
	}).then(function complete(){
		$scope.finish('list');
	});

	$scope.showDetail = function(e, pid)
	{
		e.preventDefault();		
		if($scope.data.cache[pid]){
			$scope.no_player_data = false;
			$scope.data.player = $scope.data.cache[pid];
		} else {
			// make ajax
			$scope.loading('player_placeholder');
			$http({
				url: simkit.baseUrl+'Community/getPlayer',
				method: 'post',
				data:{pid:pid}
			}).then(function success(resp){
				if(resp.statusText == 'OK')
				{
					console.log(resp);
					if(resp.data.status == 'OK')
					{
						$scope.no_player_data = false;
						$scope.data.player = resp.data.player;
						$scope.data.cache[$scope.data.player.pid] = $scope.data.player;
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
				$scope.finish('player_placeholder');
			});
		}
	};

	$scope.addToQueue = function(e){
		var button = jQuery(e.currentTarget);		
		$scope.data.cart.push({'pid':button.attr('data-pid'), 'name':button.attr('data-name'), 'author':button.attr('data-author')});
		button.closest('.tr').addClass('player-added');
		button.siblings('a').removeClass('hide');
		button.attr('disabled', 'disabled').html('<i class="fa fa-check">&nbsp;</i>Added');
	};

	$scope.removeFromCart = function(e){	
		e.preventDefault();	
		var anchor = jQuery(e.currentTarget);
		var pid = anchor.attr('data-pid');
		angular.forEach($scope.data.cart, function(obj, i){
			if(obj.pid == pid)
			{
				$scope.data.cart.splice(i, 1);
				anchor.closest('.tr').removeClass('player-added');
				anchor.addClass('hide');
				anchor.siblings('button').removeAttr('disabled').html('<i class="fa fa-plus">&nbsp;</i>Queue');
			}
		});		

	};

	$scope.confirmDownloadPlayers = function(e){
		var button = jQuery(e.currentTarget);

	};

	$scope.downloadPlayers = function(e){
		var button = jQuery(e.currentTarget);
		var button_text = button.html();
		button.siblings('button').addClass('hide');

		button.attr('disabled', 'disabled').html('Processing...');

		$http({
			url:simkit.baseUrl+'Community/downloadPlayers',
			method:'post',
			data:$scope.data.cart
		}).then(function success(resp){
			if(resp.statusText == 'OK'){
				if(resp.data.status == 'OK')
				{
					$window.location.href = 'Community';
				}
				else
				{
					alert(resp.data.msg);
				}
			} else {
				alert(resp.statusText);
			}
		}, function error(resp){
			alert(resp.statusText);
		}).then(function complete(){
			button.html(button_text).removeAttr('disabled').siblings('button').removeClass('hide');
		});
	};

	$scope.showDownloadList = function(e, pid){
		e.preventDefault();
		if($scope.usernames[pid])
		{
			$scope.data.dl_list = $scope.usernames[pid];
			$scope.showDownloadersList();
		}
		else
		{
			$scope.loading("list");
			$http({
				method:'post',
				url:simkit.baseUrl+'Community/getDownloadList',
				data:{player_id:pid}
			}).then(function success(resp){
				if(resp.statusText == 'OK')
				{
					if(resp.data.status == 'OK')
					{
						$scope.usernames[pid] = resp.data.list;
						$scope.data.dl_list = $scope.usernames[pid];
						$scope.showDownloadersList();
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
			}).then(function final(){
				$scope.finish('list');
			});
		}
	};

	$scope.showDownloadersList = function()	{
		jQuery('#dl_list').modal('show');
	};
});