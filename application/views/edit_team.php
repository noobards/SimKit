
	<div class="row site-breadcrumbs">
		<div class="col-xs-12">
			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/teams">Teams</a> <i class="fa fa-chevron-right">&nbsp;</i> Edit Team
		</div>
	</div>

	<?php
	if($this->session->flashdata('flash'))
	{
		$flash = $this->session->flashdata('flash');
		if($flash['status'] == 'OK')
		{	?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-success" style="margin-bottom: 0;">
						<?php echo $flash['msg'] ; ?>
					</div>
				</div>
			</div>
	<?php
		}
		else
		{	?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger" style="margin-bottom: 0;"><i class="fa fa-exclamation-triangle">&nbsp;</i><?php echo $flash['msg']; ?></div>
				</div>
			</div>
	<?php		
		}		
	}	

	if($is_owner == "YES")
	{	
		if($team)
		{	?>
			<div class="row">
				<div class="col-md-6" ng-controller="updateTeam">
					<div class="box">
						<div class="box-title">
							<div class="box-main-text">{{data.tn}}</div>
							<div class="box-helping-text">Edit your team's data.</div>
						</div>
						<div class="box-body">	
							<form class="form-horizontal" name="update_team_form" onsubmit="return false;" autocomplete="off" novalidate>						
								<div class="form-group">
									<label for="tn" class="control-label col-md-6">Team Name: </label>
									<div class="col-md-6">
										<input type="text" id="tn" ng-model="data.tn" ng-required="true" class="form-control" />
									</div>
								</div>						
								<div class="form-group">
									<label for="tt" class="control-label col-md-6">Team Type: </label>
									<div class="col-md-6">									
										<select id="tt" ng-model="data.tt" ng-required="true" class="form-control">
											<option value="">Choose One</option>
											<?php foreach($options as $type_id => $type_value)
											{
											?>
												<option value="<?php echo $type_id; ?>"><?php echo $type_value; ?></option>
											<?php
											}
											?>											
										</select>
									</div>
								</div>
								<div class="form-group text-center">												
									<button class="btn btn-primary" ng-disabled="update_team_form.$invalid" ng-click="editTeam($event)">Update</button>	
								</div>											
							</form>
						</div>
					</div>
				</div>
				<div class="col-md-6" ng-controller="squadPlayers">
					<div class="box">
						<div class="box-title">
							<div class="box-main-text">Squad Players</div>
							<div class="box-helping-text">Manage your players.</div>
						</div>
						<div class="box-body">
							<div style="margin-bottom: 5px;">
								<button ng-disabled="! data.atLeastOneChecked" ng-click="removePlayer($event)" class="btn btn-danger">Remove</button>
								<span ng-show="data.atLeastOneChecked" class="red">{{data.totalSelected}} selected</span>
							</div>
							<div class="vscroll" style="height: 82%;">
								<table class="table table-striped">
									<thead>
										<tr class="alert-warning">
											<th><input type="checkbox" class="allCB" ng-click="allSquadPlayers($event)" /></th>
											<th>Name</th>
											<th>Gender</th>
											<th>Country</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="player in data.players">
											<td><input type="checkbox" class="playerCB" ng-checked="data.checkThis" ng-click="allCheck($event)" ng-model="data.selectedPlayers[player.id]" /></td>
											<td>{{player.name}} ({{player.id}})</td>
											<td>{{player.gender}}</td>
											<td>{{player.country}}</td>
										</tr>
										
										<tr ng-show="data.players.length == 0">
											<td colspan="3">No players in squad. Click <a class="normal-anchor" href="<?php echo site_url(); ?>/Teams">here</a> to add them.</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>		
			</div>
			<input id="tid"	type="hidden" value="<?php echo $team->team_id; ?>">
	<?php
		} // end of $team object
		else
		{	?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger" style="margin-bottom: 0;"><i class="fa fa-exclamation-triangle">&nbsp;</i>The specified team does not exist.</div>
				</div>
			</div>
	<?php		
		}
	?>
		
	<?php
	} // end of is owner == yes
	else
	{	?>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-danger" style="margin-bottom: 0;"><i class="fa fa-exclamation-triangle">&nbsp;</i>You do not have permission to edit this team. Please contact the administrator/developer of the site.</div>
			</div>
		</div>
	<?php
	}
	?>



