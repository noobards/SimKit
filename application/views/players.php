	<div class="row site-breadcrumbs">
		<div class="col-xs-12">
			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Players
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
						<p>Summary of records: </p>
						<ul>
							<li><strong>Total records: </strong><?php echo $flash['total'] ; ?></li>
							<li><strong>Successfull inserts: </strong><?php echo $flash['inserted'] ; ?></li>
							<li><strong>Failed inserts: </strong><?php echo $flash['failed'] ; ?></li>
						</ul>										
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
	?>

	<div class="row">
		<div class="col-md-6" ng-controller="addNewPlayer">
			<div class="modal fade" tabindex="-1" id="playerDataModal">
			  <div class="modal-dialog modal-lg">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Confirm your player</h4>
			      </div>
			      <div class="modal-body">
			        
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Edit</button>
			        <button type="button" ng-click="addNewPlayer($event)" class="btn btn-primary">Save</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Add New Player(s)</div>
					<div class="box-helping-text">Define the player.</div>
				</div>
				<div class="box-body box-body-max">
					<div ng-hide="data.selection_made">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group text-center">
									Choose how you would like to add player(s) to the database
								</div>	
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group text-center">
									<button type="button" ng-click="showForm('manual')" class="btn btn-danger">Manual Entry</button>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group text-center">
									<button type="button" ng-click="showForm('upload')" class="btn btn-danger">File Upload</button>
								</div>
							</div>
						</div>
					</div>

					<form ng-show="data.manual_entry" class="form-horizontal" name="add_new_player_form" onsubmit="return false;" autocomplete="off" novalidate>
						<h3 class="form-subtitle">Demographics</h3>											
						<div class="form-group">
							<label for="fn" class="control-label col-md-6">First Name: </label>
							<div class="col-md-6">
								<input type="text" id="fn" ng-model="data.fn" ng-required="true" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="ln" class="control-label col-md-6">Last Name: </label>
							<div class="col-md-6">
								<input type="text" id="ln" ng-model="data.ln" ng-required="true" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="nick" class="control-label col-md-6">Nickname: </label>
							<div class="col-md-6">
								<input type="text" id="nick" ng-model="data.nick" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="age" class="control-label col-md-6">Age: </label>
							<div class="col-md-6">
								<input type="text" id="age" ng-model="data.age" ng-required="true" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="gen" class="control-label col-md-6">Gender: </label>
							<div class="col-md-6">
								<select id="gen" ng-model="data.gender" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="country" class="control-label col-md-6">Country: </label>
							<div class="col-md-6">
								<select id="country" ng-model="data.country" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option ng-repeat="country in countries" value="{{country.id}}">{{country.name}}</option>
								</select>
							</div>
						</div>

						<h3 class="form-subtitle">Attributes</h3>
						<div class="form-group">
							<label for="player_type" class="control-label col-md-6">Player Type: </label>
							<div class="col-md-6">
								<select id="player_type" ng-model="data.player_type" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option ng-repeat="player_type in player_types" value="{{player_type.id}}">{{player_type.name}}</option>
								</select>
							</div>
						</div>
						<div class="form-group" ng-if="hasBowlingAbility()">
							<label for="bowler_type" class="control-label col-md-6">Bowler Type: </label>
							<div class="col-md-6">
								<select id="bowler_type" ng-model="data.bowler_type" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option ng-repeat="bowler_type in bowler_types" value="{{bowler_type.id}}">{{bowler_type.name}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-6">Batting Hand: </label>
							<div class="col-md-6">
								<label class="radio-inline"><input type="radio" name="bat_hand" ng-model="data.bat_hand" ng-required="true" value="Right">Right</label>
								<label class="radio-inline"><input type="radio" name="bat_hand" ng-model="data.bat_hand" ng-required="true" value="Left">Left</label>
							</div>
						</div>
						<div class="form-group" ng-if="hasBowlingAbility()">
							<label class="control-label col-md-6">Bowling Hand: </label>
							<div class="col-md-6">
								<label class="radio-inline"><input type="radio" name="bowl_hand" ng-model="data.bowl_hand" ng-required="true" value="Right">Right</label>
								<label class="radio-inline"><input type="radio" name="bowl_hand" ng-model="data.bowl_hand" ng-required="true" value="Left">Left</label>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-6">Modes: </label>
							<div class="col-md-6">
								<label class="checkbox-inline"><input type="checkbox" ng-model="data.speciality.test" ng-required="!atLeastOneSelected(data.speciality)" value="Test">Test</label>
								<label class="checkbox-inline"><input type="checkbox" ng-model="data.speciality.odi" ng-required="!atLeastOneSelected(data.speciality)" value="ODI">ODI</label>
								<label class="checkbox-inline"><input type="checkbox" ng-model="data.speciality.t20" ng-required="!atLeastOneSelected(data.speciality)" value="T20">T20</label>
							</div>
						</div>										
						<div class="form-group text-center">												
							<button class="btn btn-primary" ng-disabled="add_new_player_form.$invalid" ng-click="showPlayerModal()">Add Player</button>&nbsp;<button type="button" ng-click="showSelectionOptions()" class="btn btn-danger">Cancel</button>
						</div>
					</form>
					<form ng-show="data.file_upload" action="Players/FileUpload" method="post" enctype="multipart/form-data" class="form-horizontal" name="file_upload_form">
						<div class="form-group">							
							<div class="col-md-12">
								<div style="width: 400px; margin: 10px auto;">
									<label for="file_input">Choose/Browse File</label>&nbsp;&nbsp;&nbsp;<input id="file_input" name="file_input" type="file" style="display: inline;" />
								</div>
							</div>
						</div>
						<div class="form-group text-center">												
							<button class="btn btn-primary">Upload</button>&nbsp;<button type="button" ng-click="showSelectionOptions()" class="btn btn-danger">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-6" ng-controller="recentlyAddedPlayers">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Player List</div>
					<div class="box-helping-text">Your most recent ones.</div>
				</div>
				<div class="box-body">					
					<table class="table table-bordered table-striped">
						<thead>
							<tr class="alert-success">
								<th>S.No</th>
								<th>Player Name</th>
								<th>Gender</th>
								<th>Country</th>
								<th>Player Type</th>
								<th>Added On</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-if="players.length > 0" ng-repeat="player in players">
								<td>{{$index + 1}}</td>
								<td>{{player.name}}</td>
								<td>{{player.gender}}</td>
								<td>{{player.country}}</td>
								<td>{{player.type}}</td>
								<td>{{player.created}}</td>
							</tr>
							<tr ng-if="players.length == 0">
								<td colspan="6">No records found</td>
							</tr>
						</tbody>
					</table>					
					<div class="text-right"><a href="<?php echo site_url(); ?>/Players/ListPlayers"><i class="fa fa-search">&nbsp;</i>View All</a></div>
				</div>
			</div>	
		</div>
	</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/players.js"></script>