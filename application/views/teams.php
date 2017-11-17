
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

				<div class="box-body box-body-max">	
					<div class="bot10">
						<small>Fields marked with <span class="red">*</span> are mandatory and cannot be left blank.</small>
					</div>
					<form class="form-horizontal" name="add_new_team_form" onsubmit="return false;" autocomplete="off" novalidate>						

						<div class="form-group">

							<label for="tn" class="control-label col-md-6">Team Name: <span class="red">*</span></label>

							<div class="col-md-6">

								<input type="text" id="tn" ng-model="data.tn" ng-required="true" class="form-control" />

							</div>

						</div>						

						<div class="form-group">

							<label for="tt" class="control-label col-md-6">Team Type: <span class="red">*</span></label>

							<div class="col-md-6">

								<select id="tt" ng-model="data.tt" ng-required="true" class="form-control">

									<option value="">Choose One</option>

									<option ng-repeat="type in team_types" value="{{type.id}}">{{type.name}}</option>

								</select>

							</div>

						</div>

						<div class="form-group">
							<label class="control-label col-md-6">Logo File: </label>
							<div class="col-md-6">
								<input type="file" file-model="myFile" class="hide" /><div ng-click="fireBrowse($event)" class="file_button">Choose file</div>
								<div><small>For best results, make sure the image is of 200x200 (px) dimension.</small><br /><small>Only png/jpg/jpeg/gif file allowed.</small><br /><small class="red">File size must be less than or equal to 200KB.</small></div>
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
	      							<td style="padding:10px 5px; width: 16.66%"><label for="search_filter_name" class="control-label">By Name:</label></td>
	      							<td style="padding:10px 5px; width: 16.66%""><input type="text" id="search_filter_name" ng-model="search_filter.name" class="form-control" /></td>
	      							<td style="padding:10px 5px; width: 16.66%""><label for="search_filter_country" class="control-label">By Country:</label></td>
	      							<td style="padding:10px 5px; width: 16.66%""><input type="text" id="search_filter_country" ng-model="search_filter.country" class="form-control" /></td>
	      							<td style="padding:10px 5px; width: 16.66%""><label for="search_filter_rating" class="control-label">By Rating:</label></td>
	      							<td style="padding:10px 5px; width: 16.66%""><input type="text" id="search_filter_rating" ng-model="search_filter.avg" class="form-control" /></td>
	      						</tr>
	      					</tbody>
	      				</table>

	      				<div class="table-mockup">
	      					<div class="thead">
	      						<div class="tr">
	      							<div class="th"><input type="checkbox" ng-click="toCheckAll($event)" /></div>
	      							<div class="th">Name</div>	      								      							
	      							<div class="th">Rating</div>
	      							<div class="th">Country</div>
	      						</div>
	      					</div>
	      					<div class="tbody">
	      						<div class="tr" ng-repeat="player in my_players | filter: search_filter">
	      							<div class="td text-center"><input type="checkbox" class="toCheckAll" ng-click="allCheck($event)" ng-model="players.selected[player.id]" /></div>
	      							<div class="td"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{player.icon}}" title="{{player.type}}" alt="{{player.type}}" />{{player.name}}</div>	      								      							
	      							<div class="td text-center">
	      								<div class='outer_bar'><div style="width:{{(player.avg*10)}}%" class='inner_bar'></div><span class='bar_value'>{{player.avg}}</span></div>
	      							</div>
	      							<div class="td text-center">{{player.country}}</div>	      							
	      						</div>
	      						<div class="tr" ng-if="(my_players | filter: search_filter).length == 0"><div class="td">No matches found.</div></div>
				        		<div class="tr" ng-if="my_players.length == 0"><div class="td">No records found.</div></div>
	      					</div>
	      				</div>
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