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
					<div class="alert alert-info" style="margin-bottom: 0;">
						<?= $flash['msg'];	?>
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
		<div class="col-md-5" ng-controller="addNewPlayer">
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
					<div class="box-main-text">Add New Player</div>
					<div class="box-helping-text">Define the player.</div>
				</div>
				<div class="box-body box-body-max">
					<form class="form-horizontal" name="add_new_player_form" onsubmit="return false;" autocomplete="off" novalidate>
					<div class="bot10">
						<small>Fields marked with <span class="red">*</span> are mandatory and cannot be left blank.</small>
					</div>
						<h3 class="form-subtitle">Demographics</h3>											
						<div class="form-group">
							<label for="fn" class="control-label col-md-6">First Name: <span class="red">*</span></label>
							<div class="col-md-6">
								<input type="text" id="fn" ng-model="data.fn" ng-required="true" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="ln" class="control-label col-md-6">Last Name: <span class="red">*</span></label>
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
								<input type="text" id="age" ng-model="data.age" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="gen" class="control-label col-md-6">Gender: <span class="red">*</span></label>
							<div class="col-md-6">
								<select id="gen" ng-model="data.gender" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="country" class="control-label col-md-6">Country: <span class="red">*</span></label>
							<div class="col-md-6">
								<select id="country" ng-model="data.country" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option ng-repeat="country in countries" value="{{country.id}}">{{country.name}}</option>
								</select>
							</div>
						</div>



						<h3 class="form-subtitle">Attributes</h3>
						<div class="form-group">
							<label for="player_type" class="control-label col-md-6">Player Role: <span class="red">*</span></label>
							<div class="col-md-6">
								<select id="player_type" ng-model="data.player_type" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option ng-repeat="player_type in player_types" value="{{player_type.id}}">{{player_type.name}}</option>
								</select>
								<span class="extra-info">Player role plays an important role in determining the outcome of a delivery (run/wicket/dot). For example: a pure batsman against a pure bowler will perform better than a batting/bowling allrounder against the same bowler if the rating point of the batsman is greater than that of the bowler.</span>
							</div>
						</div>

						<div class="form-group">
							<label for="player_mentality" class="control-label col-md-6">Mentality: <span class="red">*</span></label>
							<div class="col-md-6">
								<select id="player_mentality" ng-model="data.mentality" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option value="1">Aggressive</option>
									<option value="2">Moderate</option>
									<option value="3">Defensive</option>
								</select>
								<span class="extra-info" ng-show="data.mentality == '1'">If there is a scoring opportunity, this player will try to take advantage of it but might also end up 
								losing his wicket in the process. High risk, high reward. The end result will range between 0 to 6 runs.</span>
								<span class="extra-info" ng-show="data.mentality == '2'">This player will lay emphasis on preserving his wicket when presented with a scoring opportunity. He will mostly deal in singles and twos with the occasional boundary. The end result will range between 0 and 6 runs.</span>
								<span class="extra-info" ng-show="data.mentality == '3'">The player is out there to make sure he is not tempted by loose deliveries. He will try to score off them but will play with utmost safety. The end result will range between 0 and 4 runs.</span>
							</div>
						</div>

						<div class="form-group" ng-if="hasBowlingAbility()">
							<label for="bowler_type" class="control-label col-md-6">Bowler Type: <span class="red">*</span></label>
							<div class="col-md-6">
								<select id="bowler_type" ng-model="data.bowler_type" ng-required="true" class="form-control">
									<option value="">Choose One</option>
									<option ng-repeat="bowler_type in bowler_types" value="{{bowler_type.id}}">{{bowler_type.name}}</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-6">Batting Hand: <span class="red">*</span></label>
							<div class="col-md-6">
								<label class="radio-inline"><input type="radio" name="bat_hand" ng-model="data.bat_hand" ng-required="true" value="Right">Right</label>
								<label class="radio-inline"><input type="radio" name="bat_hand" ng-model="data.bat_hand" ng-required="true" value="Left">Left</label>
							</div>
						</div>

						<div class="form-group" ng-if="hasBowlingAbility()">
							<label class="control-label col-md-6">Bowling Hand: <span class="red">*</span></label>
							<div class="col-md-6">
								<label class="radio-inline"><input type="radio" name="bowl_hand" ng-model="data.bowl_hand" ng-required="true" value="Right">Right</label>
								<label class="radio-inline"><input type="radio" name="bowl_hand" ng-model="data.bowl_hand" ng-required="true" value="Left">Left</label>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-6">Modes: <span class="red">*</span></label>
							<div class="col-md-6">
								<label class="checkbox-inline"><input type="checkbox" ng-model="data.speciality.test" ng-required="!atLeastOneSelected(data.speciality)" value="Test">Test</label>
								<label class="checkbox-inline"><input type="checkbox" ng-model="data.speciality.odi" ng-required="!atLeastOneSelected(data.speciality)" value="ODI">ODI</label>
								<label class="checkbox-inline"><input type="checkbox" ng-model="data.speciality.t20" ng-required="!atLeastOneSelected(data.speciality)" value="T20">T20</label>
							</div>
						</div>	

						<div class="form-group">
							<label class="control-label col-md-6">&nbsp;</label>
							<div class="col-md-6">
								<label class="checkbox-inline" style="color:#ff0000;"><input type="checkbox" ng-model="data.is_private" value="1">Make this player private<br /> (won't show up in Community)</label>
							</div>
						</div>	

						<h3 class="form-subtitle">Rating Points</h3>						
						<div class="text-center">
							Use <strong><i class="fa fa-minus">&nbsp;</i></strong> or <strong><i class="fa fa-plus">&nbsp;</i></strong> to change the rating points of a player. Alternatively, you can also <strong>manually type in the value</strong> into the textbox.
						</div>
						<div class="text-right bot10">
							<span class="red">Available Rating Points: {{data.available_rp}}</span>
						</div>
						<div class="form-group">
							<label class="control-label col-md-6">Batting: <span class="red">*</span></label>
							<div class="col-md-6">
								<div class="input-group">
								  <span ng-click="minusRP($event)" data-type="batting" class="input-group-addon cursor" style="border-right: 1px solid #ccc;"><i class="fa fa-minus">&nbsp;</i></span>
								  <input type="text" class="form-control text-center" data-type="batting" ng-blur="checkRPValidity($event)" ng-change="recalculateRP()" ng-model="data.batting_rp" />
								  <span ng-hide="data.available_rp == 0" ng-click="plusRP($event)" data-type="batting" class="input-group-addon cursor"><i class="fa fa-plus">&nbsp;</i></span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-6">Bowling: <span class="red">*</span></label>
							<div class="col-md-6">
								<div class="input-group">
								  <span ng-click="minusRP($event)" data-type="bowling" class="input-group-addon cursor" style="border-right: 1px solid #ccc;"><i class="fa fa-minus">&nbsp;</i></span>
								  <input type="text" class="form-control text-center" data-type="bowling" ng-blur="checkRPValidity($event)" ng-change="recalculateRP($event)" ng-model="data.bowling_rp" />
								  <span ng-hide="data.available_rp == 0" ng-click="plusRP($event)" data-type="bowling" class="input-group-addon cursor"><i class="fa fa-plus">&nbsp;</i></span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-6">Fielding: <span class="red">*</span></label>
							<div class="col-md-6">
								<div class="input-group">
								  <span ng-click="minusRP($event)" data-type="fielding" class="input-group-addon cursor" style="border-right: 1px solid #ccc;"><i class="fa fa-minus">&nbsp;</i></span>
								  <input type="text" class="form-control text-center" data-type="fielding" ng-blur="checkRPValidity($event)" ng-change="recalculateRP($event)" ng-model="data.fielding_rp" />
								  <span ng-hide="data.available_rp == 0" ng-click="plusRP($event)" data-type="fielding" class="input-group-addon cursor"><i class="fa fa-plus">&nbsp;</i></span>
								</div>
							</div>
						</div>

						<div class="form-group text-center">												
							<button class="btn btn-primary" ng-disabled="add_new_player_form.$invalid" ng-click="showPlayerModal()">Add Player</button>
							<button class="btn btn-default" ng-click="randomizeRP($event)"><i class="fa fa-random">&nbsp;</i>Randomize Rating Points</button>
						</div>
					</form>
				</div>
			</div>
		</div>


		<div class="col-md-7" ng-controller="recentlyAddedPlayers">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Player List</div>
					<div class="box-helping-text">Your most recent ones.</div>
				</div>
				<div class="box-body">					
					<div class="table-mockup">
						<div class="thead">
							<div class="tr">
								<div class="th">Name</div>
								<div class="th">Country</div>								
								<div class="th">Added</div>
							</div>
						</div>
						<div class="tbody">
							<div class="tr" ng-if="players.length > 0" ng-repeat="player in players">
								<div class="td"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{player.icon}}" title="{{player.type}}" alt="{{player.type}}" /><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{player.ment_icon}}" title="{{player.ment_label}}" alt="{{player.ment_label}}" />{{player.name}} <label class="label label-danger label-private-small" ng-show="player.is_private == 1">Private</label></div>
								<div class="td text-center">{{player.country}}</div>								
								<div class="td text-center">{{player.created}}</div>
							</div>
							<div class="tr" ng-if="players.length == 0"><div class="td text-center">No records found</div></div>
						</div>
					</div>
					<div class="text-right"><a href="<?php echo site_url(); ?>/Players/ListPlayers"><i class="fa fa-search">&nbsp;</i>View All</a></div>
				</div>
			</div>	
		</div>
	</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/players.js?v=1"></script>