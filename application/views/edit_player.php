	<div class="row site-breadcrumbs">

		<div class="col-xs-12">

			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/Players">Players</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/Players/ListPlayers">My Players</a> <i class="fa fa-chevron-right">&nbsp;</i> Edit Player

		</div>

	</div>

	<?php if($has_permission == "NO")

	{	?>

	<div  class="row">

		<div class="col-md-12">

			<div class="alert alert-danger"><i class="fa fa-exclamation-triangle">&nbsp;</i>You do not have permission to edit this player. Please contact the developer for more details</div>

		</div>

	</div>

	<?php

	}

	else

	{	?>

		<div class="row">

			<div class="col-md-6" ng-controller="editPlayer">				



				<div class="box">

					<div class="box-title">

						<div class="box-main-text">{{player_name}}</div>

						<div class="box-helping-text">Update the player using the form below.</div>

					</div>

					<div class="box-body box-body-max">

						<form class="form-horizontal" name="edit_player_form" onsubmit="return false;" autocomplete="off" novalidate>

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

								<label for="player_type" class="control-label col-md-6">Player Role: </label>

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

							<h3 class="form-subtitle">Rating Points</h3>
							<div class="row bot10">
								<div class="col-xs-4">
									<div class="text-center"><strong>Batting</strong></div>
									<div class="c100 p{{data.batting_rp}} center">
								        <span>{{data.batting_rp}}</span>
								        <div class="slice">
								            <div class="bar"></div>
								            <div class="fill"></div>
								        </div>
								    </div>
								</div>
								<div class="col-xs-4">
									<div class="text-center"><strong>Bowling</strong></div>
									<div class="c100 p{{data.bowling_rp}} center">
								        <span>{{data.bowling_rp}}</span>
								        <div class="slice">
								            <div class="bar"></div>
								            <div class="fill"></div>
								        </div>
								    </div>
								</div>
								<div class="col-xs-4">
									<div class="text-center"><strong>Fielding</strong></div>
									<div class="c100 p{{data.fielding_rp}} center">
								        <span>{{data.fielding_rp}}</span>
								        <div class="slice">
								            <div class="bar"></div>
								            <div class="fill"></div>
								        </div>
								    </div>
								</div>
							</div>														

							<div class="form-group text-center top20">												

								<button class="btn btn-primary" ng-click="updatePlayer($event)" ng-disabled="edit_player_form.$invalid">Update</button>&nbsp;<a href="<?php echo site_url(); ?>/Players/ListPlayers" class="btn btn-danger">Go Back</a>

							</div>

							<input type="hidden" ng-model="data.player_id" />

							<input type="hidden" id="pid" value="<?php echo $pid; ?>" />

						</form>						

					</div>

				</div>

			</div>

		</div>

	<?php

	} // end of haspermission else part ?>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/edit_player.js"></script>