<script type="text/javascript">
	var app = angular.module("SimKit", []);
	app.controller("updateTeam", function($scope, $http, $location){
		$scope.data = {};
		$scope.data.tn = '<?php echo $team->team_name; ?>';
		$scope.data.tt = '<?php echo $team->team_type_id; ?>';
		$scope.data.tid = document.getElementById('tid').value;
		

		$scope.editTeam = function(e){
			var button = $(e.target);
			button.attr('disabled', 'disabled').html("Processing...");
			$http({
			  method: 'POST',
			  data:$scope.data,
			  url: simkit.baseUrl+'Teams/UpdateTeam'
			}).then(function successCallback(response) {
				if(response.statusText == "OK")
				{			
					if(response.data.status == "OK")
					{
						button.html("Redirecting...");
						window.location.href = $location.absUrl();
					}
					else
					{
						button.removeAttr('disabled').html("Update");
						alert("Ajax Error: "+response.data.msg);
					}
				}
				else
				{
					button.removeAttr('disabled').html("Update");
					alert("Ajax Error: "+response.statusText);
				}
			}, function errorCallback(response) {
				button.removeAttr('disabled').html("Update");
			    alert("Ajax Error: "+response.statusText);
			});
		};
	});

	app.controller("squadPlayers", function($scope, $http, $location){
		$scope.data = {};
		$scope.data.checkThis = false;
		$scope.data.tid = document.getElementById('tid').value;
		$http({
		  method: 'POST',
		  data:$scope.data,
		  url: simkit.baseUrl+'Teams/getTeamPlayers'
		}).then(function successCallback(response) {
			if(response.statusText == "OK")
			{			
				if(response.data.status == "OK")
				{
					$scope.data.players = response.data.players;
				}
				else
				{					
					alert("Ajax Error: "+response.data.msg);
				}
			}
			else
			{				
				alert("Ajax Error: "+response.statusText);
			}
		}, function errorCallback(response) {			
		    alert("Ajax Error: "+response.statusText);
		});

		$scope.allSquadPlayers = function(e){
			var element = $(e.target);
			if(element.is(':checked'))
			{
				$scope.data.checkThis = true;
				$scope.data.atLeastOneChecked = true;
				$scope.data.totalSelected = $scope.data.players.length;
			}
			else
			{
				$scope.data.checkThis = false;
				$scope.data.atLeastOneChecked = false;
				$scope.data.totalSelected = 0;
			}									 
		};

		$scope.allCheck = function(e){
			var element = $(e.target);
			var total_players = $scope.data.players.length;
			var checked_players = $('.playerCB:checked').length;
			$scope.data.totalSelected = checked_players;
			$scope.data.atLeastOneChecked = (checked_players > 0);
			$('.allCB').prop('checked', (total_players == checked_players) );			
		};

		$scope.removePlayer = function(e){
			var button = $(e.target);
			button.attr('disabled', 'disabled').html("Processing...");
			$http({
			  method: 'POST',
			  data:$scope.data,
			  url: simkit.baseUrl+'Teams/removeFromSquad'
			}).then(function successCallback(response) {
				if(response.statusText == "OK")
				{			
					if(response.data.status == "OK")
					{
						window.location.href = $location.absUrl();
					}
					else
					{
						button.removeAttr('disabled').html("Remove");
						alert(response.data.msg);
					}
				}
				else
				{
					button.removeAttr('disabled').html("Remove");
					alert("Ajax Error: "+response.statusText);
				}
			}, function errorCallback(response) {
				button.removeAttr('disabled').html("Remove");
			    alert("Ajax Error: "+response.statusText);
			});
		};
	});
</script>