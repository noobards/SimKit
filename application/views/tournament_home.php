<div class="row site-breadcrumbs">

	<div class="col-xs-12">

		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> Tournament

	</div>

</div>

<div ng-controller="tournamentHome">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Create Tournament</div>
					<div class="box-helping-text">Start a brand new tournament.</div>
				</div>
				<div class="box-body box-body-max">
					<div class="extra-info text-right">Fields marked with <span class="red">*</span> are mandatory</div>
					<div class="form-horizontal">
						<div class="form-group">
							<label for="t_name" class="control-label col-md-4">Tournament Name <span class="red">*</span></label>
							<div class="col-md-8">
								<input type="text" class="form-control" id="t_name" ng-model="data.t_name" />
							</div>
						</div>
						<div class="form-group">
							<label for="t_not" class="control-label col-md-4">Number of Teams <span class="red">*</span></label>
							<div class="col-md-8">
								<input type="number" class="form-control" id="t_not" min="2" max="10" ng-model="data.t_not" />
								<span class="extra-info">Min 2, Max 10 teams</span>
							</div>
						</div>
						<div class="form-group">
							<label for="t_type" class="control-label col-md-4">Tournament Type <span class="red">*</span></label>
							<div class="col-md-8">								
								<select class="form-control" id="t_type" ng-change="setNoO()" ng-model="data.t_type">
									<option value="-1">Select One</option>
									<option value="ODI">ODI</option>
									<option value="T20">T20</option>
									<option value="CUSTOM">Custom</option>
								</select>
								<span class="extra-info">If you choose "Custom", you will need to specify the number of overs that each innings in a match will comprise of.</span>
							</div>
						</div>
						<div class="form-group" ng-show="data.t_type == 'CUSTOM'">
							<label for="t_noo" class="control-label col-md-4">Number of Overs <span class="red">*</span></label>
							<div class="col-md-8">
								<input type="number" class="form-control" id="t_noo" min="1" ng-model="data.t_noo" ng-change="calculatePPAndDeath()" />
								<span class="extra-info">Min 10, Max 50 overs</span>
							</div>
						</div>
						<div class="form-group" ng-show="data.t_type != '-1'">
							<label for="t_pp" class="control-label col-md-4">Powerplay Until</label>
							<div class="col-md-8">
								<div class="input-group">
									<input readonly="readonly" type="number" class="form-control" id="t_pp" min="1" max="50" ng-model="data.t_pp" />
									<span class="input-group-addon font-fix">overs</span>
								</div>
								<span class="extra-info">The number of overs upto which the Power Play is <strong>active</strong> (For ex: 10 overs in a 50-over match). This period encourages batsmen to play in a manner that will up the run rate.</span>
							</div>
						</div>
						<div class="form-group" ng-show="data.t_type != '-1'">
							<label for="t_do" class="control-label col-md-4">Death Overs From</label>
							<div class="col-md-8">
								<div class="input-group">
									<input readonly="readonly" type="number" class="form-control" id="t_do" min="1" max="50" ng-model="data.t_do" />
									<span class="input-group-addon font-fix">overs</span>
								</div>	
								<span class="extra-info">The number of overs after which death overs <strong>begin</strong> (For ex: 40 over onwards in a 50-over match). This period encourages batsmen to play in a manner that will up the run rate.</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4">&nbsp;</label>
							<div class="col-md-8">
								<button ng-click="goToStep2($event)" class="btn btn-danger">Proceed</button>
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/tournament_home.js"></script>