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
							<div class="row">
								
								<div class="col-md-9">
									<form class="form-horizontal" name="update_team_form" onsubmit="return false;" autocomplete="off" novalidate>						
										<div class="form-group">
											<label for="tn" class="control-label col-md-5">Team Name: </label>
											<div class="col-md-7">
												<input type="text" id="tn" ng-model="data.tn" ng-required="true" class="form-control" />
											</div>
										</div>						

										<div class="form-group">
											<label for="tt" class="control-label col-md-5">Team Type: </label>
											<div class="col-md-7">									
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

										<div class="form-group">
											<div class="col-md-7 col-md-offset-5">
												<label><input type="checkbox" ng-model="data.logo" ng-click="logoChange()" style="position: absolute; top: 0px; left: 5px;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I want to change my current logo.</label>
											</div>
										</div>

										<div class="form-group" ng-show="data.upload">
											<label class="control-label col-md-5">Logo File: </label>
											<div class="col-md-7">
												<input type="file" file-model="myFile" class="hide" /><div ng-click="fireBrowse($event)" class="file_button">Choose file</div>
												<div><small>For best results, make sure the image is of 200x200 (px) dimension.</small><br /><small>Only png/jpg/jpeg/gif file allowed.</small><br /><small class="red">File size must be less than or equal to 200KB.</small></div>
											</div>
										</div>

										<div class="form-group">												
											<div class="col-md-7 col-md-offset-5">
												<button class="btn btn-primary" ng-disabled="update_team_form.$invalid" ng-click="editTeam($event)">Update</button>	
											</div>
										</div>											
									</form>
								</div>
								<div class="col-md-3 text-center">
									<?php
										if(trim($team->logo) != '')
										{
											$src = base_url().'assets/images/uploads/user_'.$this->session->logged_user.'/teams/'.$team->logo;
											$path = FCPATH.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'user_'.$this->session->logged_user.DIRECTORY_SEPARATOR.'teams'.DIRECTORY_SEPARATOR.$team->logo;
											if(! file_exists($path)) // value present in DB but image not present in file system
											{
												$src = base_url().'assets/images/no_team_logo.png';
											}
										}
										else
										{
											$src = base_url().'assets/images/no_team_logo.png';
										}
									?>
									<img src="<?php echo $src; ?>" width="120" height="120" alt="<?php echo $team->team_name ;?>" />
								</div>
							</div>							
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
	simkit.app.controller("updateTeam", function($scope, $http, $location, $window){

		$scope.data = {};

		$scope.data.tn = '<?php echo $team->team_name; ?>';

		$scope.data.tt = '<?php echo $team->team_type_id; ?>';

		$scope.data.tid = document.getElementById('tid').value;

		
		$scope.logoChange = function(){
			jQuery('.file_button').html('Choose file');
			$scope.myFile = null;
			if($scope.data.logo)
			{
				$scope.data.upload = true;
			}
			else
			{
				$scope.data.upload = false;
			}
		};

		$scope.fireBrowse = function(e){
			var button = jQuery(e.target);
			var file = button.siblings('input:file');
			file.trigger('click');
		};

		$scope.fileSelected = function(file, fake_button){
			if(file.size > 204800)
			{
				alert("File size must be less than 200KB. The current filesize is "+(Math.floor(file.size/1024))+'KB');
				fake_button.html('Choose file');
			}
			else
			{
				if($scope.validImage(file.type))
				{
					fake_button.html(file.name);
				}
				else
				{
					alert("Uploaded file is not in supported format. The current format is "+file.type);
				}			
			}		
		};

		$scope.validImage = function(name){
			if(! name){
				return false;
			}

			var split = name.split("/");
			if(split.length < 2){
				return false;
			} else if(split.length > 2){
				return false;
			} else {
				var ext = split[1];
				if(ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif'){
					return true;
				} else {
					return false;
				}
			}
		};


		$scope.editTeam = function(e){

			if( window.FormData !== undefined )
			{
				var fd = new FormData();						
				fd.append('file', $scope.myFile);
				fd.append('form', JSON.stringify($scope.data));

				var button = $(e.target);
				button.attr('disabled', 'disabled').html("Processing...");
				$http({
				  method: 'POST',
				  data:fd,
				  url: simkit.baseUrl+'Teams/UpdateTeam',
				  transformRequest: angular.identity,
              	  headers: {'Content-Type': undefined, 'Process-Data': false}
				}).then(function successCallback(response) {
					if(response.statusText == "OK")
					{			
						if(response.data.status == "OK")
						{
							button.html("Redirecting...");
							$window.location.href = $location.absUrl();
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
			}
			else
			{
				alert("Please use a browser that supports HTML5 (Firefox/Chrom/Safari/IE10+");
			}
		};

	}).directive('fileModel', function ($parse) {
    return {
       restrict: 'A',
       link: function(scope, element, attrs) {
          var model = $parse(attrs.fileModel);
          var modelSetter = model.assign;
          
          element.bind('change', function(){
             scope.$apply(function(){
                modelSetter(scope, element[0].files[0]);                
                scope.fileSelected(element[0].files[0], jQuery(element[0]).siblings('.file_button'));
             });
          });
       }
    };
 });



	simkit.app.controller("squadPlayers", function($scope, $http, $location){

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