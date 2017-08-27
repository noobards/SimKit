
	<div class="row site-breadcrumbs">
		<div class="col-xs-12">
			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Teams
		</div>
	</div>

	<?php
	if($this->session->flashdata('flash'))
	{
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-info" style="margin-bottom: 0;"><i class="fa fa-check-circle">&nbsp;</i><?php echo $this->session->flashdata('flash'); ?></div>
			</div>
		</div>
	<?php
	}
	?>

	<div class="row">
		<div class="col-md-6" ng-controller="addNewTeam">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Add New Team</div>
					<div class="box-helping-text">Choose a swacky name for your team.</div>
				</div>
				<div class="box-body">	
					<form class="form-horizontal" name="add_new_team_form" onsubmit="return false;" autocomplete="off" novalidate>						
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
									<option ng-repeat="type in team_types" value="{{type.id}}">{{type.name}}</option>
								</select>
							</div>
						</div>
						<div class="form-group text-center">												
							<button class="btn btn-primary" ng-disabled="add_new_team_form.$invalid" ng-click="addTeam($event)">Save</button>	
						</div>											
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-6" ng-controller="myTeams">
			<div class="modal fade" tabindex="-1" id="listPlayers">
			  <div class="modal-dialog modal-lg">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Choose your players for {{selected_team}}</h4>
			      </div>
			      <div class="modal-body">
			      	<div class="vscroll h250">			      		
	      				<table>
	      					<tbody>
	      						<tr>
	      							<td style="padding:10px;"><label for="search_filter_name" class="control-label">Filter By Name:</label></td>
	      							<td style="padding:10px;"><input type="text" id="search_filter_name" ng-model="search_filter.name" class="form-control" /></td>
	      							<td style="padding:10px;"><label for="search_filter_country" class="control-label">Filter By Country:</label></td>
	      							<td style="padding:10px;"><input type="text" id="search_filter_country" ng-model="search_filter.country" class="form-control" /></td>
	      						</tr>
	      					</tbody>
	      				</table>
	      				<table class="table table-striped table-bordered">
				        	<thead>
				        		<tr class="alert-warning">
				        			<th class="text-center"><input type="checkbox" ng-click="toCheckAll($event)" /></th>
				        			<th>Player Name</th>
				        			<th>Gender</th>
				        			<th>Type</th>
				        			<th>Country</th>			        			
				        		</tr>
				        	</thead>
				        	<tbody>
				        		<tr ng-repeat="player in my_players | filter: search_filter">
				        			<td class="text-center"><input type="checkbox" class="toCheckAll" ng-click="allCheck($event)" ng-model="players.selected[player.id]" /></td>
				        			<td>{{player.name}}</td>
				        			<td>{{player.gender}}</td>
				        			<td class="text-center"><img class="player_type_icon" src="<?php echo base_url(); ?>assets/images/icons/{{player.icon}}" title="{{player.type}}" alt="{{player.type}}" /></td>
				        			<td>{{player.country}}</td>
				        		</tr>
				        		<tr ng-if="(my_players | filter: search_filter).length == 0"><td colspan="5">No matches found.</td></tr>
				        		<tr ng-if="my_players.length == 0"><td colspan="5">No records found.</td></tr>
				        	</tbody>
				        </table>			      			
			        </div>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="button" ng-click="addToTeam($event)" class="btn btn-danger">Add to Team</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Team List</div>
					<div class="box-helping-text">Teams added by you.</div>
				</div>
				<div class="box-body vscroll">
					<table class="table table-striped">
						<thead>
							<tr class="alert-warning">
								
								<th>Team Name</th>
								<th>Team Type</th>
								<th># Players</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-if="my_teams.length > 0" ng-repeat="team in my_teams">
								
								<td>{{team.name}}</td>
								<td>{{team.type}}</td>
								<td>{{team.nop}}</td>
								<td>
									<div class="btn-group">
									  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
									    Actions <span class="caret"></span>
									  </button>
									  <ul class="dropdown-menu">
									    <li><a ng-click="addPlayersToTeamModal($event,team.team_id, team.name)" href="#">Add Players</a></li>
									    <li><a href="Teams/Edit/{{team.team_id}}">Edit Team</a></li>
										
										<li><a ng-click="removeTeam($event,team.team_id, team.name)" href="#">Delete Team</a></li>
									  </ul>
									</div>
								</td>
							</tr>
							<tr ng-if="my_teams.length == 0"><td colspan="3">No records found.</td></tr>
						</tbody>
					</table>
				</div>
			</div>	
		</div>
	</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/teams.js"></script>