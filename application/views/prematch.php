<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> Pre-Match
	</div>
</div>
	
<?php
if($data['status'] == "OK")
{	
	date_default_timezone_set($this->session->timezone);
?>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="box" data-match="<?php echo $data['match']; ?>" data-home="<?php echo $data['home']; ?>" data-away="<?php echo $data['away']; ?>" data-home_label="<?php echo $data['home_label']; ?>" data-away_label="<?php echo $data['away_label']; ?>" ng-controller="simulateMatch">
			<div class="box-title">
				<div class="box-main-text">Pre-Match Settings</div>
				<div class="box-helping-text">The boring stuff...</div>
			</div>
			<div class="box-body box-body-max">
				<h2 class="sim_title"><span><?php echo $data['home_label']; ?></span><label class="label label-success" style="font-size:40%; vertical-align: middle; margin: 0 5px;">vs</label><span><?php echo $data['away_label']; ?></span></h2>
				<p class="sim_extra_info">at <strong><?php echo $data['ground']; ?></strong> on <?php echo date("M d, Y @ g:i a"); ?></p>
				<p class="sim_extra_info">Pitch is <strong><?php echo $data['pitch']; ?></strong></p>
				<p class="sim_extra_info">Match length is <strong><?php echo $data['overs']; ?> overs</strong> long</p>
				
				<div class="text-center">
					<button class="btn btn-danger" ng-click="flipCoin($event)">Coin Toss</button>
					<img ng-show="coin_tossed" class="coin_toss" src="<?php echo base_url(); ?>assets/images/coin.png" alt="Toss" />
				</div>
				
				<div class="toss_result top10" ng-if="data.decision_made">
					<div class="alert alert-warning text-center mb0"><i class="fa fa-bolt">&nbsp;</i><strong>{{data.toss_win}}</strong> have won the toss and elected to <strong>{{data.toss_decision}}</strong> first.</div>
					<p class="text-center top10">Select the bowling order of the home and away teams</p>
					<div class="row">
						<div class="col-md-6">
							<div class="alert alert-info text-center mb0"><?php echo $data['home_label']; ?></div>
							<div class="table-mockup">
								<div class="thead">
									<div class="tr">
										<div class="th">Name</div>
										<div class="th">Order</div>
									</div>
								</div>	
								<div class="tbody">
									<div class="tr" ng-repeat="p in data.home_bowlers">
										<div class="td">
											<img ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" class="role-icon" alt="{{p.role}}" title="{{p.role}}" />{{p.name}}
										</div>	
										<div class="td text-center">
											<select data-pid="{{p.player_id}}" data-pname="{{p.name}}" ng-model="data.home_order[p.player_id]" class="bowling_order">
												<option value="">Bowling Order</option>
												<option value="{{($index + 1)}}" ng-repeat="b in data.home_bowlers">{{($index + 1)}}</option>
											</select>
										</div>
									</div>
								</div>
							</div>							
						</div>
						<div class="col-md-6">
							<div class="alert alert-info text-center mb0"><?php echo $data['away_label']; ?></div>
							<div class="table-mockup">
								<div class="thead">
									<div class="tr">
										<div class="th">Name</div>
										<div class="th">Order</div>
									</div>
								</div>	
								<div class="tbody">
									<div class="tr" ng-repeat="p in data.away_bowlers">
										<div class="td">
											<img ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" class="role-icon" alt="{{p.role}}" title="{{p.role}}" />{{p.name}}
										</div>	
										<div class="td text-center">
											<select data-pid="{{p.player_id}}" data-pname="{{p.name}}" ng-model="data.away_order[p.player_id]" class="bowling_order">
												<option value="">Bowling Order</option>
												<option value="{{($index + 1)}}" ng-repeat="b in data.away_bowlers">{{($index + 1)}}</option>
											</select>
										</div>
									</div>
								</div>
							</div>							
						</div>
					</div>
					<div class="text-center top10">
						<button class="btn btn-danger" ng-click="saveBowlingOrder($event)"><i class="fa fa-rocket">&nbsp;</i>Begin Match</button>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>





<?php	
}
else
{	?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger"><i class="fa fa-warning">&nbsp;</i><?php echo $data['msg']; ?></div>
		</div>
	</div>
<?php	
}
?>


	



<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/prematch.js"></script>